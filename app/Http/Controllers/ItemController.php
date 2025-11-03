<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $itens = Item::orderBy('nome')->get();
        return view('admin.itens.list', compact('itens'));
    }

    public function create()
    {
        return view('admin.itens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:itens,nome',
            'categoria' => 'nullable|string|max:255',
        ]);

        Item::create($request->all());

        return redirect()->route('itens.index')->with('success', 'Item cadastrado com sucesso!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        return view('admin.itens.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:itens,nome,' . $item->id,
            'categoria' => 'nullable|string|max:255',
        ]);

        $item->update($request->all());

        return redirect()->route('itens.index')->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('itens.index')->with('success', 'Item excluído com sucesso!');
    }
}
