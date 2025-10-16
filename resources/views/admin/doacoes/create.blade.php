@extends('app')
@section('title', 'Registrar doação')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Registrar Nova Doação</div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('doacoes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Data da Doação</label>
                            <p class="form-control-plaintext"><strong>{{ date('d/m/Y') }}</strong></p>
                            <input type="hidden" name="data_doacao" value="{{ date('Y-m-d') }}">
                        </div>
                        <hr>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="doacao_anonima_checkbox" name="doacao_anonima">
                                <label class="form-check-label" for="doacao_anonima_checkbox">É uma Doação Anônima?</label>
                            </div>
                            <div id="doador-search-section">
                                <label for="cpf_cnpj_busca" class="form-label">Buscar Doador por CPF/CNPJ</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cpf_cnpj_busca" placeholder="Digite o CPF ou CNPJ">
                                    <button class="btn btn-outline-secondary" type="button" id="buscar-doador-btn">Buscar</button>
                                </div>
                                <div id="doador-encontrado" class="mt-2" style="display: none;">
                                    <p class="mb-0"><strong>Doador:</strong> <span id="doador-nome"></span></p>
                                    <button type="button" class="btn btn-sm btn-link text-danger" id="limpar-doador-btn">Limpar</button>
                                </div>
                                <div id="doador-nao-encontrado" class="text-danger mt-2" style="display: none;">Doador não encontrado.</div>
                            </div>
                            <input type="hidden" id="doador_id" name="doador_id" value="">
                        </div>
                        <hr>

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Doação</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="dinheiro" selected>Dinheiro</option>
                                <option value="item">Item</option>
                            </select>
                        </div>

                        <div id="money-donation-section">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quantidade_dinheiro" class="form-label">Valor</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" class="form-control" id="quantidade_dinheiro" name="quantidade">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="item-donation-section" style="display: none;">
                            <div class="card bg-light p-3">
                                <h6 class="card-title">Adicionar Itens à Doação</h6>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label for="item_id" class="form-label">Item</label>
                                        <select id="item_id" class="form-select">
                                            <option value="">Selecione um item...</option>
                                            <option value="new">--- Cadastrar Novo Item ---</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-5" id="new-item-wrapper" style="display: none;">
                                        <label for="new_item_name" class="form-label">Nome do Novo Item</label>
                                        <input type="text" id="new_item_name" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="item_quantidade" class="form-label">Qtd.</label>
                                        <input type="number" id="item_quantidade" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="item_unidade" class="form-label">Un.</label>
                                        <select id="item_unidade" class="form-select">
                                            <option value="Unidade">Un</option>
                                            <option value="Kg">Kg</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" id="add-item-btn">Adicionar</button>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="card-title">Itens Adicionados</h6>
                                <ul id="item-list" class="list-group">
                                    <li class="list-group-item text-muted" id="empty-item-list">Nenhum item adicionado.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="descricao" class="form-label">Observações (Opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="submit-button">Registrar Doação</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    $('#cpf_cnpj_busca').mask(function(val) {
        return val.replace(/\D/g, '').length === 14 ? '00.000.000/0000-00' : '000.000.000-00999';
    }, { onKeyPress: function(val, e, field, options) { field.mask(this.mask.apply({}, arguments), options); } });

    const tipoSelect = document.getElementById('tipo');
    const moneySection = document.getElementById('money-donation-section');
    const itemSection = document.getElementById('item-donation-section');
    const anonimaCheckbox = document.getElementById('doacao_anonima_checkbox');
    const doadorSearchSection = document.getElementById('doador-search-section');
    const doadorIdInput = document.getElementById('doador_id');
    const submitButton = document.getElementById('submit-button');
    const itemOption = tipoSelect.querySelector('option[value="item"]');
    const itemSelect = document.getElementById('item_id');
    const newItemWrapper = document.getElementById('new-item-wrapper');

    function toggleDonationSections() {
        if (tipoSelect.value === 'dinheiro') {
            moneySection.style.display = 'block';
            itemSection.style.display = 'none';
        } else {
            moneySection.style.display = 'none';
            itemSection.style.display = 'block';
        }
    }

    function updateFormState() {
        const isAnonima = anonimaCheckbox.checked;
        const doadorSelecionado = doadorIdInput.value !== '';

        doadorSearchSection.style.display = isAnonima ? 'none' : 'block';
        itemOption.disabled = isAnonima;

        if (isAnonima) {
            tipoSelect.value = 'dinheiro';
            limparDoador();
        }
        
        toggleDonationSections();
        submitButton.disabled = !(isAnonima || doadorSelecionado);
    }

    itemSelect.addEventListener('change', function() {
        newItemWrapper.style.display = this.value === 'new' ? 'block' : 'none';
    });

    const addItemBtn = document.getElementById('add-item-btn');
    const itemList = document.getElementById('item-list');
    const emptyItemList = document.getElementById('empty-item-list');
    let itemCounter = 0;

    addItemBtn.addEventListener('click', function() {
        const quantidadeInput = document.getElementById('item_quantidade');
        const unidadeSelect = document.getElementById('item_unidade');
        
        let itemId = itemSelect.value;
        let itemName = '';
        let newItemName = '';

        if (itemId === 'new') {
            const newItemInput = document.getElementById('new_item_name');
            itemName = newItemInput.value;
            newItemName = newItemInput.value;
            if (!itemName) {
                alert('Por favor, digite o nome do novo item.');
                return;
            }
        } else {
            itemName = itemSelect.options[itemSelect.selectedIndex].text;
        }
        
        const quantidade = quantidadeInput.value;
        const unidade = unidadeSelect.value;

        if ((!itemId && !newItemName) || !quantidade) {
            alert('Por favor, selecione ou cadastre um item e informe a quantidade.');
            return;
        }

        if (emptyItemList) { emptyItemList.style.display = 'none'; }

        const listItem = document.createElement('li');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.innerHTML = `
            <span>${itemName} - <strong>${quantidade} ${unidade}</strong></span>
            <button type="button" class="btn-close" aria-label="Remove"></button>
        `;

        let hiddenInputs;
        if (itemId === 'new') {
            hiddenInputs = `<input type="hidden" name="items[${itemCounter}][new_item_name]" value="${newItemName}">`;
        } else {
            hiddenInputs = `<input type="hidden" name="items[${itemCounter}][item_id]" value="${itemId}">`;
        }
        hiddenInputs += `
            <input type="hidden" name="items[${itemCounter}][quantidade]" value="${quantidade}">
            <input type="hidden" name="items[${itemCounter}][unidade]" value="${unidade}">
        `;
        listItem.insertAdjacentHTML('beforeend', hiddenInputs);
        
        listItem.querySelector('.btn-close').addEventListener('click', function() {
            listItem.remove();
            if (itemList.children.length === 1) {
                emptyItemList.style.display = 'block';
            }
        });

        itemList.appendChild(listItem);

        itemSelect.value = '';
        newItemWrapper.style.display = 'none';
        document.getElementById('new_item_name').value = '';
        quantidadeInput.value = '';
        itemCounter++;
    });

    tipoSelect.addEventListener('change', toggleDonationSections);
    anonimaCheckbox.addEventListener('change', updateFormState);

    const buscarBtn = document.getElementById('buscar-doador-btn');
    const doadorEncontradoDiv = document.getElementById('doador-encontrado');
    const doadorNaoEncontradoDiv = document.getElementById('doador-nao-encontrado');
    const doadorNomeSpan = document.getElementById('doador-nome');
    const cpfInput = document.getElementById('cpf_cnpj_busca');
    function limparDoador() {
        doadorEncontradoDiv.style.display = 'none';
        doadorNaoEncontradoDiv.style.display = 'none';
        cpfInput.value = '';
        doadorIdInput.value = '';
        updateFormState();
    }
    document.getElementById('limpar-doador-btn').addEventListener('click', limparDoador);
    
    buscarBtn.addEventListener('click', function() {
        const cpfCnpj = cpfInput.value;
        doadorNaoEncontradoDiv.style.display = 'none';
        if (!cpfCnpj) {
            alert('Por favor, digite um CPF ou CNPJ para buscar.');
            return;
        }
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
        fetch(`{{ route('doadores.buscar') }}?cpf_cnpj=${encodeURIComponent(cpfCnpj)}`)
            .then(response => {
                if (response.ok) return response.json();
                throw new Error('Doador não encontrado.');
            })
            .then(data => {
                doadorNomeSpan.textContent = data.nome;
                doadorIdInput.value = data.id;
                doadorEncontradoDiv.style.display = 'block';
                updateFormState();
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                doadorNaoEncontradoDiv.style.display = 'block';
                doadorIdInput.value = '';
                updateFormState();
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = 'Buscar';
            });
    });

    toggleDonationSections();
    updateFormState();
});
</script>
@endpush