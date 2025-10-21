<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoacaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $doacoes = Doacao::where('paroquia_id', $user->paroquia_id)
            ->with('doador')
            ->latest('data_doacao')->get();

        return view('admin.doacoes.list', compact('doacoes'));
    }

    public function create()
    {
        $doadores = Doador::all();
        $items = Item::orderBy('nome')->get();

        return view('admin.doacoes.create', compact('doadores', 'items'));
    }

    public function store(Request $request)
    {
        $request->merge(['doacao_anonima' => $request->has('doacao_anonima')]);
        
        $validatedData = $request->validate([
            'data_doacao' => 'required|date',
            'doador_id' => 'nullable|exists:doadores,id',
            'doacao_anonima' => 'nullable|boolean',
            'tipo' => 'required|in:dinheiro,item',
            'descricao' => 'nullable|string',
        ]);

        $doadorId = $request->input('doador_id');
        $isAnonima = $request->input('doacao_anonima');
        if ($isAnonima) {
            if ($request->input('tipo') !== 'dinheiro') {
                return back()->withErrors(['tipo' => 'Doações anônimas só podem ser do tipo Dinheiro.'])->withInput();
            }
            $doadorAnonimo = Doador::firstOrCreate(['nome' => 'Anônimo'], ['cpf_cnpj' => null]);
            $doadorId = $doadorAnonimo->id;
        } elseif (empty($doadorId)) {
            return back()->withErrors(['doador_id' => 'Selecione um doador ou marque a opção "Doação Anônima".'])->withInput();
        }


        $doacao = new Doacao();
        $doacao->data_doacao = $validatedData['data_doacao'];
        $doacao->tipo = $validatedData['tipo'];
        $doacao->descricao = $validatedData['descricao'];
        $doacao->paroquia_id = Auth::user()->paroquia_id;
        $doacao->doador_id = $doadorId;

        if ($request->tipo == 'dinheiro') {
            $request->validate(['quantidade' => 'required|numeric|min:0.01']);
            $doacao->quantidade = $request->quantidade;
            $doacao->unidade = 'R$';
            $doacao->save();
        } elseif ($request->tipo == 'item') {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.quantidade' => 'required|numeric|min:0.01',
                'items.*.unidade' => 'required|in:Unidade,Kg',
                'items.*.item_id' => 'required',
                'items.*.new_item_name' => 'nullable|required_if:items.*.item_id,new|string|max:255',
            ], [
                'items.required' => 'Adicione pelo menos um item à doação.',
                'items.min' => 'Adicione pelo menos um item à doação.',
                'items.*.new_item_name.required_if' => 'O nome do novo item é obrigatório.',
            ]);

            $doacao->quantidade = null;
            $doacao->unidade = null;
            $doacao->save();

            $itemsToAttach = [];
            foreach ($request->items as $itemData) {
                $itemId = null;
                if ($itemData['item_id'] == 'new' && !empty($itemData['new_item_name'])) {
                    $newItem = Item::firstOrCreate(['nome' => trim($itemData['new_item_name'])]);
                    $itemId = $newItem->id;
                } elseif (is_numeric($itemData['item_id'])) {
                    $itemId = $itemData['item_id'];
                }

                if ($itemId) {
                    $itemsToAttach[$itemId] = [
                        'quantidade' => $itemData['quantidade'],
                        'unidade' => $itemData['unidade']
                    ];
                }
            }

            if (!empty($itemsToAttach)) {
                $doacao->items()->attach($itemsToAttach);
            } else {
                $doacao->delete();
                return back()->withErrors(['items' => 'Não foi possível processar os itens. Verifique os dados.'])->withInput();
            }
        } else {
            return back()->withErrors(['tipo' => 'Tipo de doação inválido.'])->withInput();
        }

        return redirect()->route('doacoes.index')->with('success', 'Doação registrada com sucesso!');
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
