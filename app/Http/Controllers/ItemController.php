<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('nome')->get();
        return view('admin.items.list', compact('items'));
    }

    public function create()
    {
        return view('admin.items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:items,nome',
            'categoria' => 'nullable|string|max:255',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Item cadastrado com sucesso!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        return view('admin.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:items,nome,' . $item->id,
            'categoria' => 'nullable|string|max:255',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item excluído com sucesso!');
    }
}
