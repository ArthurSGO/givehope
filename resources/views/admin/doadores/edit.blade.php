@extends('app')
@section('title', 'Editar Doador')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    {{-- Título do Card --}}
                    <div class="card-header">Editando Doador: {{ $doador->nome }}</div>
                    <div class="card-body">
                        {{-- Exibe erros de validação, se houver --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Formulário de edição --}}
                        <form action="{{ route('doadores.update', $doador->id) }}" method="POST">
                            @csrf {{-- Token de segurança do Laravel --}}
                            @method('PUT') {{-- Método HTTP para atualização --}}

                            {{-- Campo Nome --}}
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome"
                                    name="nome" value="{{ old('nome', $doador->nome) }}" required>
                                @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Campo CPF/CNPJ --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cpf_cnpj" class="form-label">CPF ou CNPJ</label>
                                    <input type="text" class="form-control @error('cpf_cnpj') is-invalid @enderror"
                                        id="cpf_cnpj" name="cpf_cnpj" value="{{ old('cpf_cnpj', $doador->cpf_cnpj) }}">
                                    @error('cpf_cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Campo Telefone --}}
                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                        id="telefone" name="telefone" value="{{ old('telefone', $doador->telefone) }}">
                                    @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr>
                            <p class="text-muted">Endereço (Opcional na Edição Simples)</p>

                            {{-- Campos de Endereço (Adapte conforme necessidade) --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep"
                                        name="cep" value="{{ old('cep', $doador->cep) }}">
                                    @error('cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9 mb-3">
                                    <label for="logradouro" class="form-label">Logradouro</label>
                                    <input type="text" class="form-control @error('logradouro') is-invalid @enderror"
                                        id="logradouro" name="logradouro" value="{{ old('logradouro', $doador->logradouro) }}">
                                    @error('logradouro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="numero" class="form-label">Número</label>
                                    <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                        id="numero" name="numero" value="{{ old('numero', $doador->numero) }}">
                                    @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                        id="cidade" name="cidade" value="{{ old('cidade', $doador->cidade) }}">
                                    @error('cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <input type="text" class="form-control @error('estado') is-invalid @enderror"
                                        id="estado" name="estado" value="{{ old('estado', $doador->estado) }}">
                                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>


                            {{-- Botões --}}
                            <div class="mt-4">
                                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                <a href="{{ route('doadores.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para máscaras (opcional, mas recomendado) --}}
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script>
            $(document).ready(function () {
                var cpfCnpjMask = function (val) { return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009'; };
                $('#cpf_cnpj').mask(cpfCnpjMask, { onKeyPress: function (val, e, field, options) { field.mask(cpfCnpjMask.apply({}, arguments), options); } });
                var phoneMask = function (val) { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; };
                $('#telefone').mask(phoneMask, { onKeyPress: function (val, e, field, options) { field.mask(phoneMask.apply({}, arguments), options); } });
                $('#cep').mask('00000-000');
                // Script para buscar CEP pode ser adicionado aqui, se necessário, similar ao create.blade.php
            });
        </script>
    @endpush
@endsection