<?php

namespace App\Http\Controllers;

use App\Models\Distribuicao;
use App\Models\DistribuicaoItem;
use App\Models\Doador;
use App\Models\Estoque;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicDonationLookupController extends Controller
{
    public function __invoke(Request $request)
    {
        $cpfInput = $request->query('cpf', '');
        $searched = $request->has('cpf');
        $errorMessage = null;
        $doador = null;
        $doacoes = collect();

        if ($searched) {
            $cpfDigits = preg_replace('/[^0-9]/', '', (string) $cpfInput);

            $validator = validator([
                'cpf' => $cpfDigits,
            ], [
                'cpf' => 'required|digits_between:11,14',
            ], [
                'cpf.required' => 'Informe um CPF ou CNPJ válido para pesquisar.',
                'cpf.digits_between' => 'O CPF deve ter 11 dígitos e o CNPJ 14 dígitos.',
            ]);

            if ($validator->fails()) {
                $errorMessage = $validator->errors()->first('cpf');
            } else {
                $doador = Doador::where('cpf_cnpj', $cpfDigits)
                    ->with([
                        'doacoes' => function ($query) {
                            $query->with(['itens', 'paroquia'])
                                ->orderByDesc('data_doacao');
                        },
                    ])
                    ->first();

                if ($doador) {
                    $doador->documento_formatado = $this->formatDocument($doador->cpf_cnpj);
                    $doacoes = $this->prepararDoacoesParaExibicao($doador->doacoes);
                } else {
                    $errorMessage = 'Nenhum registro de doações foi encontrado para o documento informado.';
                }
            }
        }

        return view('seek', [
            'cpf' => $cpfInput,
            'searched' => $searched,
            'errorMessage' => $errorMessage,
            'doador' => $doador,
            'doacoes' => $doacoes,
        ]);
    }

    private function formatDocument(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $value);

        if (strlen($digits) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
        }

        if (strlen($digits) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
        }

        return $value;
    }

    private function formatQuantity(float $value, string $unit): string
    {
        if ($unit === 'R$') {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }

        $decimals = $unit === 'Kg' ? 3 : 2;
        $formatted = number_format($value, $decimals, ',', '.');
        $formatted = rtrim(rtrim($formatted, '0'), ',');

        return $formatted . ' ' . $unit;
    }

    private function prepararDoacoesParaExibicao(Collection $doacoes): Collection
    {
        $doacoes->loadMissing(['itens', 'paroquia']);

        $chavesItens = [];

        foreach ($doacoes as $doacao) {
            if ($doacao->tipo === 'item' && $doacao->itens->isNotEmpty()) {
                foreach ($doacao->itens as $item) {
                    $chave = $this->gerarChaveEstoque($doacao->paroquia_id, $item->id, (string) $item->pivot->unidade);
                    $chavesItens[$chave] = [
                        'paroquia_id' => $doacao->paroquia_id,
                        'item_id' => $item->id,
                        'unidade' => (string) $item->pivot->unidade,
                    ];
                }
            }
        }

        $estoquesPorChave = collect();
        $resumosDistribuicao = collect();
        $doacoesItensPorChave = collect();

        if (!empty($chavesItens)) {
            $paroquiaIds = collect($chavesItens)->pluck('paroquia_id')->unique()->values();
            $itemIds = collect($chavesItens)->pluck('item_id')->unique()->values();
            $unidades = collect($chavesItens)->pluck('unidade')->unique()->values();

            $estoquesPorChave = Estoque::query()
                ->whereIn('paroquia_id', $paroquiaIds)
                ->whereIn('item_id', $itemIds)
                ->whereIn('unidade', $unidades)
                ->get()
                ->keyBy(fn($estoque) => $this->gerarChaveEstoque($estoque->paroquia_id, $estoque->item_id, (string) $estoque->unidade));

            $resumosDistribuicao = DistribuicaoItem::query()
                ->selectRaw('
                    distribuicoes.paroquia_id,
                    distribuicao_item.item_id,
                    distribuicao_item.unidade,
                    distribuicoes.status,
                    SUM(CASE WHEN distribuicoes.status = ? THEN distribuicao_item.quantidade_reservada ELSE distribuicao_item.quantidade END) as total
                ', [Distribuicao::STATUS_RESERVADO])
                ->join('distribuicoes', 'distribuicao_item.distribuicao_id', '=', 'distribuicoes.id')
                ->whereIn('distribuicoes.paroquia_id', $paroquiaIds)
                ->whereIn('distribuicao_item.item_id', $itemIds)
                ->whereIn('distribuicao_item.unidade', $unidades)
                ->groupBy('distribuicoes.paroquia_id', 'distribuicao_item.item_id', 'distribuicao_item.unidade', 'distribuicoes.status')
                ->get()
                ->groupBy(function ($linha) {
                    return $this->gerarChaveEstoque($linha->paroquia_id, $linha->item_id, (string) $linha->unidade);
                })
                ->map(function ($linhas) {
                    $totais = [
                        Distribuicao::STATUS_RESERVADO => 0.0,
                        Distribuicao::STATUS_ENVIADO => 0.0,
                        Distribuicao::STATUS_ENTREGUE => 0.0,
                    ];

                    foreach ($linhas as $linha) {
                        if (array_key_exists($linha->status, $totais)) {
                            $totais[$linha->status] = (float) $linha->total;
                        }
                    }

                    return $totais;
                });

            $doacaoItens = DB::table('doacoes')
                ->select([
                    'doacoes.id',
                    'doacoes.paroquia_id',
                    'doacoes.doador_id',
                    'doacoes.tipo',
                    'doacoes.data_doacao',
                    'doacoes.created_at',
                    'doacao_item.item_id',
                    'doacao_item.unidade',
                    'doacao_item.quantidade',
                ])
                ->join('doacao_item', 'doacoes.id', '=', 'doacao_item.doacao_id')
                ->where('doacoes.tipo', 'item')
                ->whereIn('doacoes.paroquia_id', $paroquiaIds)
                ->whereIn('doacao_item.item_id', $itemIds)
                ->whereIn('doacao_item.unidade', $unidades)
                ->get();

            $doacoesItensPorChave = $doacaoItens
                ->groupBy(function ($linha) {
                    return $this->gerarChaveEstoque($linha->paroquia_id, $linha->item_id, (string) $linha->unidade);
                })
                ->map(function ($linhas) {
                    return collect($linhas)
                        ->sortBy(function ($linha) {
                            return $this->gerarReferenciaOrdenacao($linha->data_doacao, $linha->created_at, $linha->id);
                        })
                        ->values();
                });
        }

        $alocacoesDistribuicao = $this->distribuirSaldosPorDoacao($doacoesItensPorChave, $resumosDistribuicao);

        return $doacoes->map(function ($doacao) use ($estoquesPorChave, $alocacoesDistribuicao) {
            $doacao->status_distribuicao = 'Registrado';
            $doacao->status_badge = 'secondary';

            if ($doacao->tipo === 'dinheiro' && $doacao->quantidade !== null) {
                $doacao->quantidade_formatada = $this->formatQuantity((float) $doacao->quantidade, $doacao->unidade ?? 'R$');

                return $doacao;
            }

            if ($doacao->tipo === 'item' && $doacao->itens->isNotEmpty()) {
                $haReservas = false;
                $haEnvios = false;
                $haEntregas = false;

                $doacao->detalhes_estoque = $doacao->itens->map(function ($item) use ($doacao, $estoquesPorChave, $alocacoesDistribuicao, &$haReservas, &$haEnvios, &$haEntregas) {
                    $chave = $this->gerarChaveEstoque($doacao->paroquia_id, $item->id, (string) $item->pivot->unidade);
                    $alocacaoKey = $this->gerarChaveDoacaoItem($doacao->id, $item->id, (string) $item->pivot->unidade);
                    $alocacao = $alocacoesDistribuicao->get($alocacaoKey, [
                        'reservado' => 0.0,
                        'enviado' => 0.0,
                        'entregue' => 0.0,
                    ]);

                    $reservado = (float) $alocacao['reservado'];
                    $enviado = (float) $alocacao['enviado'];
                    $entregue = (float) $alocacao['entregue'];

                    $haReservas = $haReservas || $reservado > 0;
                    $haEnvios = $haEnvios || $enviado > 0;
                    $haEntregas = $haEntregas || $entregue > 0;

                    $estoque = $estoquesPorChave->get($chave);
                    $quantidadeDoada = (float) $item->pivot->quantidade;
                    $disponivel = max(0.0, $quantidadeDoada - $reservado - $enviado - $entregue);

                    return [
                        'nome' => $item->nome,
                        'quantidade_doada' => $this->formatQuantity($quantidadeDoada, (string) $item->pivot->unidade),
                        'reservado' => $this->formatQuantity($reservado, (string) $item->pivot->unidade),
                        'enviado' => $this->formatQuantity($enviado, (string) $item->pivot->unidade),
                        'entregue' => $this->formatQuantity($entregue, (string) $item->pivot->unidade),
                        'disponivel' => $this->formatQuantity($disponivel, (string) $item->pivot->unidade),
                        'saldo_paroquia' => $estoque ? $this->formatQuantity((float) $estoque->quantidade_disponivel, (string) $item->pivot->unidade) : null,
                    ];
                });

                if ($haEntregas) {
                    $doacao->status_distribuicao = 'Entregas registradas';
                    $doacao->status_badge = 'success';
                } elseif ($haEnvios) {
                    $doacao->status_distribuicao = 'Distribuições em andamento';
                    $doacao->status_badge = 'primary';
                } elseif ($haReservas) {
                    $doacao->status_distribuicao = 'Reservas ativas';
                    $doacao->status_badge = 'warning';
                }
            }

            return $doacao;
        });
    }

    private function gerarChaveEstoque(int $paroquiaId, int $itemId, string $unidade): string
    {
        return implode('|', [$paroquiaId, $itemId, $unidade]);
    }

    private function gerarChaveDoacaoItem(int $doacaoId, int $itemId, string $unidade): string
    {
        return implode('|', [$doacaoId, $itemId, $unidade]);
    }

    private function distribuirSaldosPorDoacao(Collection $doacoesItensPorChave, Collection $resumosDistribuicao): Collection
    {
        if ($resumosDistribuicao->isEmpty() || $doacoesItensPorChave->isEmpty()) {
            return collect();
        }

        $alocacoes = collect();

        foreach ($resumosDistribuicao as $chaveEstoque => $totais) {
            $doacoesOrdenadas = $doacoesItensPorChave->get($chaveEstoque);

            if (!$doacoesOrdenadas || $doacoesOrdenadas->isEmpty()) {
                continue;
            }

            $saldoEntregue = isset($totais[Distribuicao::STATUS_ENTREGUE]) ? (float) $totais[Distribuicao::STATUS_ENTREGUE] : 0.0;
            $saldoEnviado = isset($totais[Distribuicao::STATUS_ENVIADO]) ? (float) $totais[Distribuicao::STATUS_ENVIADO] : 0.0;
            $saldoReservado = isset($totais[Distribuicao::STATUS_RESERVADO]) ? (float) $totais[Distribuicao::STATUS_RESERVADO] : 0.0;

            foreach ($doacoesOrdenadas as $registro) {
                $quantidade = (float) $registro->quantidade;
                $restante = $quantidade;

                $entregue = min($restante, $saldoEntregue);
                $saldoEntregue -= $entregue;
                $restante -= $entregue;

                $enviado = 0.0;

                if ($restante > 0 && $saldoEnviado > 0) {
                    $enviado = min($restante, $saldoEnviado);
                    $saldoEnviado -= $enviado;
                    $restante -= $enviado;
                }

                $reservado = 0.0;

                if ($restante > 0 && $saldoReservado > 0) {
                    $reservado = min($restante, $saldoReservado);
                    $saldoReservado -= $reservado;
                    $restante -= $reservado;
                }

                $alocacoes->put(
                    $this->gerarChaveDoacaoItem((int) $registro->id, (int) $registro->item_id, (string) $registro->unidade),
                    [
                        'reservado' => $reservado,
                        'enviado' => $enviado,
                        'entregue' => $entregue,
                    ]
                );

                if ($saldoEntregue <= 0 && $saldoEnviado <= 0 && $saldoReservado <= 0) {
                    break;
                }
            }
        }

        return $alocacoes;
    }

    private function gerarReferenciaOrdenacao($dataDoacao, $createdAt, int $id): string
    {
        $timestampDoacao = $this->converterParaTimestamp($dataDoacao);
        $timestampCriacao = $this->converterParaTimestamp($createdAt);

        if ($timestampDoacao === null) {
            $timestampDoacao = $timestampCriacao ?? 0;
        }

        if ($timestampCriacao === null) {
            $timestampCriacao = $timestampDoacao;
        }

        return sprintf('%015d-%015d-%06d', $timestampDoacao, $timestampCriacao, $id);
    }

    private function converterParaTimestamp($valor): ?int
    {
        if ($valor instanceof \DateTimeInterface) {
            return $valor->getTimestamp();
        }

        if (empty($valor)) {
            return null;
        }

        $timestamp = strtotime((string) $valor);

        if ($timestamp === false) {
            return null;
        }

        return $timestamp;
    }
}