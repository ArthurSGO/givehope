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
        const itemSelect = document.getElementById('item_id');
        const newItemWrapper = document.getElementById('new-item-wrapper');
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
                itemName = newItemInput.value.trim();
                newItemName = itemName;
                if (!itemName) {
                    alert('Por favor, digite o nome do novo item.');
                    return;
                }
            } else if (itemId) {
                itemName = itemSelect.options[itemSelect.selectedIndex].text;
            } else {
                alert('Por favor, selecione um item ou cadastre um novo.');
                return;
            }

            const quantidade = quantidadeInput.value;
            const unidade = unidadeSelect.value;

            if (!quantidade || parseFloat(quantidade) <= 0) {
                alert('Por favor, informe uma quantidade válida.');
                return;
            }

            if (emptyItemList) {
                emptyItemList.style.display = 'none';
            }

            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            listItem.innerHTML = `
            <span>${itemName} - <strong>${quantidade} ${unidade}</strong></span>
            <button type="button" class="btn-close" aria-label="Remove"></button>
        `;

            let hiddenInputs;
            if (itemId === 'new') {
                hiddenInputs = `<input type="hidden" name="items[${itemCounter}][item_id]" value="new">`;
                hiddenInputs += `<input type="hidden" name="items[${itemCounter}][new_item_name]" value="${newItemName}">`;
            } else {
                hiddenInputs = `<input type="hidden" name="items[${itemCounter}][item_id]" value="${itemId}">`;
                hiddenInputs += `<input type="hidden" name="items[${itemCounter}][new_item_name]" value="">`;
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

        toggleDonationSections();
        updateFormState();
    });
</script>
@endpush