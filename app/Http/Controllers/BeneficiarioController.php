<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiario;

class BeneficiarioController extends Controller
{
    public function index()
    {
        $beneficiarios = Beneficiario::orderBy('nome')->get();

        return view('admin.beneficiarios.list', compact('beneficiarios'));
    }

    public function create()
    {
        return view('admin.beneficiarios.create');
    }

    public function store(Request $request)
    {
        if ($request->filled('telefone')) {
            $request->merge([
                'telefone' => preg_replace('/[^0-9]/', '', $request->input('telefone')),
            ]);
        }

        if ($request->filled('cep')) {
            $request->merge([
                'cep' => preg_replace('/[^0-9]/', '', $request->input('cep')),
            ]);
        }

        if ($request->filled('cpf')) {
            $request->merge([
                'cpf' => preg_replace('/[^0-9]/', '', $request->input('cpf')),
            ]);
        }

        if ($request->filled('estado')) {
            $request->merge([
                'estado' => strtoupper($request->input('estado')),
            ]);
        }

        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'telefone' => 'nullable|string|digits_between:10,11',
            'endereco' => 'required|string|max:100',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|digits:8',
            'cpf' => 'nullable|string|digits:11',
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'email' => 'nullable|email|max:150',
            'ponto_referencia' => 'nullable|string|max:150',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $beneficiario = Beneficiario::create(
            collect($data)
                ->map(fn($value) => $value === '' ? null : $value)
                ->toArray()
        );

        return redirect()
            ->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Beneficiário cadastrado com sucesso!');
    }

    public function show(Beneficiario $beneficiario)
    {
        return view('admin.beneficiarios.show', compact('beneficiario'));
    }

    public function edit(Beneficiario $beneficiario)
    {
        return view('admin.beneficiarios.edit', compact('beneficiario'));
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        if ($request->filled('telefone')) {
            $request->merge([
                'telefone' => preg_replace('/[^0-9]/', '', $request->input('telefone')),
            ]);
        }

        if ($request->filled('cep')) {
            $request->merge([
                'cep' => preg_replace('/[^0-9]/', '', $request->input('cep')),
            ]);
        }

        if ($request->filled('cpf')) {
            $request->merge([
                'cpf' => preg_replace('/[^0-9]/', '', $request->input('cpf')),
            ]);
        }

        if ($request->filled('estado')) {
            $request->merge([
                'estado' => strtoupper($request->input('estado')),
            ]);
        }

        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'telefone' => 'nullable|string|digits_between:10,11',
            'endereco' => 'required|string|max:100',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|digits:8',
            'cpf' => 'nullable|string|digits:11',
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'email' => 'nullable|email|max:150',
            'ponto_referencia' => 'nullable|string|max:150',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $beneficiario->update(
            collect($data)
                ->map(fn($value) => $value === '' ? null : $value)
                ->toArray()
        );

        return redirect()
            ->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Beneficiário atualizado com sucesso!');
    }

    public function destroy(Beneficiario $beneficiario)
    {
        $beneficiario->delete();

        return redirect()
            ->route('beneficiarios.index')
            ->with('success', 'Beneficiário excluído com sucesso!');
    }
}
