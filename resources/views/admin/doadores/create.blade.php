@extends('app')
@section('title', 'Cadastrar Novo Doador')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cadastrar Novo Doador') }}</div>
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

                    <form action="{{ route('doadores.store') }}" method="POST">
                        @csrf
                        
                        @if (request('redirect_to'))
                            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                        @endif

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}" required>
                             @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cpf_cnpj" class="form-label">CPF ou CNPJ</label>
                                <input type="text" class="form-control @error('cpf_cnpj') is-invalid @enderror" id="cpf_cnpj" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}">
                                @error('cpf_cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone') }}">
                                @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <hr>
                        <p class="text-muted">Endereço</p>

                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label for="cep" class="form-label">CEP</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep" value="{{ old('cep') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="buscar-cep-btn">Buscar CEP</button>
                                </div>
                                 @error('cep') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div id="cep-error" class="text-danger small mt-1" style="display: none;"></div>
                             </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <label for="logradouro" class="form-label">Logradouro</label>
                                <input type="text" class="form-control @error('logradouro') is-invalid @enderror" id="logradouro" name="logradouro" value="{{ old('logradouro') }}">
                                @error('logradouro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero" value="{{ old('numero') }}">
                                @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade" value="{{ old('cidade') }}">
                                @error('cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" value="{{ old('estado') }}">
                                @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Cadastrar Doador</button>
                            <a href="{{ request('redirect_to', route('doadores.index')) }}" class="btn btn-secondary">Cancelar</a>
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
$(document).ready(function(){
    var cpfCnpjMask = function (val) { return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009'; };
    $('#cpf_cnpj').mask(cpfCnpjMask, { onKeyPress: function(val, e, field, options) { field.mask(cpfCnpjMask.apply({}, arguments), options); } });
    var phoneMask = function (val) { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; };
    $('#telefone').mask(phoneMask, { onKeyPress: function(val, e, field, options) { field.mask(phoneMask.apply({}, arguments), options); } });

    $('#cep').mask('00000-000');

    $('#buscar-cep-btn').on('click', function() {
        const cepInput = $('#cep');
        const cep = cepInput.val().replace(/\D/g, '');
        const errorDiv = $('#cep-error');
        const btn = $(this);

        errorDiv.hide().text('');

        if (cep.length !== 8) {
            errorDiv.text('CEP inválido. Deve conter 8 dígitos.').show();
            cepInput.addClass('is-invalid');
            return;
        }

        cepInput.removeClass('is-invalid');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...');

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
                $('#logradouro').val(data.logradouro);
                $('#cidade').val(data.localidade);
                $('#estado').val(data.uf);
                $('#numero').focus();
            })
            .catch(error => {
                console.error("Erro na busca de CEP:", error);
                errorDiv.text(error.message || 'Não foi possível buscar o CEP.').show();
                cepInput.addClass('is-invalid');
            })
            .finally(() => {
                btn.prop('disabled', false).html('Buscar CEP');
            });
    });
});
</script>
@endpush