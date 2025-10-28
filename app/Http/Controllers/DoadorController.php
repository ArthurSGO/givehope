<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doador;
use App\Models\Doacao;

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


    public function store(Request $request)
    {
        if ($request->has('telefone')) {
            $request->merge(['telefone' => preg_replace('/[^0-9]/', '', $request->input('telefone'))]);
        }
        if ($request->has('cpf_cnpj')) {
            $request->merge(['cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->input('cpf_cnpj'))]);
        }
        if ($request->has('cep')) {
            $request->merge(['cep' => preg_replace('/[^0-9]/', '', $request->input('cep'))]);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:14|unique:doadores,cpf_cnpj',
            'telefone' => 'nullable|string|max:11',
            'logradouro' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:8',
            'numero' => 'nullable|string|max:20',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'redirect_to' => 'nullable|string|url'
        ], [
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
        ]);

        $doadorData = $validatedData;
        unset($doadorData['redirect_to']);

        $doador = Doador::create($doadorData);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Doador cadastrado com sucesso!',
                'doador' => [
                    'id' => $doador->id,
                    'nome' => $doador->nome,
                    'cpf_cnpj' => $doador->cpf_cnpj,
                ],
            ], 201);
        }

        $redirectUrl = $request->filled('redirect_to') ? $request->input('redirect_to') : route('doadores.index');

        return redirect($redirectUrl)
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
        if ($request->has('telefone')) {
            $request->merge(['telefone' => preg_replace('/[^0-9]/', '', $request->input('telefone'))]);
        }
        if ($request->has('cpf_cnpj')) {
            $request->merge(['cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->input('cpf_cnpj'))]);
        }
        if ($request->has('cep')) {
            $request->merge(['cep' => preg_replace('/[^0-9]/', '', $request->input('cep'))]);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'nullable|string|max:14|unique:doadores,cpf_cnpj,' . $doador->id,
            'telefone' => 'nullable|string|max:11',
            'logradouro' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:8',
            'numero' => 'nullable|string|max:20',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'redirect_to' => 'nullable|string|url'
        ], [
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
        ]);

        $doadorData = $validatedData;
        unset($doadorData['redirect_to']);

        $doador->update($doadorData);

        $redirectUrl = $request->filled('redirect_to') ? $request->input('redirect_to') : route('doadores.index');

        return redirect($redirectUrl)
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

    public function buscar(Request $request)
    {

        $cpfCnpjInput = $request->query('cpf_cnpj', $request->input('cpf_cnpj'));

        $cpfCnpjLimpo = preg_replace('/[^0-9]/', '', $cpfCnpjInput);

        if (empty($cpfCnpjLimpo)) {
            return response()->json(['error' => 'CPF/CNPJ inválido.'], 400);
        }

        $doador = Doador::where('cpf_cnpj', $cpfCnpjLimpo)->first();

        if ($doador) {
            return response()->json([
                'id' => $doador->id,
                'nome' => $doador->nome,
                'cpf_cnpj' => $doador->cpf_cnpj,
            ]);
        }

        return response()->json(['error' => 'Doador não encontrado.'], 404);
    }
}
