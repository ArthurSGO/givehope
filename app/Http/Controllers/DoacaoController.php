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
    $rules = [
        'data_doacao' => 'required|date',
        'tipo' => 'required|in:dinheiro,item',
        'items.*.quantidade' => 'required|numeric|min:0.01',
        'doador_id' => 'nullable|exists:doadores,id',
        'descricao' => 'nullable|string|max:255',
    ];
    
    if ($request->tipo === 'item') {
        $rules['items'] = 'required|array|min:1';
        $rules['items.*.quantidade'] = 'required|numeric|min:0.01';
        $rules['items.*.unidade'] = 'required|in:Unidade,Kg';
        $rules['items.*.item_id'] = 'nullable|string'; 
        $rules['items.*.new_item_name'] = 'nullable|string|max:100';
    }

    if ($request->tipo === 'dinheiro') {
        $rules['quantidade'] = 'required|numeric|min:0.01';
        $rules['unidade'] = 'required|in:R$';
    }
    $request->validate($rules);

    $data = $request->except('items');
    $user = Auth::user();

    if (!$user->is_admin) {
        $data['paroquia_id'] = $user->paroquia_id;
    }

    if ($request->doador_id === '') {
        $data['doador_id'] = null;
    }

    if ($request->tipo === 'item') {
        unset($data['quantidade'], $data['unidade']); 
    }

$doacao = Doacao::create($data); 

    if ($request->tipo === 'item') {
        $itemsData = $request->input('items', []);
        $pivotData = [];

        foreach ($itemsData as $item) {
            $itemId = $item['item_id'] ?? null;
            $itemName = $item['new_item_name'] ?? null;

            if ($itemId === 'new' && !empty($itemName)) {

            $normalizedName = strtolower($itemName);    

            $existingItem = Item::whereRaw('LOWER(nome) = ?', [$normalizedName])->first();

            if ($existingItem) {

                $itemId = $existingItem->id;
            } else {
                $newItem = Item::create(['nome' => $itemName, 'categoria' => 'Doação']); 
                $itemId = $newItem->id;
            }

            } elseif ($itemId === 'new') {
                continue; 
            }
            if ($itemId) {
                $pivotData[$itemId] = [
                    'quantidade' => $item['quantidade'],
                    'unidade' => $item['unidade'],
                ];
            }
        }
        
        $doacao->items()->sync($pivotData);
    }
    
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
