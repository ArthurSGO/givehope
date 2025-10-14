<?php

namespace App\Http\Controllers;

use App\Models\Paroquia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParoquiaController extends Controller
{
    public function index()
    {
        $paroquias = Paroquia::all();
        return view('admin.paroquias.list', compact('paroquias'));
    }

    public function create()
    {
        return view('admin.paroquias.create');
    }

    public function store(Request $request)
    {

        $request->merge([
            'cnpj' => preg_replace('/[^\d]/', '', $request->cnpj)
        ]);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:paroquias,cnpj|max:14',
            'logradouro' => 'required|string|max:255',
            'email' => 'nullable|string|unique:paroquias,email|max:255',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'telefone' => 'required|string|max:11',
            'numero'=> 'required|string|max:5',
        ]);

        Paroquia::create($request->all());

        return redirect()->route('paroquias.index')
            ->with('success', 'Paróquia cadastrada com sucesso!');
    }

    public function show($id)
    {
        //
    }

    public function edit(Paroquia $paroquia)
    {
        return view('admin.paroquias.edit', compact('paroquia'));
    }

    public function update(Request $request, Paroquia $paroquia)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => ['required', 'string', 'max:14', Rule::unique('paroquias')->ignore($paroquia->id)],
            'logradouro' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'telefone' => 'required|string|max:11',
        ]);

        $paroquia->update($request->all());

        return redirect()->route('paroquias.index')
            ->with('success', 'Paróquia atualizada com sucesso!');
    }

    public function destroy(Paroquia $paroquia)
    {
        $paroquia->delete();

        return redirect()->route('paroquias.index')
            ->with('success', 'Paróquia excluída com sucesso!');
    }
}
