@extends('app')
@section('title', 'Painel da Paróquia')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Painel da Paróquia: <strong>{{ Auth::user()->paroquia->nome ?? 'Paróquia não associada' }}</strong>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p>Bem-vindo, {{ Auth::user()->name }}!</p>

                    <hr>

                    <h4>Ações Rápidas</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('doacoes.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Registrar Nova Doação
                        </a>
                        <a href="{{ route('doacoes.index') }}" class="btn btn-secondary">
                            <i class="fa fa-list"></i> Ver Todas as Doações
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection