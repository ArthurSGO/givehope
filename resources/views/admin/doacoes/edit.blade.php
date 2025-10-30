@extends('app')
@section('title', 'Editar Doação #'.$doacao->id)
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editando Doação #{{ $doacao->id }}</div>
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

                    @if (!$isEditavel)
                    <div class="alert alert-warning" role="alert">
                        <i class="fa-solid fa-clock me-1"></i> <strong>Modo de edição restrita.</strong> O tempo para edição completa (15 minutos) expirou.
                    </div>
                    @else
                     <div class="alert alert-info" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Modo de edição completa.</strong> Alterar itens irá estornar o estoque <br><strong>(Você tem até 15 minutos após a criação da doação para editar)</strong>.
                    </div>
                    @endif

                    <form action="{{ route('doacoes.update', $doacao->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="data_doacao" class="form-label">Data da Doação</label>
                            <input type="date" class="form-control" name="data_doacao" value="{{ old('data_doacao', \Carbon\Carbon::parse($doacao->data_doacao)->format('Y-m-d')) }}">
                        </div>
                        <hr>

                        @php
                            $isAnonima = old('doacao_anonima', ($doacao->doador && $doacao->doador->nome === 'Anônimo'));
                        @endphp

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="doacao_anonima_checkbox" name="doacao_anonima" 
                                    @if($isAnonima) checked @endif
                                    @if($doacao->tipo === 'item' && !$isEditavel) disabled @endif>
                                <label class="form-check-label" for="doacao_anonima_checkbox">É uma Doação Anônima?</label>
                                <span id="anonima-aviso-item" class="text-danger ms-2" style="display: none;"></span>
                            </div>
                            
                            <div id="doador-search-section" style="@if($isAnonima) display: none; @endif">
                                <label for="cpf_cnpj_busca" class="form-label">Buscar Doador por CPF/CNPJ</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cpf_cnpj_busca" placeholder="Digite o CPF ou CNPJ" value="{{ old('cpf_cnpj_busca', $doacao->doador ? $doacao->doador->cpf_cnpj : '') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="buscar-doador-btn">Buscar</button>
                                </div>
                                <div id="doador-encontrado" class="mt-2" style="@if(!$isAnonima && $doacao->doador) display: block; @else display: none; @endif">
                                    <p class="mb-0"><strong>Doador:</strong> <span id="doador-nome">{{ $doacao->doador ? $doacao->doador->nome : '' }}</span></p>
                                    <button type="button" class="btn btn-sm btn-link text-danger" id="limpar-doador-btn">Limpar</button>
                                </div>
                                <div id="doador-nao-encontrado" class="alert alert-warning mt-2" style="display: none;">
                                    <div id="doador-nao-encontrado-mensagem" class="mb-2">Doador não encontrado.</div>
                                    <button type="button" class="btn btn-sm btn-primary" id="abrir-modal-doador-btn">
                                        Cadastrar novo doador
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="doador_id" name="doador_id" value="{{ old('doador_id', $doacao->doador_id) }}">
                        </div>
                        <hr>

                        @if ($isEditavel)
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de Doação</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="dinheiro" {{ old('tipo', $doacao->tipo) == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="item" {{ old('tipo', $doacao->tipo) == 'item' ? 'selected' : '' }}>Item</option>
                                </select>
                            </div>
                            
                            <div id="money-donation-section" style="{{ old('tipo', $doacao->tipo) == 'dinheiro' ? 'display: block;' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="quantidade_dinheiro" class="form-label">Valor</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" class="form-control" id="quantidade_dinheiro" name="quantidade" value="{{ old('quantidade', $doacao->quantidade) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="unidade" id="doacao_unidade_input">

                            <div id="item-donation-section" style="{{ old('tipo', $doacao->tipo) == 'item' ? 'display: block;' : 'display: none;' }}">
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
                                                        <option value="Alimento">Alimento</option>
                                                        <option value="Higiene">Higiene</option>
                                                        <option value="Limpeza">Limpeza</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-link text-decoration-none text-danger px-0" id="cancel-new-item-btn">Cancelar</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="item_quantidade" class="form-label">Qtd.</label>
                                            <input type="number" id="item_quantidade" class="form-control" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="item_unidade" class="form-label">Un.</label>
                                            <select id="item_unidade" class="form-select" disabled>
                                                <option value="">Selecione item</option>
                                                <option value="Un">Un</option>
                                                <option value="Kg">Kg</option>
                                                <option value="L">L</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary w-100" id="add-item-btn">Adicionar</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <h6 class="card-title">Itens Adicionados</h6>
                                    <div class="card">
                                        <ul id="item-list" class="list-group list-group-flush">
                                            @php $itemIndex = 0; @endphp
                                            @if(old('items'))
                                                @foreach(old('items') as $index => $item)
                                                    @php
                                                        $itemId = $item['item_id'];
                                                        $itemName = $item['new_item_name'] ?? ($itemsData->firstWhere('id', $itemId)['nome'] ?? 'Item');
                                                        $itemCat = $item['new_item_category'] ?? '';
                                                    @endphp
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $itemName }} - <strong>{{ $item['quantidade'] }} {{ $item['unidade'] }}</strong></span>
                                                        <button type="button" class="btn-close" aria-label="Remove"></button>
                                                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $itemId }}">
                                                        <input type="hidden" name="items[{{ $index }}][new_item_name]" value="{{ $item['new_item_name'] ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][new_item_category]" value="{{ $itemCat }}">
                                                        <input type="hidden" name="items[{{ $index }}][quantidade]" value="{{ $item['quantidade'] }}">
                                                        <input type="hidden" name="items[{{ $index }}][unidade]" value="{{ $item['unidade'] }}">
                                                    </li>
                                                    @php $itemIndex = $index + 1; @endphp
                                                @endforeach
                                            @else
                                                @foreach($itensAtuais as $item)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $item['nome'] }} - <strong>{{ $item['quantidade'] }} {{ $item['unidade'] }}</strong></span>
                                                        <button type="button" class="btn-close" aria-label="Remove"></button>
                                                        <input type="hidden" name="items[{{ $itemIndex }}][item_id]" value="{{ $item['id'] }}">
                                                        <input type="hidden" name="items[{{ $itemIndex }}][new_item_name]" value="">
                                                        <input type="hidden" name="items[{{ $itemIndex }}][new_item_category]" value="">
                                                        <input type="hidden" name="items[{{ $itemIndex }}][quantidade]" value="{{ $item['quantidade'] }}">
                                                        <input type="hidden" name="items[{{ $itemIndex }}][unidade]" value="{{ $item['unidade'] }}">
                                                    </li>
                                                    @php $itemIndex++; @endphp
                                                @endforeach
                                            @endif
                                            <li class="list-group-item text-muted" id="empty-item-list" @if($itemIndex > 0) style="display: none;" @endif>Nenhum item adicionado.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        
                        @else
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de Doação</label>
                                <input type="text" class="form-control" id="tipo" name="tipo_disabled" value="{{ ucfirst($doacao->tipo) }}" disabled>
                                <small class="form-text text-muted">O tipo da doação (Dinheiro ou Item) não pode ser alterado após 15 minutos.</small>
                            </div>

                            @if ($doacao->tipo === 'dinheiro')
                            <div id="money-donation-section">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="quantidade_dinheiro" class="form-label">Valor</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" class="form-control" id="quantidade_dinheiro" name="quantidade" value="{{ old('quantidade', $doacao->quantidade) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($doacao->tipo === 'item')
                            <div id="item-donation-section">
                                <label class="form-label">Itens da Doação</label>
                                <div class="card bg-light p-3">
                                    <p class="text-danger small mb-0"><i class="fa-solid fa-lock me-1"></i> Os itens de uma doação não podem ser editados após 15 minutos.</p>
                                </div>
                                <div class="card mt-3">
                                    <ul id="item-list" class="list-group list-group-flush">
                                        @forelse($doacao->items as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ $item->nome }}</span>
                                                <span class="fw-semibold">{{ $item->formatted_quantidade }} {{ $item->pivot->unidade }}</span>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-muted" id="empty-item-list">Nenhum item adicionado.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            @endif
                        @endif
                        
                        <div class="mb-3 mt-3">
                            <label for="descricao" class="form-label">Observações (Opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao', $doacao->descricao) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="submit-button">Salvar Alterações</button>
                            <a href="{{ route('doacoes.index') }}" class="btn btn-secondary">Cancelar</a>
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
        const itemOption = tipoSelect ? tipoSelect.querySelector('option[value="item"]') : null;
        const itemsDataEl = document.getElementById('preloaded-items-data');
        const itemsData = itemsDataEl ? JSON.parse(itemsDataEl.getAttribute('data-items')) : [];
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
            show() { novoDoadorModalEl.classList.add('show'); novoDoadorModalEl.style.display = 'block'; },
            hide() { novoDoadorModalEl.classList.remove('show'); novoDoadorModalEl.style.display = 'none'; resetNovoDoadorForm(); }
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
            if (!tipoSelect) return; 
            const isDinheiro = tipoSelect.value === 'dinheiro';
            moneySection.style.display = isDinheiro ? 'block' : 'none';
            if (itemSection) itemSection.style.display = isDinheiro ? 'none' : 'block';
            anonimaCheckWrapper.style.display = 'block'; 

            if (quantidadeDinheiroInput) {
                quantidadeDinheiroInput.required = isDinheiro;
                quantidadeDinheiroInput.disabled = !isDinheiro;
            }

            if (!isDinheiro && anonimaCheckbox.checked) {
                anonimaCheckbox.checked = false;
                updateDoadorSection();
            }
        }

        function updateDoadorSection() {
            const isAnonima = anonimaCheckbox.checked;
            doadorSearchSection.style.display = isAnonima ? 'none' : 'block';

            if (itemOption) {
                itemOption.disabled = isAnonima;
                if (isAnonima && tipoSelect.value === 'item') {
                    tipoSelect.value = 'dinheiro';
                    toggleDonationSections();
                }
            }
            if (isAnonima) {
                limparDoador();
                doadorIdInput.value = '';
            } else if (!doadorIdInput.value) {
                preencherDoador({ 
                    id: '{{ $doacao->doador_id }}', 
                    nome: '{{ $doacao->doador ? $doacao->doador->nome : '' }}', 
                    cpf_cnpj: '{{ $doacao->doador ? $doacao->doador->cpf_cnpj : '' }}' 
                });
            }
        }
        
        if (anonimaCheckbox) {
            anonimaCheckbox.addEventListener('change', updateDoadorSection);
        }

        @if ($isEditavel)
        let itemCounter = {{ $itemIndex }};
        let creatingNewItem = false;
        
        if (selectedItemIdInput) {
            selectedItemIdInput.dataset.category = selectedItemIdInput.dataset.category || '';
        }

        function sanitizeForAttribute(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/'/g, '&#39;');
        }

        function setUnitOptionsForCategory(category) {
            if (!unidadeSelect) return;
            
            unidadeSelect.innerHTML = ''; 
            unidadeSelect.disabled = true;

            if (!category) {
                unidadeSelect.innerHTML = '<option value="">Selecione item</option>';
                return;
            }

            let unidades = [];
            if (category === 'Alimento') {
                unidades = ['Un', 'Kg'];
            } else if (category === 'Limpeza') {
                unidades = ['Un', 'L'];
            } else {
                unidades = ['Un'];
            }

            unidades.forEach(unidade => {
                const option = document.createElement('option');
                option.value = unidade;
                option.textContent = unidade;
                unidadeSelect.appendChild(option);
            });
            
            unidadeSelect.disabled = false;
            
            if (category === 'Alimento') {
                unidadeSelect.value = 'Kg';
            } else {
                unidadeSelect.value = 'Un';
            }
        }

        function hideSearchResults() {
            if(itemSearchResults) itemSearchResults.style.display = 'none';
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
                itemSearchResults.innerHTML = '<div class="list-group-item disabled">Nenhum item encontrado.</div>';
            } else {
                results.forEach(item => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.textContent = item.nome;
                    button.dataset.itemId = item.id;
                    button.dataset.itemCategoria = item.categoria;
                    button.addEventListener('click', () => selectExistingItem(item));
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
            if (!item) return;
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
            itemSearchInput.value = '';
            selectedItemIdInput.value = 'new';
            setUnitOptionsForCategory(newItemCategorySelect.value || null);
            newItemNameInput.focus();
        }

        if (itemSearchInput) {
            itemSearchInput.addEventListener('input', e => {
                if (creatingNewItem) return;
                if (selectedItemIdInput.value) clearSelectedItem();
                renderSearchResults(e.target.value);
            });
            itemSearchInput.addEventListener('focus', e => {
                if (!creatingNewItem && e.target.value.trim()) {
                    renderSearchResults(e.target.value);
                }
            });
            document.addEventListener('click', e => {
                if (!itemSearchResults.contains(e.target) && e.target !== itemSearchInput) {
                    hideSearchResults();
                }
            });
        }
        
        if (clearSelectedItemBtn) clearSelectedItemBtn.addEventListener('click', () => {
             clearSelectedItem();
             hideSearchResults();
             itemSearchInput.focus();
        });
        
        if (toggleNewItemBtn) toggleNewItemBtn.addEventListener('click', () => {
            creatingNewItem ? hideNewItemSection() : showNewItemSection();
        });

        if (cancelNewItemBtn) cancelNewItemBtn.addEventListener('click', () => {
            hideNewItemSection();
            clearSelectedItem();
        });

        if (newItemCategorySelect) newItemCategorySelect.addEventListener('change', e => {
            if (creatingNewItem) setUnitOptionsForCategory(e.target.value || null);
        });

        const addItemBtn = document.getElementById('add-item-btn');
        const itemList = document.getElementById('item-list');
        const emptyItemList = document.getElementById('empty-item-list');

        if (addItemBtn) {
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
                    if (!newItemName) { alert('Digite o nome do novo item.'); return; }
                    if (!newItemCategory) { alert('Selecione a categoria do novo item.'); return; }
                    itemName = newItemName;
                    itemCategory = newItemCategory;
                } else if (itemId) {
                    const selectedItem = itemsData.find(item => String(item.id) === String(itemId));
                    itemName = selectedItem ? selectedItem.nome : itemSearchInput.value.trim();
                    itemCategory = selectedItem ? (selectedItem.categoria || '') : '';
                } else {
                    alert('Selecione um item ou cadastre um novo.'); return;
                }

                if (!quantidade || parseFloat(quantidade) <= 0) {
                    alert('Informe uma quantidade válida.'); return;
                }
                
                if (itemCategory === 'Alimento' && !['Un', 'Kg'].includes(unidade)) {
                    alert("A unidade '"+unidade+"' não é válida para Alimento. Use 'Un' ou 'Kg'."); return;
                }
                if (itemCategory === 'Higiene' && unidade !== 'Un') {
                    alert("A unidade '"+unidade+"' não é válida para Higiene. Use 'Un'."); return;
                }
                if (itemCategory === 'Limpeza' && !['Un', 'L'].includes(unidade)) {
                    alert("A unidade '"+unidade+"' não é válida para Limpeza. Use 'Un' ou 'L'."); return;
                }

                if (emptyItemList) emptyItemList.style.display = 'none';

                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';

                listItem.innerHTML = `
                    <span>
                        ${sanitizeForAttribute(itemName)} - <strong>${sanitizeForAttribute(quantidade)} ${sanitizeForAttribute(unidade)}</strong>
                    </span>
                    <button type="button" class="btn-close" aria-label="Remove"></button>
                    <input type="hidden" name="items[${itemCounter}][item_id]" value="${sanitizeForAttribute(itemId)}">
                    <input type="hidden" name="items[${itemCounter}][new_item_name]" value="${sanitizeForAttribute(isCreatingNewItem ? newItemName : '')}">
                    <input type="hidden" name="items[${itemCounter}][new_item_category]" value="${sanitizeForAttribute(isCreatingNewItem ? newItemCategory : '')}">
                    <input type="hidden" name="items[${itemCounter}][quantidade]" value="${sanitizeForAttribute(quantidade)}">
                    <input type="hidden" name="items[${itemCounter}][unidade]" value="${sanitizeForAttribute(unidade)}">
                `;
                
                listItem.querySelector('.btn-close').addEventListener('click', function() {
                    listItem.remove();
                    if (itemList.children.length === 1) {
                        emptyItemList.style.display = 'block';
                    }
                });

                itemList.appendChild(listItem);
                
                if (isCreatingNewItem) {
                    hideNewItemSection();
                } else {
                    clearSelectedItem();
                }
                quantidadeInput.value = '';
                setUnitOptionsForCategory(null);
                itemCounter++;
            });
        }
        
        if (itemList) {
            itemList.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-close')) {
                    e.target.closest('li').remove();
                    if (itemList.children.length === 1) {
                        emptyItemList.style.display = 'block';
                    }
                }
            });
        }
        @endif

        if (tipoSelect) {
            tipoSelect.addEventListener('change', toggleDonationSections);
        }

        function preencherDoador(doador) {
            if (!doador || !doador.nome || doador.nome === 'Anônimo') {
                 limparDoador();
                 return;
            }
            if (doador.cpf_cnpj) {
                $('#cpf_cnpj_busca').val(doador.cpf_cnpj).trigger('input');
            } else {
                 $('#cpf_cnpj_busca').val('').trigger('input');
            }
            doadorNomeSpan.textContent = doador.nome;
            doadorIdInput.value = doador.id;
            doadorEncontradoDiv.style.display = 'block';
            doadorNaoEncontradoDiv.style.display = 'none';
        }

        function limparDoador() {
            doadorEncontradoDiv.style.display = 'none';
            doadorNaoEncontradoDiv.style.display = 'none';
            cpfInput.value = '';
            doadorIdInput.value = '';
        }

        if (limparDoadorBtn) {
            limparDoadorBtn.addEventListener('click', limparDoador);
        }

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
            if (!dados) return;
            if (dados.logradouro) modalLogradouroInput.value = dados.logradouro;
            if (dados.localidade) modalCidadeInput.value = dados.localidade;
            if (dados.uf) modalEstadoInput.value = dados.uf;
            modalNumeroInput.focus();
        }

        if (modalBuscarCepBtn) {
            modalBuscarCepBtn.addEventListener('click', function() {
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
                        if (!response.ok) throw new Error('Erro ao buscar CEP.');
                        return response.json();
                    })
                    .then(data => {
                        if (data.erro) throw new Error('CEP não encontrado.');
                        modalCepInput.classList.remove('is-invalid');
                        preencherEndereco(data);
                    })
                    .catch(error => {
                        modalCepError.textContent = error.message || 'Não foi possível buscar o CEP.';
                        modalCepError.style.display = 'block';
                        modalCepInput.classList.add('is-invalid');
                    })
                    .finally(() => {
                        modalBuscarCepBtn.disabled = false;
                        modalBuscarCepBtn.innerHTML = 'Buscar CEP';
                    });
            });
        }
        
        if (modalCepInput) {
            modalCepInput.addEventListener('input', function() {
                modalCepInput.classList.remove('is-invalid');
                modalCepError.style.display = 'none';
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
            novoDoadorModalEl.addEventListener('shown.bs.modal', () => modalNomeInput.focus());
            novoDoadorModalEl.addEventListener('hidden.bs.modal', () => resetNovoDoadorForm());
        }

        if (novoDoadorForm) {
            novoDoadorForm.addEventListener('submit', function(event) {
                event.preventDefault();
                novoDoadorErrors.classList.add('d-none');
                novoDoadorErrors.innerHTML = '';
                const formData = new FormData(novoDoadorForm);
                novoDoadorSubmitBtn.disabled = true;
                novoDoadorSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cadastrando...';

                fetch(`{{ route('doadores.store') }}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 422) {
                            return response.json().then(data => { throw { type: 'validation', errors: data.errors || {} }; });
                        }
                        throw new Error('Não foi possível cadastrar o doador.');
                    }
                    return response.json();
                })
                .then(data => {
                    const doador = data.doador || data;
                    preencherDoador(doador);
                    novoDoadorModal.hide();
                })
                .catch(error => {
                    if (error.type === 'validation') {
                        const messages = Object.values(error.errors).flat();
                        novoDoadorErrors.innerHTML = messages.map(msg => `<div>${msg}</div>`).join('');
                    } else {
                        novoDoadorErrors.innerHTML = error.message || 'Erro desconhecido.';
                    }
                    novoDoadorErrors.classList.remove('d-none');
                })
                .finally(() => {
                    novoDoadorSubmitBtn.disabled = false;
                    novoDoadorSubmitBtn.innerHTML = 'Cadastrar';
                });
            });
        }

        if (buscarBtn) {
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
                            return response.json().then(() => { throw { type: 'not_found' }; });
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
                        doadorIdInput.value = '';
                        if (error.type === 'not_found') {
                            doadorNaoEncontradoMensagem.textContent = 'Doador não encontrado.';
                        } else {
                            doadorNaoEncontradoMensagem.textContent = 'Não foi possível buscar o doador.';
                        }
                        doadorNaoEncontradoDiv.style.display = 'block';
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = 'Buscar';
                    });
            });
        }
        
        @if(!$isAnonima && $doacao->doador)
            preencherDoador({ 
                id: '{{ $doacao->doador_id }}', 
                nome: '{{ $doacao->doador->nome }}', 
                cpf_cnpj: '{{ $doacao->doador->cpf_cnpj }}' 
            });
        @endif

        toggleDonationSections();
        updateDoadorSection();
    });
</script>
@endpush