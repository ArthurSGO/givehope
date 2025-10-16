@extends('app')
@section('title', 'Editar Item')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editando Item: {{ $item->nome }}</div>
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

                    <form action="{{ route('items.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Item</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $item->nome) }}" required>
                            <small class="form-text text-muted">Ex: Arroz (5kg), Cesta Básica Média, etc.</small>
                        </div>

                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria (Opcional)</label>
                            <input type="text" class="form-control" id="categoria" name="categoria" value="{{ old('categoria', $item->categoria) }}">
                            <small class="form-text text-muted">Ex: Alimento não perecível, Higiene, Vestuário.</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection