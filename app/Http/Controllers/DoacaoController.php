<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use App\Models\Doador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoacaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Busca apenas as doações da paróquia do usuário logado.
        // O `with('doador')` carrega os dados do doador para exibirmos o nome.
        $doacoes = Doacao::where('paroquia_id', $user->paroquia_id)
            ->with('doador')
            ->latest('data_doacao')->get();

        // Retorna a view com a lista de doações já filtrada.
        return view('admin.doacoes.list', compact('doacoes'));
    }

    public function create()
    {
        $doadores = Doador::orderBy('nome')->get();
        return view('admin.doacoes.create', compact('doadores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_doacao' => 'required|date',
            'tipo' => 'required|in:dinheiro,item',
            'quantidade' => 'required|numeric|min:0.01',
            'unidade' => 'required|in:R$,Unidade,Kg',
            'doador_id' => 'nullable|exists:doadores,id',
            'descricao' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $user = Auth::user();

        if (!$user->is_admin) {
            $data['paroquia_id'] = $user->paroquia_id;
        }

        if ($request->doador_id === '') {
            $data['doador_id'] = null;
        }

        Doacao::create($data);

        return redirect()->route('painel.dashboard')->with('success', 'Doação registrada com sucesso!');
    }

    public function show($id)
    {
        //
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
