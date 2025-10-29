<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doacao;
use App\Models\Distribuicao;
use App\Models\Beneficiario;
use App\Models\Estoque;

class PainelController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $paroquiaId = $user->paroquia_id;
        $dataInicio = now()->subDays(30);

        $stats = [
            'doacoes_30d' => Doacao::where('paroquia_id', $paroquiaId)
                ->where('tipo', 'dinheiro')
                ->where('data_doacao', '>=', $dataInicio)
                ->sum('quantidade'),

            'distribuicoes_30d' => Distribuicao::where('paroquia_id', $paroquiaId)
                ->whereIn('status', [Distribuicao::STATUS_ENVIADO, Distribuicao::STATUS_ENTREGUE])
                ->where(function($query) use ($dataInicio) {
                    $query->where('enviado_em', '>=', $dataInicio)
                          ->orWhere('entregue_em', '>=', $dataInicio);
                })
                ->count(),
            
            'beneficiarios_atendidos' => Beneficiario::whereHas('distribuicoes', function($query) use ($paroquiaId) {
                $query->where('paroquia_id', $paroquiaId)
                      ->whereIn('status', [Distribuicao::STATUS_ENVIADO, Distribuicao::STATUS_ENTREGUE]);
            })->count(),

            'itens_em_estoque' => Estoque::where('paroquia_id', $paroquiaId)
                ->where('quantidade', '>', 0)
                ->count(),
        ];

        return view('painel.dashboard', compact('user', 'stats'));
    }
}