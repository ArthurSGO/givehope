<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

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
        $itemsData = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'nome' => $item->nome,
                'categoria' => $item->categoria,
            ];
        })->values();

        return view('admin.doacoes.create', compact('doadores', 'items', 'itemsData'));
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
            $doadorAnonimo = Doador::firstOrCreate(
                ['nome' => 'Anônimo'],
                [
                    'cpf_cnpj' => null,
                    'telefone' => null,
                    'logradouro' => null,
                    'cep' => null,
                    'numero' => null,
                    'cidade' => null,
                    'estado' => null
                ]
            );
            $doadorId = $doadorAnonimo->id;
        } elseif (empty($doadorId)) {
            return back()->withErrors(['doador_id' => 'Selecione um doador ou marque a opção "Doação Anônima".'])->withInput();
        }


        $doacao = new Doacao();
        $doacao->data_doacao = $validatedData['data_doacao'];
        $doacao->tipo = $validatedData['tipo'];
        $doacao->descricao = $validatedData['descricao'];
        $paroquiaId = Auth::user()->paroquia_id;
        $userId = Auth::id();
        $doacao->paroquia_id = $paroquiaId;
        $doacao->doador_id = $doadorId;

        if ($request->tipo == 'dinheiro') {
            $request->validate(['quantidade' => 'required|numeric|min:0.01']);
            DB::transaction(function () use ($doacao, $request) {
                $doacao->quantidade = (float) $request->quantidade;
                $doacao->unidade = 'R$';
                $doacao->save();
            });
        } elseif ($request->tipo == 'item') {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.quantidade' => 'required|numeric|min:0.01',
                'items.*.unidade' => 'required|in:Unidade,Kg',
                'items.*.item_id' => 'required',
                'items.*.new_item_name' => 'nullable|required_if:items.*.item_id,new|string|max:255',
                'items.*.new_item_category' => 'sometimes|nullable|required_if:items.*.item_id,new|in:alimento,outro',
            ], [
                'items.required' => 'Adicione pelo menos um item à doação.',
                'items.min' => 'Adicione pelo menos um item à doação.',
                'items.*.new_item_name.required_if' => 'O nome do novo item é obrigatório.',
                'items.*.new_item_category.required_if' => 'A categoria do novo item é obrigatória.',
                'items.*.new_item_category.in' => 'Selecione uma categoria válida para o item.',
            ]);

            $doacao->quantidade = null;
            $doacao->unidade = null;
            $doacao->save();

            $itemsCollection = collect($request->input('items', []));
            $existingItemIds = $itemsCollection
                ->pluck('item_id')
                ->filter(fn($id) => is_numeric($id))
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $existingItems = Item::whereIn('id', $existingItemIds)->get()->keyBy('id');

            $unitErrors = [];
            foreach ($itemsCollection as $index => $itemData) {
                $unidade = $itemData['unidade'] ?? null;
                if (($itemData['item_id'] ?? null) === 'new') {
                    $category = $itemData['new_item_category'] ?? null;
                    if ($category === 'alimento' && $unidade !== 'Kg') {
                        $unitErrors["items.$index.unidade"] = 'Itens da categoria "Alimento" devem ser cadastrados em Kg.';
                    }
                } elseif (is_numeric($itemData['item_id'])) {
                    $existingItem = $existingItems->get((int) $itemData['item_id']);
                    if ($existingItem && $existingItem->categoria === 'alimento' && $unidade !== 'Kg') {
                        $unitErrors["items.$index.unidade"] = 'Itens da categoria "Alimento" devem ser cadastrados em Kg.';
                    }
                }
            }

            if (!empty($unitErrors)) {
                return back()->withErrors($unitErrors)->withInput();
            }

            $itemsToAttach = [];
            foreach ($request->items as $itemData) {
                $itemId = null;
                if ($itemData['item_id'] == 'new' && !empty($itemData['new_item_name'])) {
                    $itemName = trim($itemData['new_item_name']);
                    $newItemCategory = $itemData['new_item_category'] ?? null;
                    $newItem = Item::firstOrCreate(
                        ['nome' => $itemName],
                        $newItemCategory ? ['categoria' => $newItemCategory] : []
                    );
                    if (!$newItem->wasRecentlyCreated && $newItemCategory && empty($newItem->categoria)) {
                        $newItem->update(['categoria' => $newItemCategory]);
                    }
                    $itemId = $newItem->id;
                } elseif (is_numeric($itemData['item_id'])) {
                    $itemId = $itemData['item_id'];
                }

                if ($itemId) {
                    $itemsToAttach[$itemId] = [
                        'quantidade' => round((float) $itemData['quantidade'], 3),
                        'unidade' => $itemData['unidade']
                    ];
                }
            }

            if (!empty($itemsToAttach)) {
                DB::transaction(function () use ($doacao, $itemsToAttach, $paroquiaId, $userId) {
                    $doacao->quantidade = null;
                    $doacao->unidade = null;
                    $doacao->save();

                    $doacao->items()->attach($itemsToAttach);

                    foreach ($itemsToAttach as $itemId => $itemData) {
                        $estoque = Estoque::firstOrNew([
                            'paroquia_id' => $paroquiaId,
                            'item_id' => $itemId,
                            'unidade' => $itemData['unidade'],
                        ]);

                        $estoque->quantidade = round(($estoque->quantidade ?? 0) + $itemData['quantidade'], 3);
                        $estoque->save();

                        \App\Models\EstoqueMovimentacao::create([
                            'estoque_id' => $estoque->id,
                            'paroquia_id' => $paroquiaId,
                            'item_id' => $itemId,
                            'doacao_id' => $doacao->id,
                            'user_id' => $userId,
                            'tipo' => 'entrada',
                            'quantidade' => $itemData['quantidade'],
                            'unidade' => $itemData['unidade'],
                            'motivo' => 'Doação recebida',
                        ]);
                    }
                });
            } else {
                return back()->withErrors(['items' => 'Não foi possível processar os itens. Verifique os dados.'])->withInput();
            }
        } else {
            return back()->withErrors(['tipo' => 'Tipo de doação inválido.'])->withInput();
        }

        return redirect()->route('doacoes.index')->with('success', 'Doação registrada com sucesso!');
    }

    public function show($id)
    {
        $user = Auth::user();

        $doacaoQuery = Doacao::with(['doador', 'items']);

        if (!$user->is_admin) {
            $doacaoQuery->where('paroquia_id', $user->paroquia_id);
        }

        $doacao = $doacaoQuery->findOrFail($id);

        $resumoItens = [];

        if ($doacao->tipo === 'item') {
            $formatarQuantidade = function (float $valor, string $unidade): string {
                $casasDecimais = $unidade === 'Kg' ? 3 : 0;
                $formatado = number_format($valor, $casasDecimais, ',', '.');

                if ($casasDecimais > 0) {
                    $formatado = rtrim(rtrim($formatado, '0'), ',');
                }

                return $formatado;
            };

            $doacao->items->each(function ($item) use ($formatarQuantidade) {
                $item->formatted_quantidade = $formatarQuantidade(
                    (float) $item->pivot->quantidade,
                    $item->pivot->unidade
                );
            });

            $resumoItens = $doacao->items
                ->groupBy(fn($item) => $item->pivot->unidade)
                ->mapWithKeys(function ($grupo, $unidade) use ($formatarQuantidade) {
                    $total = $grupo->sum(fn($item) => (float) $item->pivot->quantidade);

                    return [$unidade => $formatarQuantidade($total, $unidade)];
                })
                ->toArray();
        }

        $logs = Activity::with('causer:id,name')
            ->where('log_name', 'Doações')
            ->where('subject_type', Doacao::class)
            ->where('subject_id', $doacao->id)
            ->latest()
            ->get();

        return view('admin.doacoes.show', compact('doacao', 'logs'));
    }

    public function edit(Doacao $doacao)
    {
        $user = Auth::user();

        if (!$user->is_admin && $doacao->paroquia_id !== $user->paroquia_id) {
            abort(404);
        }

        $isEditavel = $doacao->created_at->gt(Carbon::now()->subMinutes(15));

        $doacao->load(['doador', 'items']);
        $doadores = Doador::orderBy('nome')->get();

        $itemsData = [];
        $itensAtuais = [];

        if ($doacao->tipo === 'item') {
            $formatarQuantidade = function (float $valor, string $unidade): string {
                $casasDecimais = $unidade === 'Kg' ? 3 : 0;
                $formatado = number_format($valor, $casasDecimais, ',', '.');
                if ($casasDecimais > 0) {
                    $formatado = rtrim(rtrim($formatado, '0'), ',');
                }
                return $formatado;
            };

            foreach ($doacao->items as $item) {
                $item->formatted_quantidade = $formatarQuantidade(
                    (float) $item->pivot->quantidade,
                    $item->pivot->unidade
                );

                $itensAtuais[] = [
                    'id' => $item->id,
                    'nome' => $item->nome,
                    'quantidade' => (float) $item->pivot->quantidade,
                    'unidade' => $item->pivot->unidade,
                    'categoria' => $item->categoria,
                ];
            }
        }

        if ($isEditavel) {
            $itemsData = Item::orderBy('nome')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nome' => $item->nome,
                    'categoria' => $item->categoria,
                ];
            })->values();
        }

        return view('admin.doacoes.edit', compact(
            'doacao',
            'doadores',
            'itemsData',
            'itensAtuais',
            'isEditavel'
        ));
    }

    public function update(Request $request, Doacao $doacao)
    {
        $user = Auth::user();

        if (!$user->is_admin && $doacao->paroquia_id !== $user->paroquia_id) {
            abort(404);
        }

        $isEditavel = $doacao->created_at->gt(Carbon::now()->subMinutes(15));

        $request->merge(['doacao_anonima' => $request->has('doacao_anonima')]);

        $basicRules = [
            'data_doacao' => 'required|date',
            'doador_id' => 'nullable|exists:doadores,id',
            'doacao_anonima' => 'nullable|boolean',
            'descricao' => 'nullable|string',
        ];

        $doadorId = $request->input('doador_id');
        $isAnonima = $request->input('doacao_anonima');

        if ($isAnonima) {
            $doadorAnonimo = Doador::firstOrCreate(['nome' => 'Anônimo']);
            $doadorId = $doadorAnonimo->id;
        } elseif (empty($doadorId)) {
            return back()->withErrors(['doador_id' => 'Selecione um doador ou marque "Doação Anônima".'])->withInput();
        }

        DB::beginTransaction();
        try {
            $tipoOriginal = $doacao->tipo;

            $doacao->data_doacao = $request->data_doacao;
            $doacao->descricao = $request->descricao;
            $doacao->doador_id = $doadorId;

            if ($isEditavel) {

                $fullRules = $basicRules + [
                    'tipo' => 'required|in:dinheiro,item',
                    'quantidade' => 'nullable|required_if:tipo,dinheiro|numeric|min:0.01',
                    'items' => 'nullable|required_if:tipo,item|array|min:1',
                    'items.*.item_id' => 'required|string',
                    'items.*.quantidade' => 'required|numeric|min:0.001',
                    'items.*.unidade' => 'required|string',
                    'items.*.new_item_name' => 'nullable|string|required_if:items.*.item_id,new',
                    'items.*.new_item_category' => 'nullable|string|required_if:items.*.item_id,new',
                ];

                $request->validate($fullRules, [
                    'items.required_if' => 'Você deve adicionar pelo menos um item.',
                    'items.*.item_id.required' => 'O ID do item é obrigatório.',
                    'items.*.new_item_name.required_if' => 'O nome do novo item é obrigatório.',
                    'items.*.new_item_category.required_if' => 'A categoria do novo item é obrigatória.',
                ]);

                $novoTipo = $request->tipo;

                if ($tipoOriginal === 'item') {
                    foreach ($doacao->items as $itemAntigo) {
                        $estoque = Estoque::firstOrNew([
                            'item_id' => $itemAntigo->id,
                            'paroquia_id' => $doacao->paroquia_id,
                        ]);

                        $quantidadeEstorno = (float) $itemAntigo->pivot->quantidade;
                        if ($estoque->quantidade < $quantidadeEstorno) {
                            throw new \Exception("Não é possível editar. O item '{$itemAntigo->nome}' já foi distribuído e o estorno resultaria em estoque negativo.");
                        }

                        $estoque->decrement('quantidade', $quantidadeEstorno);

                        EstoqueMovimentacao::create([
                            'estoque_id' => $estoque->id,
                            'tipo' => 'estorno_edicao',
                            'quantidade' => -$quantidadeEstorno,
                            'unidade' => $itemAntigo->pivot->unidade,
                            'user_id' => $user->id,
                        ]);
                    }
                    $doacao->items()->detach();
                }

                $doacao->tipo = $novoTipo;

                if ($novoTipo === 'dinheiro') {
                    $doacao->quantidade = (float) $request->quantidade;
                    $doacao->unidade = 'R$';
                } else if ($novoTipo === 'item') {
                    $doacao->quantidade = null;
                    $doacao->unidade = null;

                    $itemsParaSincronizar = [];
                    $itemModels = [];
                    $itemDataArray = $request->items;
                    $existingItemIds = [];

                    foreach ($itemDataArray as $index => $itemData) {
                        if ($itemData['item_id'] === 'new') {
                            $newItem = Item::create([
                                'nome' => $itemData['new_item_name'],
                                'categoria' => $itemData['new_item_category'],
                            ]);
                            $itemDataArray[$index]['item_id'] = $newItem->id;
                            $itemModels[$newItem->id] = $newItem;
                        } else {
                            $existingItemIds[] = $itemData['item_id'];
                        }
                    }

                    if (!empty($existingItemIds)) {
                        $itemModels += Item::whereIn('id', $existingItemIds)->get()->keyBy('id')->all();
                    }

                    foreach ($itemDataArray as $itemData) {
                        $itemId = $itemData['item_id'];
                        if (!isset($itemModels[$itemId])) {
                            throw new \Exception("Item com ID {$itemId} não foi encontrado ou criado.");
                        }

                        $itemModel = $itemModels[$itemId];
                        $categoria = $itemModel->categoria;
                        $unidade = $itemData['unidade'];
                        $nomeItem = $itemModel->nome;

                        if ($categoria === 'Alimento' && !in_array($unidade, ['Un', 'Kg'])) {
                            throw new \Exception("A unidade '{$unidade}' não é válida para o Alimento '{$nomeItem}'. Use 'Un' ou 'Kg'.");
                        }
                        if ($categoria === 'Higiene' && $unidade !== 'Un') {
                            throw new \Exception("A unidade '{$unidade}' não é válida para Higiene '{$nomeItem}'. Use 'Un'.");
                        }
                        if ($categoria === 'Limpeza' && !in_array($unidade, ['Un', 'L'])) {
                            throw new \Exception("A unidade '{$unidade}' não é válida para Limpeza '{$nomeItem}'. Use 'Un' ou 'L'.");
                        }

                        $itemsParaSincronizar[$itemId] = [
                            'quantidade' => (float) $itemData['quantidade'],
                            'unidade' => $itemData['unidade'],
                        ];

                        Estoque::entrada(
                            $itemId,
                            $doacao->paroquia_id,
                            (float) $itemData['quantidade'],
                            $itemData['unidade'],
                            Doacao::class,
                            $doacao->id,
                            $user->id
                        );
                    }

                    $doacao->items()->sync($itemsParaSincronizar);
                }

            } else {
                if ($isAnonima && $doacao->tipo === 'item') {
                    return back()->withErrors(['doacao_anonima' => 'Não é possível marcar doação de itens como anônima.'])->withInput();
                }

                if ($doacao->tipo === 'dinheiro') {
                    $request->validate(['quantidade' => 'required|numeric|min:0.01']);
                    $doacao->quantidade = (float) $request->quantidade;
                }
            }

            $doacao->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['db_error' => 'Erro ao salvar: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('doacoes.show', $doacao->id)->with('success', 'Doação atualizada com sucesso!');
    }

    public function destroy($id)
    {
        //
    }

    public function report(Request $request)
    {
        $user = Auth::user();
        $filtros = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'doador_id' => ['nullable', 'exists:doadores,id'],
            'tipo' => ['nullable', 'in:dinheiro,item'],
        ]);

        $dadosRelatorio = $this->montarConsultaRelatorio($filtros, $user->paroquia_id)->paginate(20);
        $doadores = Doador::orderBy('nome')->get();

        return view('admin.doacoes.relatorios', [
            'dadosRelatorio' => $dadosRelatorio,
            'doadores' => $doadores,
            'filtros' => $filtros,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $filtros = $request->validate([
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'doador_id' => ['nullable', 'exists:doadores,id'],
            'tipo' => ['nullable', 'in:dinheiro,item'],
        ]);

        $linhas = $this->montarConsultaRelatorio($filtros, $user->paroquia_id)->get();
        $nomeArquivo = 'relatorio-doacoes-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($linhas) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Data', 'Doador', 'Tipo', 'Valor (R$)', 'Itens (Qtd)', 'Unidade']);

            foreach ($linhas as $linha) {

                $quantidadeItens = 'N/A';
                $unidadeItens = 'N/A';

                if ($linha->tipo === 'item') {
                    $quantidade = (float) $linha->total_itens;
                    $unidade = $linha->unidade_itens;
                    $casasDecimais = ($unidade === 'Kg') ? 3 : 0;
                    $valorFormatado = number_format($quantidade, $casasDecimais, ',', '.');

                    if ($casasDecimais > 0) {
                        $valorFormatado = rtrim(rtrim($valorFormatado, '0'), ',');
                    }

                    $quantidadeItens = $valorFormatado;
                    $unidadeItens = $unidade;
                    if ($unidade === 'Unidade' && $quantidade != 1.0) {
                        $unidadeItens = 'Unidades';
                    }
                }

                fputcsv($handle, [
                    \Carbon\Carbon::parse($linha->data_doacao)->format('d/m/Y'),
                    $linha->doador->nome,
                    $linha->tipo,
                    $linha->tipo === 'dinheiro' ? number_format($linha->quantidade, 2, ',', '.') : 'N/A',
                    $quantidadeItens,
                    $unidadeItens,
                ]);
            }

            fclose($handle);
        }, $nomeArquivo, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function montarConsultaRelatorio(array $filtros, int $paroquiaId)
    {
        $query = Doacao::with(['doador'])
            ->where('paroquia_id', $paroquiaId)
            ->select('doacoes.*')
            ->selectRaw('SUM(doacao_item.quantidade) as total_itens')
            ->selectRaw('doacao_item.unidade as unidade_itens')
            ->leftJoin('doacao_item', 'doacoes.id', '=', 'doacao_item.doacao_id');

        if (!empty($filtros['doador_id'])) {
            $query->where('doador_id', $filtros['doador_id']);
        }

        if (!empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (!empty($filtros['data_inicio'])) {
            $query->whereDate('data_doacao', '>=', $filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $query->whereDate('data_doacao', '<=', $filtros['data_fim']);
        }

        $query->orderBy('data_doacao', 'desc');

        $query->groupBy(
            'doacoes.id',
            'doacoes.paroquia_id',
            'doacoes.doador_id',
            'doacoes.tipo',
            'doacoes.quantidade',
            'doacoes.unidade',
            'doacoes.descricao',
            'doacoes.data_doacao',
            'doacoes.created_at',
            'doacoes.updated_at',
            'doacao_item.unidade'
        );

        return $query;
    }
}
