<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Distribuicao;
use App\Models\DistribuicaoItem;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DistribuicaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $distribuicoes = Distribuicao::with(['beneficiario', 'items'])
            ->where('paroquia_id', $user->paroquia_id)
            ->latest()
            ->paginate(15);

        return view('admin.distribuicoes.list', compact('distribuicoes'));
    }

    public function create()
    {
        $user = Auth::user();

        $beneficiarios = Beneficiario::orderBy('nome')->get();
        $estoques = Estoque::with(['item', 'distribuicaoItems.distribuicao'])
            ->where('paroquia_id', $user->paroquia_id)
            ->orderBy('item_id')
            ->orderBy('unidade')
            ->get()
            ->sortBy(fn (Estoque $estoque) => $estoque->item->nome)
            ->values();

        return view('admin.distribuicoes.create', compact('beneficiarios', 'estoques'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'beneficiario_id' => ['required', 'exists:beneficiarios,id'],
            'observacoes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
        ]);

        $itensSelecionados = $this->extrairItensDaRequisicao($request->input('items', []));

        $distribuicao = null;

        DB::transaction(function () use ($user, $validated, $itensSelecionados, &$distribuicao) {
            $paroquiaId = $user->paroquia_id;

            $distribuicao = Distribuicao::create([
                'paroquia_id' => $paroquiaId,
                'beneficiario_id' => $validated['beneficiario_id'],
                'status' => Distribuicao::STATUS_RESERVADO,
                'observacoes' => Arr::get($validated, 'observacoes'),
                'reservado_em' => now(),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            foreach ($itensSelecionados as $item) {
                $estoque = Estoque::where('id', $item['estoque_id'])
                    ->where('paroquia_id', $paroquiaId)
                    ->lockForUpdate()
                    ->first();

                if (!$estoque) {
                    throw ValidationException::withMessages([
                        'items' => 'Um dos itens selecionados não está disponível no estoque da paróquia.',
                    ]);
                }

                $disponivel = $this->calcularDisponibilidade($estoque, $distribuicao->id);

                if ($disponivel < $item['quantidade']) {
                    throw ValidationException::withMessages([
                        "items.{$estoque->id}.quantidade" => sprintf(
                            'Quantidade solicitada (%.2f %s) excede o disponível (%.2f %s).',
                            $item['quantidade'],
                            $estoque->unidade,
                            $disponivel,
                            $estoque->unidade
                        ),
                    ]);
                }

                DistribuicaoItem::create([
                    'distribuicao_id' => $distribuicao->id,
                    'estoque_id' => $estoque->id,
                    'item_id' => $estoque->item_id,
                    'unidade' => $estoque->unidade,
                    'quantidade' => $item['quantidade'],
                    'quantidade_reservada' => $item['quantidade'],
                ]);
            }
        });

        $distribuicao->load('items', 'beneficiario');

        activity('Distribuições')
            ->performedOn($distribuicao)
            ->causedBy($user)
            ->withProperties([
                'status' => $distribuicao->status,
                'itens' => $distribuicao->items->map(function (Item $item) {
                    return [
                        'item_id' => $item->id,
                        'nome' => $item->nome,
                        'quantidade' => $item->pivot->quantidade,
                        'unidade' => $item->pivot->unidade,
                    ];
                })->values(),
            ])
            ->log('Distribuição criada e itens reservados.');

        return redirect()->route('distribuicoes.show', $distribuicao)
            ->with('success', 'Distribuição registrada e itens reservados com sucesso.');
    }

    public function show(Distribuicao $distribuicao)
    {
        $user = Auth::user();
        $this->garantirParoquia($distribuicao, $user->paroquia_id);

        $distribuicao->load(['beneficiario', 'items']);

        $statusDisponiveis = $this->statusDisponiveis($distribuicao);

        return view('admin.distribuicoes.show', compact('distribuicao', 'statusDisponiveis'));
    }

    public function update(Request $request, Distribuicao $distribuicao): RedirectResponse
    {
        $user = Auth::user();
        $this->garantirParoquia($distribuicao, $user->paroquia_id);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Distribuicao::STATUS_RESERVADO,
                Distribuicao::STATUS_ENVIADO,
                Distribuicao::STATUS_ENTREGUE,
            ])],
            'observacoes' => ['nullable', 'string'],
        ]);

        $novoStatus = $validated['status'];
        $statusAtual = $distribuicao->status;

        if (!in_array($novoStatus, $this->statusDisponiveis($distribuicao), true)) {
            throw ValidationException::withMessages([
                'status' => 'Transição de status inválida para esta distribuição.',
            ]);
        }

        DB::transaction(function () use ($novoStatus, $statusAtual, $distribuicao, $validated, $user) {
            $distribuicao->observacoes = Arr::get($validated, 'observacoes');

            if ($novoStatus !== $statusAtual) {
                if (in_array($novoStatus, [Distribuicao::STATUS_ENVIADO, Distribuicao::STATUS_ENTREGUE], true)
                    && !$distribuicao->estoque_debitado_em) {
                    $this->debitarEstoque($distribuicao, $user, $novoStatus);
                }

                if ($novoStatus === Distribuicao::STATUS_ENVIADO && !$distribuicao->enviado_em) {
                    $distribuicao->enviado_em = now();
                }

                if ($novoStatus === Distribuicao::STATUS_ENTREGUE) {
                    if (!$distribuicao->enviado_em) {
                        $distribuicao->enviado_em = now();
                    }
                    $distribuicao->entregue_em = now();
                }

                $distribuicao->status = $novoStatus;
            }

            $distribuicao->updated_by = $user->id;
            $distribuicao->save();
        });

        $distribuicao->refresh()->load('items', 'beneficiario');

        if ($statusAtual !== $novoStatus) {
            $mensagem = $novoStatus === Distribuicao::STATUS_ENTREGUE
                ? 'Distribuição entregue ao beneficiário.'
                : ($novoStatus === Distribuicao::STATUS_ENVIADO
                    ? 'Distribuição enviada ao beneficiário.'
                    : 'Distribuição atualizada.');

            activity('Distribuições')
                ->performedOn($distribuicao)
                ->causedBy($user)
                ->withProperties([
                    'status' => $novoStatus,
                    'itens' => $distribuicao->items->map(function (Item $item) {
                        return [
                            'item_id' => $item->id,
                            'nome' => $item->nome,
                            'quantidade' => $item->pivot->quantidade,
                            'unidade' => $item->pivot->unidade,
                        ];
                    })->values(),
                ])
                ->log($mensagem);
        }

        return redirect()->route('distribuicoes.show', $distribuicao)
            ->with('success', 'Distribuição atualizada com sucesso.');
    }

    public function report(Request $request)
    {
        $user = Auth::user();
        $filtros = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'item_id' => ['nullable', 'exists:items,id'],
            'beneficiario_id' => ['nullable', 'exists:beneficiarios,id'],
        ]);

        $dadosRelatorio = $this->montarConsultaRelatorio($filtros, $user->paroquia_id)->get();

        $items = Item::orderBy('nome')->get();
        $beneficiarios = Beneficiario::orderBy('nome')->get();

        return view('admin.distribuicoes.relatorios', [
            'dadosRelatorio' => $dadosRelatorio,
            'items' => $items,
            'beneficiarios' => $beneficiarios,
            'filtros' => $filtros,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $filtros = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'item_id' => ['nullable', 'exists:items,id'],
            'beneficiario_id' => ['nullable', 'exists:beneficiarios,id'],
        ]);

        $linhas = $this->montarConsultaRelatorio($filtros, $user->paroquia_id)
            ->orderBy('beneficiarios.nome')
            ->orderBy('items.nome')
            ->get();

        $nomeArquivo = 'relatorio-distribuicoes-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($linhas, $filtros) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Beneficiário', 'Item', 'Unidade', 'Total Distribuído', 'Distribuições']);

            foreach ($linhas as $linha) {
                fputcsv($handle, [
                    $linha->beneficiario_nome,
                    $linha->item_nome,
                    $linha->unidade,
                    number_format($linha->total_quantidade, 2, ',', '.'),
                    $linha->total_distribuicoes,
                ]);
            }

            fclose($handle);
        }, $nomeArquivo, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function debitarEstoque(Distribuicao $distribuicao, User $user, string $status): void
    {
        $distribuicao->loadMissing('items');

        foreach ($distribuicao->items as $item) {
            $pivot = $item->pivot;

            $estoque = Estoque::where('id', $pivot->estoque_id)
                ->where('paroquia_id', $distribuicao->paroquia_id)
                ->lockForUpdate()
                ->first();

            if (!$estoque) {
                throw ValidationException::withMessages([
                    'items' => 'Não foi possível localizar o estoque vinculado a esta distribuição.',
                ]);
            }

            if ($pivot->quantidade_reservada > $estoque->quantidade) {
                throw ValidationException::withMessages([
                    'items' => sprintf(
                        'O estoque disponível para %s é insuficiente para concluir a distribuição.',
                        $item->nome
                    ),
                ]);
            }

            $estoque->quantidade = round($estoque->quantidade - $pivot->quantidade_reservada, 3);
            $estoque->save();

            EstoqueMovimentacao::create([
                'estoque_id' => $estoque->id,
                'paroquia_id' => $estoque->paroquia_id,
                'item_id' => $estoque->item_id,
                'distribuicao_id' => $distribuicao->id,
                'user_id' => $user->id,
                'tipo' => 'saida',
                'quantidade' => $pivot->quantidade_reservada,
                'unidade' => $pivot->unidade,
                'motivo' => $status === Distribuicao::STATUS_ENTREGUE
                    ? 'Distribuição entregue'
                    : 'Distribuição enviada',
            ]);
        }

        $distribuicao->estoque_debitado_em = now();
    }

    protected function calcularDisponibilidade(Estoque $estoque, int $excluirDistribuicao = 0): float
    {
        $reservado = DistribuicaoItem::where('estoque_id', $estoque->id)
            ->whereHas('distribuicao', function ($query) use ($estoque, $excluirDistribuicao) {
                $query->where('paroquia_id', $estoque->paroquia_id)
                    ->where('status', Distribuicao::STATUS_RESERVADO)
                    ->whereNull('estoque_debitado_em');

                if ($excluirDistribuicao) {
                    $query->where('id', '!=', $excluirDistribuicao);
                }
            })
            ->sum('quantidade_reservada');

        return round(max(0, $estoque->quantidade - $reservado), 3);
    }

    protected function garantirParoquia(Distribuicao $distribuicao, int $paroquiaId): void
    {
        if ($distribuicao->paroquia_id !== $paroquiaId) {
            abort(404);
        }
    }

    protected function statusDisponiveis(Distribuicao $distribuicao): array
    {
        return match ($distribuicao->status) {
            Distribuicao::STATUS_RESERVADO => [
                Distribuicao::STATUS_RESERVADO,
                Distribuicao::STATUS_ENVIADO,
                Distribuicao::STATUS_ENTREGUE,
            ],
            Distribuicao::STATUS_ENVIADO => [
                Distribuicao::STATUS_ENVIADO,
                Distribuicao::STATUS_ENTREGUE,
            ],
            default => [Distribuicao::STATUS_ENTREGUE],
        };
    }

    protected function extrairItensDaRequisicao(array $items): Collection
    {
        $itensFormatados = collect();
        $erros = [];

        foreach ($items as $estoqueId => $dados) {
            $quantidade = Arr::get($dados, 'quantidade');

            if ($quantidade === null || $quantidade === '') {
                continue;
            }

            if (!is_numeric($quantidade) || (float) $quantidade <= 0) {
                $erros["items.{$estoqueId}.quantidade"] = 'Informe uma quantidade válida para o item selecionado.';
                continue;
            }

            $itensFormatados->push([
                'estoque_id' => (int) $estoqueId,
                'quantidade' => round((float) $quantidade, 3),
            ]);
        }

        if ($itensFormatados->isEmpty()) {
            $erros['items'] = 'Selecione ao menos um item para distribuir.';
        }

        if (!empty($erros)) {
            throw ValidationException::withMessages($erros);
        }

        return $itensFormatados;
    }

    protected function montarConsultaRelatorio(array $filtros, int $paroquiaId)
    {
        $query = DistribuicaoItem::query()
            ->selectRaw(
                'beneficiarios.nome as beneficiario_nome, items.nome as item_nome, distribuicao_item.unidade, ' .
                'SUM(distribuicao_item.quantidade) as total_quantidade, COUNT(DISTINCT distribuicoes.id) as total_distribuicoes'
            )
            ->join('distribuicoes', 'distribuicao_item.distribuicao_id', '=', 'distribuicoes.id')
            ->join('items', 'distribuicao_item.item_id', '=', 'items.id')
            ->join('beneficiarios', 'distribuicoes.beneficiario_id', '=', 'beneficiarios.id')
            ->where('distribuicoes.paroquia_id', $paroquiaId)
            ->whereNotNull('distribuicoes.estoque_debitado_em')
            ->whereIn('distribuicoes.status', [
                Distribuicao::STATUS_ENVIADO,
                Distribuicao::STATUS_ENTREGUE,
            ]);

        if (!empty($filtros['item_id'])) {
            $query->where('distribuicao_item.item_id', $filtros['item_id']);
        }

        if (!empty($filtros['beneficiario_id'])) {
            $query->where('distribuicoes.beneficiario_id', $filtros['beneficiario_id']);
        }

        if (!empty($filtros['data_inicio'])) {
            $query->whereDate(DB::raw('coalesce(distribuicoes.entregue_em, distribuicoes.enviado_em)'), '>=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query->whereDate(DB::raw('coalesce(distribuicoes.entregue_em, distribuicoes.enviado_em)'), '<=', $filtros['data_fim']);
        }

        $query->orderBy('beneficiarios.nome')
            ->orderBy('items.nome');

        return $query->groupBy(
            'beneficiarios.nome',
            'items.nome',
            'distribuicao_item.unidade',
            'distribuicoes.beneficiario_id',
            'distribuicao_item.item_id'
        );
    }
}