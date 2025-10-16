<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doador;

class DoadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $doadores = Doador::latest()->get();
        return view('admin.doadores.list', compact('doadores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.doadores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:14',
            'telefone' => 'nullable|string|max:11',
        ]);

        Doador::create($request->all());

        return redirect()->route('doadores.index')
            ->with('success', 'Doador cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doador  $doador
     * @return \Illuminate\View\View
     */
    public function edit(Doador $doador)
    {
        return view('admin.doadores.edit', compact('doador'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doador  $doador
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Doador $doador)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:14',
            'telefone' => 'nullable|string|max:11',
        ]);

        $doador->update($request->all());

        return redirect()->route('doadores.index')
            ->with('success', 'Doador atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doador  $doador
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Doador $doador)
    {
        $doador->delete();

        return redirect()->route('doadores.index')
            ->with('success', 'Doador excluído com sucesso!');
    }

    public function buscarPorCpfCnpj(Request $request)
    {
        // 1. Valida se o parâmetro foi enviado
        $request->validate(['cpf_cnpj' => 'required|string']);

        // 2. Pega o valor enviado (pode estar com ou sem máscara)
        $cpfCnpjInput = $request->input('cpf_cnpj');

        // 3. Remove QUALQUER caractere que não seja um número
        $cpfCnpjLimpo = preg_replace('/[^0-9]/', '', $cpfCnpjInput);

        // 4. Se depois da limpeza não sobrar nada, retorna erro
        if (empty($cpfCnpjLimpo)) {
            return response()->json(['error' => 'CPF/CNPJ inválido.'], 400);
        }

        // 5. Busca no banco de dados ONDE a coluna 'cpf_cnpj' é IGUAL ao valor limpo
        $doador = Doador::where('cpf_cnpj', $cpfCnpjLimpo)->first();

        // 6. Responde com sucesso ou erro
        if ($doador) {
            return response()->json([
                'id' => $doador->id,
                'nome' => $doador->nome,
            ]);
        }

        return response()->json(['error' => 'Doador não encontrado.'], 404);
    }
}
