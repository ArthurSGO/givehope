@extends('app')
@section('title', 'Editar Paróquia')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editando Paróquia: {{ $paroquia->nome }}</div>
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

                    <form action="{{ route('paroquias.update', $paroquia->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" class="form-control" id="cnpj" name="cnpj" value="{{ old('cnpj', $paroquia->cnpj) }}" required>
                            <small id="cnpj-feedback" class="form-text text-muted"></small>
                        </div>

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Paróquia</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $paroquia->nome) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail de Contato</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $paroquia->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone', $paroquia->telefone) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="logradouro" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="logradouro" name="logradouro" value="{{ old('logradouro', $paroquia->logradouro) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" value="{{ old('numero', $paroquia->numero) }}">
                        </div>

                        <div class="mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" value="{{ old('cidade', $paroquia->cidade) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="estado" name="estado" value="{{ old('estado', $paroquia->estado) }}" required>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Salvar Alterações</button>
                            <a href="{{ route('paroquias.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const cnpjInput = document.getElementById('cnpj');
        if (!cnpjInput) return;

        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.substring(0, 14);

            if (value.length > 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{1,4}).*/, '$1.$2.$3/$4');
            } else if (value.length > 5) {
                value = value.replace(/^(\d{2})(\d{3})(\d{1,3}).*/, '$1.$2.$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{1,3}).*/, '$1.$2');
            }

            e.target.value = value;
        });

        cnpjInput.addEventListener('blur', function() {
            const cnpjValue = this.value;
            const feedback = document.getElementById('cnpj-feedback');

            const cnpj = cnpjValue.replace(/[^\d]/g, '');

            if (cnpj.length !== 14) {
                if (cnpj.length > 0) {
                    feedback.textContent = 'CNPJ inválido. Preencha os 14 dígitos.';
                } else {
                    feedback.textContent = '';
                }
                return;
            }

            feedback.textContent = 'Buscando dados do CNPJ...';
            feedback.style.color = 'blue';

            const apiUrl = `https://receitaws.com.br/v1/cnpj/${cnpj}`;
            const proxyUrl = `https://api.allorigins.win/raw?url=${encodeURIComponent(apiUrl)}`;

            fetch(proxyUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro de rede ao buscar o CNPJ.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === "ERROR") {
                        throw new Error(data.message);
                    }

                    feedback.textContent = 'Dados carregados com sucesso!';
                    feedback.style.color = 'green';
                    document.getElementById('nome').value = data.nome || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('telefone').value = (data.telefone || '').replace(/[^\d]/g, '');
                    document.getElementById('logradouro').value = data.logradouro || '';
                    document.getElementById('numero').value = data.numero || '';
                    document.getElementById('cidade').value = data.municipio || '';
                    document.getElementById('estado').value = data.uf || '';
                })
                .catch(error => {
                    feedback.textContent = `Erro: ${error.message}`;
                    feedback.style.color = 'red';
                    console.error('Falha ao buscar CNPJ:', error);
                });
        });
    });
</script>
@endpush
@endsection