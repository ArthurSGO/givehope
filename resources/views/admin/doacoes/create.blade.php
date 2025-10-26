@extends('app')
@section('title', 'Registrar doação')
@section('content')
<style>
    .form-control[type=number]::-webkit-outer-spin-button,
    .form-control[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .form-control[type=number] {
        -moz-appearance: textfield;
    }
</style>

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
                                <span id="anonima-aviso-item" class="text-danger ms-2" style="display: none;"></span>
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
                                <div id="doador-nao-encontrado" class="alert alert-warning mt-2" style="display: none;">
                                    <div id="doador-nao-encontrado-mensagem" class="mb-2">Doador não encontrado.</div>
                                    <button type="button" class="btn btn-sm btn-primary" id="abrir-modal-doador-btn">
                                        Cadastrar novo doador
                                    </button>
                                </div>
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
                                    <input type="hidden" name="unidade_dinheiro" id="unidade_dinheiro" value="R$">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="unidade" id="doacao_unidade_input">

                        <div id="item-donation-section" style="display: none;">
                            <div class="card bg-light p-3">
                                <h6 class="card-title">Adicionar Itens à Doação</h6>
                                <div class="row g-3 align-items-end position-relative">
                                    <div class="col-md-6 position-relative">
                                        <label for="item_search" class="form-label">Buscar Item</label>
                                        <input type="text" id="item_search" class="form-control" placeholder="Digite para buscar..." autocomplete="off">
                                        <input type="hidden" id="selected_item_id">
                                        <div id="item_search_results" class="list-group shadow position-absolute w-100" style="z-index: 1050; display: none; top: 100%; left: 0; max-height: 220px; overflow-y: auto;"></div>
                                        <div id="selected-item-info" class="alert alert-info mt-2 py-2 px-3 d-none">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span id="selected-item-name" class="fw-semibold"></span>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0" id="clear-selected-item">Remover</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-secondary w-100" id="toggle-new-item-btn">Cadastrar novo item</button>
                                    </div>

                                    <div class="col-md-12" id="new-item-section" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="new_item_name" class="form-label">Nome do Novo Item</label>
                                                <input type="text" id="new_item_name" class="form-control" placeholder="Digite o nome do item">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="new_item_category" class="form-label">Categoria</label>
                                                <select id="new_item_category" class="form-select">
                                                    <option value="">Selecione...</option>
                                                    <option value="alimento">Alimento</option>
                                                    <option value="outro">Outro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-link text-decoration-none text-danger px-0" id="cancel-new-item-btn">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="item_quantidade" class="form-label">Qtd.</label>
                                        <input type="number" id="item_quantidade" class="form-control text-decoration-none" step="0.1">
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
                    <div class="modal fade" id="novoDoadorModal" tabindex="-1" aria-labelledby="novoDoadorModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="novoDoadorModalLabel">Cadastrar novo doador</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="novo-doador-errors" class="alert alert-danger d-none" role="alert"></div>
                                    <form id="novo-doador-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="modal_nome" class="form-label">Nome completo</label>
                                            <input type="text" class="form-control" id="modal_nome" name="nome" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_cpf_cnpj" class="form-label">CPF ou CNPJ</label>
                                                <input type="text" class="form-control" id="modal_cpf_cnpj" name="cpf_cnpj">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_telefone" class="form-label">Telefone</label>
                                                <input type="text" class="form-control" id="modal_telefone" name="telefone">
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="text-muted">Endereço</p>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="modal_cep" class="form-label">CEP</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="modal_cep" name="cep">
                                                    <button class="btn btn-outline-secondary" type="button" id="modal_buscar_cep_btn">Buscar CEP</button>
                                                </div>
                                                <div id="modal_cep_error" class="text-danger small mt-1" style="display: none;"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-9 mb-3">
                                                <label for="modal_logradouro" class="form-label">Logradouro</label>
                                                <input type="text" class="form-control" id="modal_logradouro" name="logradouro">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="modal_numero" class="form-label">Número</label>
                                                <input type="text" class="form-control" id="modal_numero" name="numero">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_cidade" class="form-label">Cidade</label>
                                                <input type="text" class="form-control" id="modal_cidade" name="cidade">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_estado" class="form-label">Estado</label>
                                                <input type="text" class="form-control" id="modal_estado" name="estado">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" form="novo-doador-form" class="btn btn-primary" id="novo-doador-submit-btn">Cadastrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<div id="preloaded-items-data" data-items='@json($itemsData)' class="d-none"></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function() {
        var maskBehavior = function(val) {
            return val.replace(/\D/g, '').length === 14 ? '00.000.000/0000-00' : '000.000.000-00999';
        };
        var phoneMask = function(val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        };
        $('#cpf_cnpj_busca').mask(maskBehavior, {
            onKeyPress: function(val, e, field, options) {
                field.mask(maskBehavior.apply({}, arguments), options);
            }
        });
        $('#modal_cpf_cnpj').mask(maskBehavior, {
            onKeyPress: function(val, e, field, options) {
                field.mask(maskBehavior.apply({}, arguments), options);
            }
        });
        $('#modal_telefone').mask(phoneMask, {
            onKeyPress: function(val, e, field, options) {
                field.mask(phoneMask.apply({}, arguments), options);
            }
        });
        $('#modal_cep').mask('00000-000');

        const tipoSelect = document.getElementById('tipo');
        const moneySection = document.getElementById('money-donation-section');
        const itemSection = document.getElementById('item-donation-section');
        const anonimaCheckbox = document.getElementById('doacao_anonima_checkbox');
        const anonimaCheckWrapper = anonimaCheckbox.closest('.form-check');
        const doadorSearchSection = document.getElementById('doador-search-section');
        const doadorIdInput = document.getElementById('doador_id');
        const submitButton = document.getElementById('submit-button');
        const itemOption = tipoSelect.querySelector('option[value="item"]');
        const itemsData = @json($itemsData);
        const itemSearchInput = document.getElementById('item_search');
        const itemSearchResults = document.getElementById('item_search_results');
        const selectedItemIdInput = document.getElementById('selected_item_id');
        const selectedItemInfo = document.getElementById('selected-item-info');
        const selectedItemName = document.getElementById('selected-item-name');
        const clearSelectedItemBtn = document.getElementById('clear-selected-item');
        const toggleNewItemBtn = document.getElementById('toggle-new-item-btn');
        const newItemSection = document.getElementById('new-item-section');
        const newItemNameInput = document.getElementById('new_item_name');
        const newItemCategorySelect = document.getElementById('new_item_category');
        const cancelNewItemBtn = document.getElementById('cancel-new-item-btn');
        const unidadeSelect = document.getElementById('item_unidade');
        selectedItemIdInput.dataset.category = selectedItemIdInput.dataset.category || '';
        const quantidadeDinheiroInput = document.getElementById('quantidade_dinheiro');
        const buscarBtn = document.getElementById('buscar-doador-btn');
        const doadorEncontradoDiv = document.getElementById('doador-encontrado');
        const doadorNaoEncontradoDiv = document.getElementById('doador-nao-encontrado');
        const doadorNaoEncontradoMensagem = document.getElementById('doador-nao-encontrado-mensagem');
        const doadorNomeSpan = document.getElementById('doador-nome');
        const cpfInput = document.getElementById('cpf_cnpj_busca');
        const limparDoadorBtn = document.getElementById('limpar-doador-btn');
        const abrirModalBtn = document.getElementById('abrir-modal-doador-btn');
        const novoDoadorModalEl = document.getElementById('novoDoadorModal');
        const bootstrapModal = window.bootstrap && window.bootstrap.Modal ? window.bootstrap.Modal : null;
        const fallbackModal = {
            show() {
                novoDoadorModalEl.classList.add('show');
                novoDoadorModalEl.style.display = 'block';
                novoDoadorModalEl.removeAttribute('aria-hidden');
                novoDoadorModalEl.setAttribute('aria-modal', 'true');
                novoDoadorModalEl.removeAttribute('inert');
                modalNomeInput.focus();
            },
            hide() {
                novoDoadorModalEl.classList.remove('show');
                novoDoadorModalEl.style.display = 'none';
                novoDoadorModalEl.setAttribute('aria-hidden', 'true');
                novoDoadorModalEl.removeAttribute('aria-modal');
                novoDoadorModalEl.setAttribute('inert', 'true');
                resetNovoDoadorForm();
            }
        };
        const novoDoadorModal = bootstrapModal ? bootstrapModal.getOrCreateInstance(novoDoadorModalEl) : fallbackModal;
        const novoDoadorForm = document.getElementById('novo-doador-form');
        const novoDoadorErrors = document.getElementById('novo-doador-errors');
        const novoDoadorSubmitBtn = document.getElementById('novo-doador-submit-btn');
        const modalCpfInput = document.getElementById('modal_cpf_cnpj');
        const modalTelefoneInput = document.getElementById('modal_telefone');
        const modalNomeInput = document.getElementById('modal_nome');
        const modalCepInput = document.getElementById('modal_cep');
        const modalLogradouroInput = document.getElementById('modal_logradouro');
        const modalNumeroInput = document.getElementById('modal_numero');
        const modalCidadeInput = document.getElementById('modal_cidade');
        const modalEstadoInput = document.getElementById('modal_estado');
        const modalBuscarCepBtn = document.getElementById('modal_buscar_cep_btn');
        const modalCepError = document.getElementById('modal_cep_error');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function toggleDonationSections() {
            const isDinheiro = tipoSelect.value === 'dinheiro';
            moneySection.style.display = isDinheiro ? 'block' : 'none';
            itemSection.style.display = isDinheiro ? 'none' : 'block';
            anonimaCheckWrapper.style.display = isDinheiro ? 'block' : 'none';

            quantidadeDinheiroInput.required = isDinheiro;
            quantidadeDinheiroInput.disabled = !isDinheiro;

            if (!isDinheiro) {
                anonimaCheckbox.checked = false;
            }
        }

        function updateFormState() {
            const isAnonima = anonimaCheckbox.checked;
            const doadorSelecionado = doadorIdInput.value !== '';
            const isDinheiro = tipoSelect.value === 'dinheiro';

            doadorSearchSection.style.display = isAnonima ? 'none' : 'block';

            itemOption.disabled = isAnonima;
            if (isAnonima && !isDinheiro) {
                tipoSelect.value = 'dinheiro';
            }

            toggleDonationSections();

            submitButton.disabled = !((isAnonima && isDinheiro) || (!isAnonima && doadorSelecionado));
        }

        const addItemBtn = document.getElementById('add-item-btn');
        const itemList = document.getElementById('item-list');
        const emptyItemList = document.getElementById('empty-item-list');
        let itemCounter = 0;

        let creatingNewItem = false;

        function sanitizeForAttribute(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/'/g, '&#39;');
        }

        function setUnitOptionsForCategory(category) {
            if (!unidadeSelect) {
                return;
            }
            const options = Array.from(unidadeSelect.options);
            if (category === 'alimento') {
                options.forEach(option => {
                    option.disabled = option.value !== 'Kg';
                });
                unidadeSelect.value = 'Kg';
            } else {
                options.forEach(option => option.disabled = false);
                if (!['Unidade', 'Kg'].includes(unidadeSelect.value)) {
                    unidadeSelect.value = 'Unidade';
                }
            }
        }

        function hideSearchResults() {
            itemSearchResults.style.display = 'none';
            itemSearchResults.innerHTML = '';
        }

        function renderSearchResults(query) {
            const normalizedQuery = query.trim().toLowerCase();
            if (!normalizedQuery) {
                hideSearchResults();
                return;
            }

            const results = itemsData.filter(item => item.nome.toLowerCase().includes(normalizedQuery)).slice(0, 8);
            itemSearchResults.innerHTML = '';

            if (results.length === 0) {
                const emptyItem = document.createElement('div');
                emptyItem.className = 'list-group-item disabled';
                emptyItem.textContent = 'Nenhum item encontrado.';
                itemSearchResults.appendChild(emptyItem);
            } else {
                results.forEach(item => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.textContent = item.nome;
                    button.addEventListener('click', function() {
                        selectExistingItem(item);
                    });
                    itemSearchResults.appendChild(button);
                });
            }

            itemSearchResults.style.display = 'block';
        }

        function clearSelectedItem() {
            selectedItemIdInput.value = '';
            selectedItemIdInput.dataset.category = '';
            selectedItemInfo.classList.add('d-none');
            selectedItemName.textContent = '';
            if (!creatingNewItem) {
                itemSearchInput.value = '';
            }
            setUnitOptionsForCategory(null);
        }

        function hideNewItemSection() {
            creatingNewItem = false;
            newItemSection.style.display = 'none';
            toggleNewItemBtn.textContent = 'Cadastrar novo item';
            itemSearchInput.disabled = false;
            newItemNameInput.value = '';
            newItemCategorySelect.value = '';
            if (selectedItemIdInput.value === 'new') {
                selectedItemIdInput.value = '';
            }
            setUnitOptionsForCategory(selectedItemIdInput.dataset.category || null);
            hideSearchResults();
        }

        function selectExistingItem(item) {
            if (!item) {
                return;
            }
            hideNewItemSection();
            clearSelectedItem();
            selectedItemIdInput.value = item.id;
            selectedItemIdInput.dataset.category = item.categoria || '';
            selectedItemInfo.classList.remove('d-none');
            selectedItemName.textContent = item.nome;
            itemSearchInput.value = item.nome;
            setUnitOptionsForCategory(item.categoria || null);
            hideSearchResults();
        }

        function showNewItemSection() {
            clearSelectedItem();
            hideSearchResults();
            creatingNewItem = true;
            newItemSection.style.display = 'block';
            toggleNewItemBtn.textContent = 'Usar item existente';
            itemSearchInput.disabled = true;
            selectedItemIdInput.value = 'new';
            setUnitOptionsForCategory(newItemCategorySelect.value || null);
            newItemNameInput.focus();
        }

        if (itemSearchInput) {
            itemSearchInput.addEventListener('input', function(event) {
                if (creatingNewItem) {
                    return;
                }
                if (selectedItemIdInput.value) {
                    clearSelectedItem();
                }
                renderSearchResults(event.target.value);
            });

            itemSearchInput.addEventListener('focus', function(event) {
                if (!creatingNewItem && event.target.value.trim()) {
                    renderSearchResults(event.target.value);
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (!itemSearchResults.contains(event.target) && event.target !== itemSearchInput) {
                hideSearchResults();
            }
        });

        if (clearSelectedItemBtn) {
            clearSelectedItemBtn.addEventListener('click', function() {
                clearSelectedItem();
                hideSearchResults();
                itemSearchInput.focus();
            });
        }

        if (toggleNewItemBtn) {
            toggleNewItemBtn.addEventListener('click', function() {
                if (creatingNewItem) {
                    hideNewItemSection();
                } else {
                    showNewItemSection();
                }
            });
        }

        if (cancelNewItemBtn) {
            cancelNewItemBtn.addEventListener('click', function() {
                hideNewItemSection();
                clearSelectedItem();
            });
        }

        if (newItemCategorySelect) {
            newItemCategorySelect.addEventListener('change', function(event) {
                if (creatingNewItem) {
                    setUnitOptionsForCategory(event.target.value || null);
                }
            });
        }

        addItemBtn.addEventListener('click', function() {
            const quantidadeInput = document.getElementById('item_quantidade');
            const quantidade = quantidadeInput.value;
            const unidade = unidadeSelect.value;
            const isCreatingNewItem = creatingNewItem;
            let itemId = isCreatingNewItem ? 'new' : selectedItemIdInput.value;
            let itemName = '';
            let newItemName = '';
            let newItemCategory = '';
            let itemCategory = '';

            if (isCreatingNewItem) {
                newItemName = newItemNameInput.value.trim();
                newItemCategory = newItemCategorySelect.value;
                if (!newItemName) {
                    alert('Por favor, digite o nome do novo item.');
                    return;
                }
                if (!newItemCategory) {
                    alert('Selecione a categoria do novo item.');
                    return;
                }
                itemName = newItemName;
                itemCategory = newItemCategory;
            } else if (itemId) {
                const selectedItem = itemsData.find(item => String(item.id) === String(itemId));
                itemName = selectedItem ? selectedItem.nome : itemSearchInput.value.trim();
                itemCategory = selectedItem ? (selectedItem.categoria || '') : '';
            } else {
                alert('Por favor, selecione um item ou cadastre um novo.');
                return;
            }

            if (!quantidade || parseFloat(quantidade) <= 0) {
                alert('Por favor, informe uma quantidade válida.');
                return;
            }

            if ((itemCategory === 'alimento' || newItemCategory === 'alimento') && unidade !== 'Kg') {
                alert('Itens da categoria "Alimento" devem ser cadastrados em Kg.');
                return;
            }

            if (emptyItemList) {
                emptyItemList.style.display = 'none';
            }

            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';

            const infoSpan = document.createElement('span');
            infoSpan.appendChild(document.createTextNode(`${itemName} - `));
            const quantityStrong = document.createElement('strong');
            quantityStrong.textContent = `${quantidade} ${unidade}`;
            infoSpan.appendChild(quantityStrong);
            listItem.appendChild(infoSpan);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn-close';
            removeButton.setAttribute('aria-label', 'Remove');
            removeButton.addEventListener('click', function() {
                listItem.remove();
                if (itemList.children.length === 1) {
                    emptyItemList.style.display = 'block';
                }
            });

            listItem.appendChild(removeButton);

            let hiddenInputs = '';
            hiddenInputs += `<input type="hidden" name="items[${itemCounter}][item_id]" value="${sanitizeForAttribute(itemId)}">`;
            hiddenInputs += `<input type="hidden" name="items[${itemCounter}][new_item_name]" value="${sanitizeForAttribute(isCreatingNewItem ? newItemName : '')}">`;
            if (isCreatingNewItem) {
                hiddenInputs += `<input type="hidden" name="items[${itemCounter}][new_item_category]" value="${sanitizeForAttribute(newItemCategory)}">`;
            }
            hiddenInputs += `<input type="hidden" name="items[${itemCounter}][quantidade]" value="${sanitizeForAttribute(quantidade)}">`;
            hiddenInputs += `<input type="hidden" name="items[${itemCounter}][unidade]" value="${sanitizeForAttribute(unidade)}">`;
            listItem.insertAdjacentHTML('beforeend', hiddenInputs);

            itemList.appendChild(listItem);
            if (isCreatingNewItem) {
                hideNewItemSection();
            } else {
                clearSelectedItem();
            }
            quantidadeInput.value = '';
            unidadeSelect.value = 'Unidade';
            setUnitOptionsForCategory(selectedItemIdInput.dataset.category || null);
            itemCounter++;
        });

        tipoSelect.addEventListener('change', updateFormState);
        anonimaCheckbox.addEventListener('change', updateFormState);

        function preencherDoador(doador) {
            if (!doador) {
                return;
            }
            if (doador.cpf_cnpj) {
                $('#cpf_cnpj_busca').val(doador.cpf_cnpj).trigger('input');
            }
            doadorNomeSpan.textContent = doador.nome;
            doadorIdInput.value = doador.id;
            doadorEncontradoDiv.style.display = 'block';
            doadorNaoEncontradoDiv.style.display = 'none';
            updateFormState();
        }

        function limparDoador() {
            doadorEncontradoDiv.style.display = 'none';
            doadorNaoEncontradoDiv.style.display = 'none';
            cpfInput.value = '';
            doadorIdInput.value = '';
            updateFormState();
        }

        limparDoadorBtn.addEventListener('click', limparDoador);

        function resetNovoDoadorForm() {
            novoDoadorForm.reset();
            $(modalCpfInput).val('').trigger('input');
            $(modalTelefoneInput).val('').trigger('input');
            $(modalCepInput).val('').trigger('input');
            novoDoadorErrors.classList.add('d-none');
            novoDoadorErrors.innerHTML = '';
            modalCepError.style.display = 'none';
            modalCepError.textContent = '';
            modalCepInput.classList.remove('is-invalid');
        }

        function preencherEndereco(dados) {
            if (!dados) {
                return;
            }
            if (dados.logradouro) {
                modalLogradouroInput.value = dados.logradouro;
            }
            if (dados.localidade) {
                modalCidadeInput.value = dados.localidade;
            }
            if (dados.uf) {
                modalEstadoInput.value = dados.uf;
            }
            modalNumeroInput.focus();
        }

        function buscarCepModal() {
            const cep = modalCepInput.value.replace(/\D/g, '');
            modalCepError.style.display = 'none';
            modalCepError.textContent = '';

            if (cep.length !== 8) {
                modalCepError.textContent = 'CEP inválido. Deve conter 8 dígitos.';
                modalCepError.style.display = 'block';
                modalCepInput.classList.add('is-invalid');
                return;
            }

            modalCepInput.classList.remove('is-invalid');
            modalBuscarCepBtn.disabled = true;
            modalBuscarCepBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao buscar CEP. Verifique a conexão.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.erro) {
                        throw new Error('CEP não encontrado.');
                    }
                    modalCepInput.classList.remove('is-invalid');
                    preencherEndereco(data);
                })
                .catch(error => {
                    console.error('Erro na busca de CEP:', error);
                    modalCepError.textContent = error.message || 'Não foi possível buscar o CEP.';
                    modalCepError.style.display = 'block';
                    modalCepInput.classList.add('is-invalid');
                })
                .finally(() => {
                    modalBuscarCepBtn.disabled = false;
                    modalBuscarCepBtn.innerHTML = 'Buscar CEP';
                });
        }

        if (modalBuscarCepBtn) {
            modalBuscarCepBtn.addEventListener('click', buscarCepModal);
        }

        if (modalCepInput) {
            modalCepInput.addEventListener('input', function() {
                modalCepInput.classList.remove('is-invalid');
                modalCepError.style.display = 'none';
                modalCepError.textContent = '';
            });
        }

        if (abrirModalBtn) {
            abrirModalBtn.addEventListener('click', function() {
                resetNovoDoadorForm();
                $(modalCpfInput).val(cpfInput.value).trigger('input');
                novoDoadorModal.show();
            });
        }

        if (bootstrapModal) {
            novoDoadorModalEl.addEventListener('shown.bs.modal', function() {
                modalNomeInput.focus();
            });

            novoDoadorModalEl.addEventListener('hidden.bs.modal', function() {
                resetNovoDoadorForm();
            });
        }

        novoDoadorForm.addEventListener('submit', function(event) {
            event.preventDefault();
            novoDoadorErrors.classList.add('d-none');
            novoDoadorErrors.innerHTML = '';

            const formData = new FormData(novoDoadorForm);

            novoDoadorSubmitBtn.disabled = true;
            novoDoadorSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cadastrando...';

            fetch(`{{ route('doadores.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 422) {
                            return response.json().then(data => {
                                throw {
                                    type: 'validation',
                                    errors: data.errors || {}
                                };
                            });
                        }
                        throw new Error('Não foi possível cadastrar o doador.');
                    }
                    return response.json();
                })
                .then(data => {
                    const doador = data.doador || data;
                    if (doador && doador.cpf_cnpj) {
                        $('#cpf_cnpj_busca').val(doador.cpf_cnpj).trigger('input');
                    }
                    preencherDoador(doador);
                    novoDoadorModal.hide();
                })
                .catch(error => {
                    if (error.type === 'validation') {
                        const messages = Object.values(error.errors).flat();
                        if (messages.length) {
                            novoDoadorErrors.innerHTML = messages.map(message => `<div>${message}</div>`).join('');
                            novoDoadorErrors.classList.remove('d-none');
                        }
                    } else {
                        novoDoadorErrors.textContent = error.message || 'Não foi possível cadastrar o doador.';
                        novoDoadorErrors.classList.remove('d-none');
                    }
                })
                .finally(() => {
                    novoDoadorSubmitBtn.disabled = false;
                    novoDoadorSubmitBtn.innerHTML = 'Cadastrar';
                });
        });

        buscarBtn.addEventListener('click', function() {
            const cpfCnpj = cpfInput.value;
            doadorNaoEncontradoDiv.style.display = 'none';
            doadorEncontradoDiv.style.display = 'none';
            if (!cpfCnpj) {
                alert('Por favor, digite um CPF ou CNPJ para buscar.');
                return;
            }
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            fetch(`{{ route('doadores.buscar') }}?cpf_cnpj=${encodeURIComponent(cpfCnpj)}`)
                .then(response => {
                    if (response.status === 404) {
                        return response.json().then(() => {
                            throw {
                                type: 'not_found'
                            };
                        });
                    }
                    if (!response.ok) {
                        throw new Error('Falha na requisição ao servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    preencherDoador(data);
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    doadorIdInput.value = '';
                    if (error.type === 'not_found') {
                        doadorNaoEncontradoMensagem.textContent = 'Doador não encontrado. Cadastre um novo doador para continuar.';
                    } else {
                        doadorNaoEncontradoMensagem.textContent = 'Não foi possível buscar o doador. Tente novamente em instantes.';
                    }
                    doadorNaoEncontradoDiv.style.display = 'block';
                    updateFormState();
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = 'Buscar';
                });
        });

        setUnitOptionsForCategory(selectedItemIdInput.dataset.category || null);

        toggleDonationSections();
        updateFormState();
    });
</script>
@endpush