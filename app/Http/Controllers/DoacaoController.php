<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DoacaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $doacoes = Doacao::where('paroquia_id', $user->paroquia_id)
            ->with('doador')
            ->latest('data_doacao')->get();

        return view('admin.doacoes.list', compact('doacoes'));
    }

    public function create()
    {
        $doadores = Doador::all();
        $itens = Item::orderBy('nome')->get();
        $itensData = $itens->map(function ($item) {
            return [
                'id' => $item->id,
                'nome' => $item->nome,
                'categoria' => $item->categoria,
            ];
        })->values();

        return view('admin.doacoes.create', compact('doadores', 'itens', 'itensData'));
    }

    public function store(Request $request)
    {
        $request->merge(['doacao_anonima' => $request->has('doacao_anonima')]);

        $validatedData = $request->validate([
            'data_doacao' => 'required|date',
            'doador_id' => 'nullable|exists:doadores,id',
            'doacao_anonima' => 'nullable|boolean',
            'tipo' => 'required|in:dinheiro,item',
            'descricao' => 'nullable|string',
        ]);

        $doadorId = $request->input('doador_id');
        $isAnonima = $request->input('doacao_anonima');
        if ($isAnonima) {
            if ($request->input('tipo') !== 'dinheiro') {
                return back()->withErrors(['tipo' => 'Doações anônimas só podem ser do tipo Dinheiro.'])->withInput();
            }
            $doadorAnonimo = Doador::firstOrCreate(
                ['nome' => 'Anônimo'],
                [
                    'cpf_cnpj' => null,
                    'telefone' => null,
                    'logradouro' => null,
                    'cep' => null,
                    'numero' => null,
                    'cidade' => null,
                    'estado' => null
                ]
            );
            $doadorId = $doadorAnonimo->id;
        } elseif (empty($doadorId)) {
            return back()->withErrors(['doador_id' => 'Selecione um doador ou marque a opção "Doação Anônima".'])->withInput();
        }


        $doacao = new Doacao();
        $doacao->data_doacao = $validatedData['data_doacao'];
        $doacao->tipo = $validatedData['tipo'];
        $doacao->descricao = $validatedData['descricao'];
        $paroquiaId = Auth::user()->paroquia_id;
        $userId = Auth::id();
        $doacao->paroquia_id = $paroquiaId;
        $doacao->doador_id = $doadorId;

        if ($request->tipo == 'dinheiro') {
            $request->validate(['quantidade' => 'required|numeric|min:0.01']);
            DB::transaction(function () use ($doacao, $request) {
                $doacao->quantidade = (float) $request->quantidade;
                $doacao->unidade = 'R$';
                $doacao->save();
            });
        } elseif ($request->tipo == 'item') {
            $request->validate([
                'itens' => 'required|array|min:1',
                'itens.*.quantidade' => 'required|numeric|min:0.01',
                'itens.*.unidade' => 'required|in:Unidade,Kg',
                'itens.*.item_id' => 'required',
                'itens.*.new_item_name' => 'nullable|required_if:itens.*.item_id,new|string|max:255',
                'itens.*.new_item_category' => 'sometimes|nullable|required_if:itens.*.item_id,new|in:alimento,outro',
            ], [
                'itens.required' => 'Adicione pelo menos um item à doação.',
                'itens.min' => 'Adicione pelo menos um item à doação.',
                'itens.*.new_item_name.required_if' => 'O nome do novo item é obrigatório.',
                'itens.*.new_item_category.required_if' => 'A categoria do novo item é obrigatória.',
                'itens.*.new_item_category.in' => 'Selecione uma categoria válida para o item.',
            ]);

            $doacao->quantidade = null;
            $doacao->unidade = null;

            $itensCollection = collect($request->input('itens', []));
            $existingItemIds = $itensCollection
                ->pluck('item_id')
                ->filter(fn($id) => is_numeric($id))
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $existingitens = Item::whereIn('id', $existingItemIds)->get()->keyBy('id');

            $unitErrors = [];
            foreach ($itensCollection as $index => $itemData) {
                $unidade = $itemData['unidade'] ?? null;
                if (($itemData['item_id'] ?? null) === 'new') {
                    $category = $itemData['new_item_category'] ?? null;
                    if ($category === 'alimento' && $unidade !== 'Kg') {
                        $unitErrors["itens.$index.unidade"] = 'Itens da categoria "Alimento" devem ser cadastrados em Kg.';
                    }
                } elseif (is_numeric($itemData['item_id'])) {
                    $existingItem = $existingitens->get((int) $itemData['item_id']);
                    if ($existingItem && $existingItem->categoria === 'alimento' && $unidade !== 'Kg') {
                        $unitErrors["itens.$index.unidade"] = 'Itens da categoria "Alimento" devem ser cadastrados em Kg.';
                    }
                }
            }

            if (!empty($unitErrors)) {
                return back()->withErrors($unitErrors)->withInput();
            }

            $itensToAttach = [];
            foreach ($request->itens as $itemData) {
                $itemId = null;
                if ($itemData['item_id'] == 'new' && !empty($itemData['new_item_name'])) {
                    $itemName = trim($itemData['new_item_name']);
                    $newItemCategory = $itemData['new_item_category'] ?? null;
                    $newItem = Item::firstOrCreate(
                        ['nome' => $itemName],
                        $newItemCategory ? ['categoria' => $newItemCategory] : []
                    );
                    if (!$newItem->wasRecentlyCreated && $newItemCategory && empty($newItem->categoria)) {
                        $newItem->update(['categoria' => $newItemCategory]);
                    }
                    $itemId = $newItem->id;
                } elseif (is_numeric($itemData['item_id'])) {
                    $itemId = $itemData['item_id'];
                }

                if ($itemId) {
                    $itensToAttach[$itemId] = [
                        'quantidade' => round((float) $itemData['quantidade'], 3),
                        'unidade' => $itemData['unidade']
                    ];
                }
            }

            if (!empty($itensToAttach)) {
                DB::transaction(function () use ($doacao, $itensToAttach, $paroquiaId, $userId) {
                    $doacao->quantidade = null;
                    $doacao->unidade = null;
                    $doacao->save();

                    $doacao->itens()->attach($itensToAttach);

                    foreach ($itensToAttach as $itemId => $itemData) {
                        $estoque = Estoque::firstOrNew([
                            'paroquia_id' => $paroquiaId,
                            'item_id' => $itemId,
                            'unidade' => $itemData['unidade'],
                        ]);

                        $estoque->quantidade = round(($estoque->quantidade ?? 0) + $itemData['quantidade'], 3);
                        $estoque->save();

                        \App\Models\EstoqueMovimentacao::create([
                            'estoque_id' => $estoque->id,
                            'paroquia_id' => $paroquiaId,
                            'item_id' => $itemId,
                            'doacao_id' => $doacao->id,
                            'user_id' => $userId,
                            'tipo' => 'entrada',
                            'quantidade' => $itemData['quantidade'],
                            'unidade' => $itemData['unidade'],
                            'motivo' => 'Doação recebida',
                        ]);
                    }
                });
            } else {
                return back()->withErrors(['itens' => 'Não foi possível processar os itens. Verifique os dados.'])->withInput();
            }
        } else {
            return back()->withErrors(['tipo' => 'Tipo de doação inválido.'])->withInput();
        }

        return redirect()->route('doacoes.index')->with('success', 'Doação registrada com sucesso!');
    }

    public function show($id)
    {
        $user = Auth::user();

        $doacaoQuery = Doacao::with(['doador', 'itens']);

        if (!$user->is_admin) {
            $doacaoQuery->where('paroquia_id', $user->paroquia_id);
        }

        $doacao = $doacaoQuery->findOrFail($id);

        $resumoItens = [];

        if ($doacao->tipo === 'item') {
            $formatarQuantidade = function (float $valor, string $unidade): string {
                $casasDecimais = $unidade === 'Kg' ? 3 : 0;
                $formatado = number_format($valor, $casasDecimais, ',', '.');

                if ($casasDecimais > 0) {
                    $formatado = rtrim(rtrim($formatado, '0'), ',');
                }

                return $formatado;
            };

            $doacao->itens->each(function ($item) use ($formatarQuantidade) {
                $item->formatted_quantidade = $formatarQuantidade(
                    (float) $item->pivot->quantidade,
                    $item->pivot->unidade
                );
            });

            $resumoItens = $doacao->itens
                ->groupBy(fn($item) => $item->pivot->unidade)
                ->mapWithKeys(function ($grupo, $unidade) use ($formatarQuantidade) {
                    $total = $grupo->sum(fn($item) => (float) $item->pivot->quantidade);

                    return [$unidade => $formatarQuantidade($total, $unidade)];
                })
                ->toArray();
        }

        $logs = Activity::with('causer:id,name')
            ->where('log_name', 'Doações')
            ->where('subject_type', Doacao::class)
            ->where('subject_id', $doacao->id)
            ->latest()
            ->get();

        return view('admin.doacoes.show', compact('doacao', 'logs'));
    }

    public function edit($id)
    {
        $doacao = Doacao::with(['doador', 'itens'])->findOrFail($id);
        $itens = Item::select('id', 'nome', 'categoria')->orderBy('nome')->get();
        if ($doacao->tipo !== 'item') {
            return redirect()->route('doacoes.show', $id)->with('error', 'Edição disponível apenas para doações de item.');
        }
        return view('admin.doacoes.edit', [
            'doacao' => $doacao,
            'itens' => $itens,
        ]);
    }

    public function update(Request $request, $id)
    {
        $doacao = Doacao::with(['itens'])->findOrFail($id);
        if ($doacao->tipo !== 'item') {
            return redirect()->route('doacoes.show', $id)->with('error', 'Edição disponível apenas para doações de item.');
        }

        $dados = $request->validate([
            'itens' => ['array'],
            'itens.*.item_id' => ['nullable'],
            'itens.*.new_item_name' => ['nullable', 'string', 'max:255'],
            'itens.*.new_item_category' => ['nullable', 'in:alimento,outro'],
            'itens.*.quantidade' => ['required', 'numeric', 'gt:0'],
            'itens.*.unidade' => ['required', 'in:Kg,Unidade'],
            'descricao' => ['nullable','string'],
        ]);

        $sync = [];
        $itensReq = collect($dados['itens'] ?? []);

        foreach ($itensReq as $row) {
            $isNew = isset($row['item_id']) && $row['item_id'] === 'new';
            if ($isNew) {
                if (empty($row['new_item_name']) || empty($row['new_item_category'])) {
                    return back()->withErrors(['itens' => 'Dados do novo item incompletos.'])->withInput();
                }
                if ($row['new_item_category'] === 'alimento' && $row['unidade'] !== 'Kg') {
                    return back()->withErrors(['itens' => 'Itens da categoria Alimento devem usar unidade Kg.'])->withInput();
                }
                $novo = \App\Models\Item::firstOrCreate(
                    ['nome' => $row['new_item_name']],
                    ['categoria' => $row['new_item_category']]
                );
                $itemId = $novo->id;
                $categoria = $row['new_item_category'];
            } else {
                if (empty($row['item_id'])) {
                    return back()->withErrors(['itens' => 'Item inválido.'])->withInput();
                }
                $item = \App\Models\Item::select('id', 'categoria')->findOrFail($row['item_id']);
                $itemId = $item->id;
                $categoria = $item->categoria ?? 'outro';
                if ($categoria === 'alimento' && $row['unidade'] !== 'Kg') {
                    return back()->withErrors(['itens' => 'Itens da categoria Alimento devem usar unidade Kg.'])->withInput();
                }
            }

            $sync[$itemId] = [
                'quantidade' => $row['quantidade'],
                'unidade' => $row['unidade'],
            ];
        }

        $doacao->descricao = $request->input('descricao');

        DB::transaction(function () use ($doacao, $sync) {
            $doacao->save();
            $paroquiaId = $doacao->paroquia_id;
            $userId = Auth::id();

            $antes = $doacao->itens->mapWithKeys(function ($item) {
                $k = $item->id . '|' . $item->pivot->unidade;
                return [$k => (float) $item->pivot->quantidade];
            });

            $doacao->itens()->sync($sync);

            $depois = $doacao->itens()->get()->mapWithKeys(function ($item) {
                $k = $item->id . '|' . $item->pivot->unidade;
                return [$k => (float) $item->pivot->quantidade];
            });

            $chaves = $antes->keys()->merge($depois->keys())->unique();

            $idsParaNomes = $chaves->map(function ($k) {
                [$itemId, $u] = explode('|', $k);
                return (int) $itemId;
            })->unique()->values();

            $nomes = \App\Models\Item::whereIn('id', $idsParaNomes)->pluck('nome', 'id');

            $alteracoes = [];

            foreach ($chaves as $k) {
                [$itemId, $unidade] = explode('|', $k);
                $old = (float) ($antes[$k] ?? 0);
                $new = (float) ($depois[$k] ?? 0);
                $delta = round($new - $old, 3);
                if ($delta == 0.0) {
                    continue;
                }

                $estoque = Estoque::firstOrNew([
                    'paroquia_id' => $paroquiaId,
                    'item_id' => (int) $itemId,
                    'unidade' => $unidade,
                ]);

                $estoque->quantidade = round((float) ($estoque->quantidade ?? 0) + $delta, 3);
                $estoque->save();

                EstoqueMovimentacao::create([
                    'estoque_id' => $estoque->id,
                    'paroquia_id' => $paroquiaId,
                    'item_id' => (int) $itemId,
                    'doacao_id' => $doacao->id,
                    'user_id' => $userId,
                    'tipo' => $delta > 0 ? 'entrada' : 'saida',
                    'quantidade' => abs($delta),
                    'unidade' => $unidade,
                    'motivo' => 'Ajuste de Doação (ID: ' . $doacao->id . ')',
                ]);

                $acao = $old == 0 && $new > 0 ? 'adicionado' : ($new == 0 && $old > 0 ? 'removido' : 'alterado');

                $alteracoes[] = [
                    'item_id' => (int) $itemId,
                    'nome' => (string) ($nomes[(int) $itemId] ?? ''),
                    'unidade' => $unidade,
                    'anterior' => $old,
                    'atual' => $new,
                    'delta' => $delta,
                    'acao' => $acao,
                ];
            }

            $propsChanges = [
                'old' => ['itens' => $antes->toArray()],
                'attributes' => ['itens' => $depois->toArray()],
                'extra' => ['alteracoes' => array_values($alteracoes)],
            ];

            if (!empty($alteracoes)) {
                activity()->useLog('Doações')
                    ->event('updated')
                    ->performedOn($doacao)
                    ->causedBy(Auth::user())
                    ->withProperties($propsChanges)
                    ->log('Doação editada');
            } else {
                activity()->useLog('Doações')
                    ->event('updated')
                    ->performedOn($doacao)
                    ->causedBy(Auth::user())
                    ->withProperties($propsChanges)
                    ->log('Doação editada sem alteração de itens');
            }
        });

        return redirect()->route('doacoes.show', $doacao->id)->with('success', 'Doação atualizada com sucesso.');
    }

    public function destroy(Doacao $doacao)
    {
        $user = Auth::user();
        if (!$user->is_admin && $doacao->paroquia_id != $user->paroquia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        try {
            DB::transaction(function () use ($doacao, $user) {
                $paroquiaId = $doacao->paroquia_id;
                $userId = $user->id;

                if ($doacao->tipo == 'item') {
                    $itens = $doacao->itens()->get();
                    if ($itens->count() > 0) {
                        foreach ($itens as $item) {
                            $estoque = Estoque::firstOrCreate(
                                [
                                    'paroquia_id' => $paroquiaId,
                                    'item_id' => $item->id,
                                    'unidade' => $item->pivot->unidade,
                                ]
                            );

                            $estoque->quantidade = round(floatval($estoque->quantidade) - floatval($item->pivot->quantidade), 3);
                            $estoque->save();

                            EstoqueMovimentacao::create([
                                'estoque_id' => $estoque->id,
                                'paroquia_id' => $paroquiaId,
                                'item_id' => $item->id,
                                'doacao_id' => $doacao->id,
                                'user_id' => $userId,
                                'tipo' => 'saida',
                                'quantidade' => $item->pivot->quantidade,
                                'unidade' => $item->pivot->unidade,
                                'motivo' => 'Exclusão de Doação (ID: ' . $doacao->id . ')',
                            ]);
                        }

                        $doacao->itens()->detach();
                    }
                }

                $doacao->delete();

            });

        } catch (\Exception $e) {
            return redirect()->route('doacoes.index')->with('error', 'Erro ao excluir a doação: ' . $e->getMessage());
        }

        return redirect()->route('doacoes.index')->with('success', 'Doação excluída com sucesso!');
    }

    public function report(Request $request)
    {
        $user = Auth::user();
        $filtros = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'doador_id' => ['nullable', 'exists:doadores,id'],
            'tipo' => ['nullable', 'in:dinheiro,item'],
        ]);

        $dadosRelatorio = $this->montarConsultaRelatorio($filtros, $user->paroquia_id)->paginate(20);
        $doadores = Doador::orderBy('nome')->get();

        return view('admin.doacoes.relatorios', [
            'dadosRelatorio' => $dadosRelatorio,
            'doadores' => $doadores,
            'filtros' => $filtros,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $filtros = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'doador_id' => ['nullable', 'exists:doadores,id'],
            'tipo' => ['nullable', 'in:dinheiro,item'],
        ]);

        $linhas = $this->montarConsultaRelatorio($filtros, $user->paroquia_id)->get();
        $nomeArquivo = 'relatorio-doacoes-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($linhas) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Data', 'Doador', 'Tipo', 'Valor (R$)', 'Itens (Qtd)', 'Unidade']);

            foreach ($linhas as $linha) {

                $quantidadeItens = 'N/A';
                $unidadeItens = 'N/A';

                if ($linha->tipo === 'item') {
                    $quantidade = (float) $linha->total_itens;
                    $unidade = $linha->unidade_itens;
                    $casasDecimais = ($unidade === 'Kg') ? 3 : 0;
                    $valorFormatado = number_format($quantidade, $casasDecimais, ',', '.');

                    if ($casasDecimais > 0) {
                        $valorFormatado = rtrim(rtrim($valorFormatado, '0'), ',');
                    }

                    $quantidadeItens = $valorFormatado;
                    $unidadeItens = $unidade;
                    if ($unidade === 'Unidade' && $quantidade != 1.0) {
                        $unidadeItens = 'Unidades';
                    }
                }

                fputcsv($handle, [
                    \Carbon\Carbon::parse($linha->data_doacao)->format('d/m/Y'),
                    $linha->doador->nome,
                    $linha->tipo,
                    $linha->tipo === 'dinheiro' ? number_format($linha->quantidade, 2, ',', '.') : 'N/A',
                    $quantidadeItens,
                    $unidadeItens,
                ]);
            }

            fclose($handle);
        }, $nomeArquivo, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function montarConsultaRelatorio(array $filtros, int $paroquiaId)
    {
        $query = Doacao::with(['doador'])
            ->where('paroquia_id', $paroquiaId)
            ->select('doacoes.*')
            ->selectRaw('SUM(doacao_item.quantidade) as total_itens')
            ->selectRaw('doacao_item.unidade as unidade_itens')
            ->leftJoin('doacao_item', 'doacoes.id', '=', 'doacao_item.doacao_id');

        if (!empty($filtros['doador_id'])) {
            $query->where('doador_id', $filtros['doador_id']);
        }

        if (!empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (!empty($filtros['data_inicio'])) {
            $query->whereDate('data_doacao', '>=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query->whereDate('data_doacao', '<=', $filtros['data_fim']);
        }

        $query->orderBy('data_doacao', 'desc');

        $query->groupBy(
            'doacoes.id',
            'doacoes.paroquia_id',
            'doacoes.doador_id',
            'doacoes.tipo',
            'doacoes.quantidade',
            'doacoes.unidade',
            'doacoes.descricao',
            'doacoes.data_doacao',
            'doacoes.created_at',
            'doacoes.updated_at',
            'doacao_item.unidade'
        );

        return $query;
    }
}