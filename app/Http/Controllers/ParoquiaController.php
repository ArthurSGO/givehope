<?php

namespace App\Http\Controllers;

use App\Models\Paroquia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;

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
        $cnpjSanitizado = preg_replace('/[^\d]/', '', (string) $request->cnpj);
        $telefoneSanitizado = preg_replace('/[^\d]/', '', (string) $request->telefone);
        $telefoneSanitizado = $telefoneSanitizado !== '' ? $telefoneSanitizado : null;

        $request->merge([
            'cnpj' => $cnpjSanitizado,
            'telefone' => $telefoneSanitizado,
        ]);

        $validatedCnpj = $request->validate([
            'cnpj' => 'required|string|unique:paroquias,cnpj|size:14',
        ]);

        $request->validate([
            'telefone' => ['nullable', 'digits_between:10,11'],
            'email' => ['nullable', 'email', 'max:255'],
            'numero' => ['nullable', 'string', 'max:10'],
        ]);

        $response = Http::get("https://receitaws.com.br/v1/cnpj/{$validatedCnpj['cnpj']}");

        $dadosParaSalvar = [];
        if ($response->successful() && $response->json('status') === 'OK') {
            $apiData = $response->json();
            $telefoneApi = preg_replace('/[^\d]/', '', $apiData['telefone'] ?? '');
            $telefoneApi = $telefoneApi !== '' ? $telefoneApi : null;
            if ($telefoneApi && !preg_match('/^\d{10,11}$/', $telefoneApi)) {
                $telefoneApi = null;
            }

            $telefoneFinal = $request->telefone ?? $telefoneApi;
            $dadosParaSalvar = [
                'nome'              => $apiData['nome'],
                'nome_fantasia'     => $apiData['fantasia'] ?? null,
                'cnpj'              => $validatedCnpj['cnpj'],
                'abertura'          => $apiData['abertura'] ?? null,
                'porte'             => $apiData['porte'] ?? null,
                'natureza_juridica' => $apiData['natureza_juridica'] ?? null,
                'situacao'          => $apiData['situacao'] ?? null,
                'logradouro'        => $apiData['logradouro'] ?? $request->logradouro,
                'numero'            => $apiData['numero'] ?? $request->numero,
                'bairro'            => $apiData['bairro'] ?? null,
                'cep'               => preg_replace('/[^\d]/', '', $apiData['cep'] ?? ''),
                'cidade'            => $apiData['municipio'] ?? $request->cidade,
                'estado'            => $apiData['uf'] ?? $request->estado,
                'telefone'          => $telefoneFinal,
                'email'             => $apiData['email'] ?? $request->email,
            ];
        } else {
            $dadosParaSalvar = $request->validate([
                'nome' => 'required|string|max:255',
                'cnpj' => 'required|string|unique:paroquias,cnpj|size:14',
                'logradouro' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'cidade' => 'required|string|max:100',
                'estado' => 'required|string|max:100',
                'telefone' => ['nullable', 'digits_between:10,11'],
                'numero' => 'nullable|string|max:10',
            ]);
        }

        Paroquia::create($dadosParaSalvar);

        return redirect()->route('paroquias.index')
            ->with('success', 'Paróquia cadastrada com sucesso!');
    }

    public function show(Paroquia $paroquia)
    {
        $paroquia->load(['users', 'doacoes']);
        return view('admin.paroquias.show', compact('paroquia'));
    }

    public function edit(Paroquia $paroquia)
    {
        return view('admin.paroquias.edit', compact('paroquia'));
    }

    public function update(Request $request, Paroquia $paroquia)
    {
        $cnpjSanitizado = preg_replace('/[^\d]/', '', (string) $request->cnpj);
        $telefoneSanitizado = preg_replace('/[^\d]/', '', (string) $request->telefone);
        $telefoneSanitizado = $telefoneSanitizado !== '' ? $telefoneSanitizado : null;

        $request->merge([
            'cnpj' => $cnpjSanitizado,
            'telefone' => $telefoneSanitizado,
        ]);

        $request->validate([
            'telefone' => ['nullable', 'digits_between:10,11'],
            'email' => ['nullable', 'email', 'max:255'],
            'numero' => ['nullable', 'string', 'max:10'],
        ]);

        $paroquia->update($request->all());

        return redirect()->route('paroquias.index')
            ->with('success', 'Paróquia atualizada com sucesso!');
    }

    public function destroy(Paroquia $paroquia)
    {
        try {
            $paroquia->delete();
            return redirect()->route('paroquias.index')->with('success', 'Paróquia excluída com sucesso!');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Não é possível excluir esta paróquia, pois ela possui usuários ou outros registros associados.');
            }
            return back()->with('error', 'Ocorreu um erro no banco de dados ao tentar excluir a paróquia.');
        }
    }
}
