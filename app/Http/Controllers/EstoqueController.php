<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;
use App\Models\EstoqueMovimentacao;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->is_admin) {
            $estoquesPorParoquia = Estoque::with(['item', 'paroquia'])
                ->get()
                ->groupBy('paroquia_id')
                ->map(function ($grupo) {
                    $ordenados = $grupo->sortBy(fn($estoque) => optional($estoque->item)->nome);

                    return [
                        'paroquia' => $grupo->first()->paroquia,
                        'estoques' => $ordenados->values(),
                    ];
                })
                ->sortBy(function ($grupo) {
                    $paroquia = $grupo['paroquia'];

                    return optional($paroquia)->nome_fantasia ?? optional($paroquia)->nome ?? '';
                })
                ->values();

            return view('admin.estoque.index', [
                'isAdmin' => true,
                'estoquesPorParoquia' => $estoquesPorParoquia,
            ]);
        }

        $paroquia = $user->paroquia;

        $estoques = Estoque::with('item')
            ->where('paroquia_id', $user->paroquia_id)
            ->get()
            ->sortBy(fn($estoque) => optional($estoque->item)->nome)
            ->values();

        return view('admin.estoque.list', [
            'isAdmin' => false,
            'paroquia' => $paroquia,
            'estoques' => $estoques,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Estoque $estoque)
    {
        $user = Auth::user();
        if ($estoque->paroquia_id !== $user->paroquia_id) {
            abort(404);
        }

        $estoque->load('item');

        $movimentacoes = EstoqueMovimentacao::with(['user', 'doacao', 'distribuicao'])
            ->where('estoque_id', $estoque->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.estoque.show', compact('estoque', 'movimentacoes'));

    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
