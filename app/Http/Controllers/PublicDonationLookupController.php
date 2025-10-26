<?php

namespace App\Http\Controllers;

use App\Models\Doador;
use Illuminate\Http\Request;

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
                            $query->with(['items', 'paroquia'])
                                ->orderByDesc('data_doacao');
                        },
                    ])
                    ->first();

                if ($doador) {
                    $doador->documento_formatado = $this->formatDocument($doador->cpf_cnpj);
                    $doacoes = $doador->doacoes->map(function ($doacao) {
                    $distribuicao = $doacao->distribuicoes()->latest()->first();

                    if ($distribuicao) {
                        switch ($distribuicao->status) {
                            case 'reservado':
                                $doacao->status_distribuicao = 'Distribuição reservada';
                                $doacao->status_badge = 'warning';
                                break;

                            case 'enviado':
                                $doacao->status_distribuicao = 'Distribuição enviada';
                                $doacao->status_badge = 'primary';
                                break;

                            case 'entregue':
                                $doacao->status_distribuicao = 'Distribuição entregue';
                                $doacao->status_badge = 'success';
                                break;

                            default:
                                $doacao->status_distribuicao = ucfirst($distribuicao->status);
                                $doacao->status_badge = 'secondary';
                        }
                    } else {
                        $doacao->status_distribuicao = 'Registrado';
                        $doacao->status_badge = 'secondary';
                    }
                        if ($doacao->tipo === 'dinheiro' && $doacao->quantidade !== null) {
                            $doacao->quantidade_formatada = $this->formatQuantity((float) $doacao->quantidade, $doacao->unidade ?? 'R$');
                        }

                        if ($doacao->tipo === 'item' && $doacao->items->isNotEmpty()) {
                            $doacao->items->each(function ($item) {
                                $item->quantidade_formatada = $this->formatQuantity((float) $item->pivot->quantidade, $item->pivot->unidade);
                            });
                        }

                        return $doacao;
                    });
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
}
