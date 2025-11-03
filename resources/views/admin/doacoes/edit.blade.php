@extends('app')
@section('title', 'Editar doação')
@section('content')

    @php
        $doadorNome = optional($doacao->doador)->nome ?? '—';
        $docData = \Carbon\Carbon::parse($doacao->data_doacao)->format('d/m/Y');
        $tipo = $doacao->tipo;
        $itensLista = $itens->map(fn($i) => ['id' => $i->id, 'nome' => $i->nome, 'categoria' => $i->categoria ?? 'outro'])->values();
    @endphp

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card mb-3">
                    <div class="card-header">Doação #{{ $doacao->id }}</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-label">Data</div>
                                <div class="form-control-plaintext fw-semibold">{{ $docData }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-label">Doador</div>
                                <div class="form-control-plaintext fw-semibold">{{ $doadorNome }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-label">Tipo</div>
                                <div class="form-control-plaintext fw-semibold text-capitalize">{{ $tipo }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('doacoes.update', $doacao->id) }}" method="POST" id="form-editar-doacao">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">Itens da doação</div>
                        <div class="card-body">
                            <ul class="list-group mb-3" id="item-list">
                                @forelse ($doacao->itens as $idx => $i)
                                    <li class="list-group-item d-flex justify-content-between align-itens-center">
                                        <div>
                                            <span class="fw-semibold">{{ $i->nome }}</span>
                                            <small class="text-muted ms-2">
                                                {{ rtrim(rtrim(number_format($i->pivot->quantidade, 3, ',', '.'), '0'), ',') }}
                                                {{ $i->pivot->unidade }}
                                            </small>
                                        </div>
                                        <button type="button" class="btn-close" aria-label="Remove"></button>
                                        <input type="hidden" name="itens[{{ $idx }}][item_id]" value="{{ $i->id }}">
                                        <input type="hidden" name="itens[{{ $idx }}][quantidade]"
                                            value="{{ $i->pivot->quantidade }}">
                                        <input type="hidden" name="itens[{{ $idx }}][unidade]" value="{{ $i->pivot->unidade }}">
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted" id="empty-item-list">Nenhum item nesta doação.</li>
                                @endforelse
                            </ul>

                            <div class="border rounded p-3 bg-light">
                                <div class="row g-3 align-itens-end">
                                    <div class="col-md-6 position-relative">
                                        <label class="form-label">Buscar item</label>
                                        <input type="text" id="item_search" class="form-control"
                                            placeholder="Digite para buscar..." autocomplete="off">
                                        <div id="item_search_results" class="list-group position-absolute w-100 shadow-sm"
                                            style="z-index:1000; display:none; top:100%; left:0; max-height:220px; overflow-y:auto;">
                                        </div>

                                        <div id="selected-item-info" class="alert alert-info mt-2 py-2 px-3 d-none">
                                            <div class="d-flex justify-content-between align-itens-center">
                                                <span id="selected-item-name" class="fw-semibold"></span>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                                                    id="clear-selected-item">Remover</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Quantidade</label>
                                        <input type="number" step="0.01" min="0" id="item_quantidade" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Unidade</label>
                                        <select id="item_unidade" class="form-select">
                                            <option value="Unidade">Unidade</option>
                                            <option value="Kg">Kg</option>
                                        </select>
                                    </div>

                                    <input type="hidden" id="selected_item_id" value="">

                                    <div class="col-md-3 d-flex align-itens-end">
                                        <button type="button" class="btn btn-outline-success w-100"
                                            id="add-item-btn">Adicionar à lista</button>
                                    </div>

                                    <div class="col-md-9 d-flex align-itens-end">
                                        <button type="button" class="btn btn-primary" id="toggle-new-item-btn">Cadastrar
                                            novo item</button>
                                    </div>

                                    <div class="col-12" id="new-item-section" style="display:none;">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nome do novo item</label>
                                                <input type="text" id="new_item_name" class="form-control"
                                                    placeholder="Nome do item">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Categoria</label>
                                                <select id="new_item_category" class="form-select">
                                                    <option value="">Selecione</option>
                                                    <option value="alimento">Alimento</option>
                                                    <option value="outro">Outro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 d-flex align-itens-end">
                                                <button type="button" class="btn btn-outline-secondary w-100"
                                                    id="cancel-new-item-btn">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-success">Salvar alterações</button>
                                <a href="{{ route('doacoes.show', $doacao->id) }}" class="btn btn-secondary">Cancelar</a>
                            </div>

                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itensData = @json($itensLista);
            const itemList = document.getElementById('item-list');
            const emptyItemList = document.getElementById('empty-item-list');

            const itensearchInput = document.getElementById('item_search');
            const itensearchResults = document.getElementById('item_search_results');
            const selectedItemInfo = document.getElementById('selected-item-info');
            const selectedItemName = document.getElementById('selected-item-name');
            const clearSelectedItemBtn = document.getElementById('clear-selected-item');
            const selectedItemIdInput = document.getElementById('selected_item_id');

            const quantidadeInput = document.getElementById('item_quantidade');
            const unidadeSelect = document.getElementById('item_unidade');

            const toggleNewItemBtn = document.getElementById('toggle-new-item-btn');
            const newitensection = document.getElementById('new-item-section');
            const cancelNewItemBtn = document.getElementById('cancel-new-item-btn');
            const newItemNameInput = document.getElementById('new_item_name');
            const newItemCategorySelect = document.getElementById('new_item_category');

            function formatQtd(v) {
                const n = Number(v);
                const s = n.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 3 });
                return s.replace(/,?0+$/, '').replace(/,$/, '');
            }

            function setUnitOptionsForCategory(category) {
                const opts = Array.from(unidadeSelect.options);
                if (category === 'alimento') {
                    opts.forEach(o => o.disabled = (o.value !== 'Kg'));
                    unidadeSelect.value = 'Kg';
                } else {
                    opts.forEach(o => o.disabled = false);
                    if (!unidadeSelect.value) unidadeSelect.value = 'Unidade';
                }
            }

            function filteritens(term) {
                const t = (term || '').toLowerCase();
                return (itensData || []).filter(i => (i.nome || '').toLowerCase().includes(t));
            }

            function renderSearchResults(list) {
                itensearchResults.innerHTML = '';
                if (!list.length) {
                    itensearchResults.style.display = 'none';
                    return;
                }
                list.slice(0, 20).forEach(item => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action';
                    btn.textContent = item.nome;
                    btn.addEventListener('click', function () {
                        selectExistingItem(item);
                    });
                    itensearchResults.appendChild(btn);
                });
                itensearchResults.style.display = 'block';
            }

            function selectExistingItem(item) {
                selectedItemIdInput.value = item.id;
                selectedItemIdInput.dataset.category = item.categoria || '';
                selectedItemInfo.classList.remove('d-none');
                selectedItemName.textContent = item.nome;
                itensearchInput.value = item.nome;
                itensearchResults.style.display = 'none';
                setUnitOptionsForCategory(item.categoria || null);
                hideNewitensection();
            }

            function clearSelectedItem() {
                selectedItemIdInput.value = '';
                selectedItemIdInput.dataset.category = '';
                selectedItemInfo.classList.add('d-none');
                selectedItemName.textContent = '';
                if (itensearchInput.value) itensearchInput.value = '';
                setUnitOptionsForCategory(null);
            }

            function showNewitensection() {
                newitensection.style.display = 'block';
                toggleNewItemBtn.textContent = 'Usar item existente';
                clearSelectedItem();
            }

            function hideNewitensection() {
                newitensection.style.display = 'none';
                toggleNewItemBtn.textContent = 'Cadastrar novo item';
                newItemNameInput.value = '';
                newItemCategorySelect.value = '';
            }

            function renumberHiddenInputs() {
                const rows = itemList.querySelectorAll('li.list-group-item');
                let k = 0;
                rows.forEach(li => {
                    const hids = li.querySelectorAll('input[type="hidden"]');
                    const isEmptyMarker = li.id === 'empty-item-list';
                    if (isEmptyMarker) return;
                    hids.forEach(h => {
                        const n = h.getAttribute('data-field');
                        if (n) h.name = `itens[${k}][${n}]`;
                    });
                    k++;
                });
            }

            function addListRow({ itemId, nome, quantidade, unidade, isNew, newName, newCategory }) {
                if (emptyItemList) emptyItemList.style.display = 'none';

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-itens-center';

                const left = document.createElement('div');
                const title = document.createElement('span');
                title.className = 'fw-semibold';
                title.textContent = isNew ? newName : nome;

                const small = document.createElement('small');
                small.className = 'text-muted ms-2';
                small.textContent = `${formatQtd(quantidade)} ${unidade}`;

                left.appendChild(title);
                left.appendChild(small);
                li.appendChild(left);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'btn-close';
                removeButton.setAttribute('aria-label', 'Remove');
                removeButton.addEventListener('click', function () {
                    li.remove();
                    if (!itemList.querySelector('li.list-group-item')) {
                        if (emptyItemList) emptyItemList.style.display = 'block';
                    }
                    renumberHiddenInputs();
                });
                li.appendChild(removeButton);

                const hid1 = document.createElement('input');
                hid1.type = 'hidden';
                hid1.setAttribute('data-field', 'item_id');
                hid1.value = isNew ? 'new' : itemId;
                li.appendChild(hid1);

                if (isNew) {
                    const hidN = document.createElement('input');
                    hidN.type = 'hidden';
                    hidN.setAttribute('data-field', 'new_item_name');
                    hidN.value = newName;
                    li.appendChild(hidN);

                    const hidC = document.createElement('input');
                    hidC.type = 'hidden';
                    hidC.setAttribute('data-field', 'new_item_category');
                    hidC.value = newCategory;
                    li.appendChild(hidC);
                }

                const hid2 = document.createElement('input');
                hid2.type = 'hidden';
                hid2.setAttribute('data-field', 'quantidade');
                hid2.value = quantidade;
                li.appendChild(hid2);

                const hid3 = document.createElement('input');
                hid3.type = 'hidden';
                hid3.setAttribute('data-field', 'unidade');
                hid3.value = unidade;
                li.appendChild(hid3);

                itemList.appendChild(li);
                renumberHiddenInputs();
            }

            if (itensearchInput) {
                itensearchInput.addEventListener('input', function () {
                    const term = this.value;
                    if (!term || term.length < 2) {
                        itensearchResults.style.display = 'none';
                        return;
                    }
                    renderSearchResults(filteritens(term));
                });
            }

            if (clearSelectedItemBtn) {
                clearSelectedItemBtn.addEventListener('click', function () {
                    clearSelectedItem();
                });
            }

            if (toggleNewItemBtn) {
                toggleNewItemBtn.addEventListener('click', function () {
                    if (newitensection.style.display === 'none') {
                        showNewitensection();
                    } else {
                        hideNewitensection();
                    }
                });
            }

            if (cancelNewItemBtn) {
                cancelNewItemBtn.addEventListener('click', function () {
                    hideNewitensection();
                });
            }

            if (newItemCategorySelect) {
                newItemCategorySelect.addEventListener('change', function (e) {
                    const cat = e.target.value || null;
                    if (cat) setUnitOptionsForCategory(cat);
                });
            }

            const addItemBtn = document.getElementById('add-item-btn');
            if (addItemBtn) {
                addItemBtn.addEventListener('click', function () {
                    const qtd = quantidadeInput.value;
                    const uni = unidadeSelect.value;

                    if (!qtd || Number(qtd) <= 0) return;

                    const creating = newitensection.style.display !== 'none';
                    if (creating) {
                        const name = newItemNameInput.value.trim();
                        const cat = newItemCategorySelect.value;
                        if (!name || !cat) return;
                        if (cat === 'alimento' && uni !== 'Kg') return;
                        addListRow({ itemId: null, nome: null, quantidade: qtd, unidade: uni, isNew: true, newName: name, newCategory: cat });
                        hideNewitensection();
                    } else {
                        const id = selectedItemIdInput.value;
                        if (!id) return;
                        const item = (itensData || []).find(i => String(i.id) === String(id));
                        const cat = item ? item.categoria : null;
                        if (cat === 'alimento' && uni !== 'Kg') return;
                        addListRow({ itemId: id, nome: item ? item.nome : 'Item', quantidade: qtd, unidade: uni, isNew: false });
                        clearSelectedItem();
                    }

                    quantidadeInput.value = '';
                    unidadeSelect.value = 'Unidade';
                });
            }

            const existingCloses = itemList.querySelectorAll('.btn-close');
            existingCloses.forEach(btn => {
                btn.addEventListener('click', function () {
                    const li = btn.closest('li');
                    li.remove();
                    if (!itemList.querySelector('li.list-group-item')) {
                        if (emptyItemList) emptyItemList.style.display = 'block';
                    }
                    renumberHiddenInputs();
                });
            });
        });
    </script>
@endpush