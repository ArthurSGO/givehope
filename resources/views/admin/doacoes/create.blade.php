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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_doacao" class="form-label">Data da Doação</label>
                                <input type="date" class="form-control" id="data_doacao" name="data_doacao"
                                    value="{{ old('data_doacao', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="doador_id" class="form-label">Doador</label>
                            <select class="form-select" id="doador_id" name="doador_id">
                                <option value="" selected>Doação Anônima</option>
                                @foreach ($doadores as $doador)
                                <option value="{{ $doador->id }}" {{ old('doador_id') == $doador->id ? 'selected' : '' }}>
                                    {{ $doador->nome }} - ({{ $doador->cpf_cnpj ?? 'Não informado' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Selecione um doador ou deixe como anônimo.</small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo de Doação</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="dinheiro" {{ old('tipo') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="item" {{ old('tipo') == 'item' ? 'selected' : '' }}>Item</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quantidade" class="form-label">Quantidade</label>
                                <input type="number" step="0.01" class="form-control" id="quantidade" name="quantidade" 
                                value="{{ old('quantidade') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="unidade" class="form-label">Unidade de Medida</label>
                            <select class="form-select" id="unidade" name="unidade" required>
                                <option value="R$" {{ old('unidade') == 'R$' ? 'selected' : '' }}>R$ (Reais)</option>
                                <option value="Unidade" {{ old('unidade') == 'Unidade' ? 'selected' : '' }}>Unidade(s)</option>
                                <option value="Kg" {{ old('unidade') == 'Kg' ? 'selected' : '' }}>Kg (Quilos)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (Opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                            <small class="form-text text-muted">Ex: "Cesta básica", "Material de construção", etc.</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Registrar Doação</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection