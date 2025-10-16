@extends('app')
@section('title', 'Painel da Paróquia')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Painel da Paróquia: <strong>{{ Auth::user()->paroquia->nome ?? 'Não associada' }}</strong></h1>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" data-icon="➕">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ação Rápida</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Nova Doação</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('doacoes.create') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2" data-icon="📋">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Ação Rápida</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Todas as Doações</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('doacoes.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 card-icon-beneficiarios" data-icon="👥">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ação Rápida</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Beneficiários</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('beneficiarios.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
    <style>
        .card .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .card .border-left-secondary {
            border-left: 0.25rem solid #858796 !important;
        }

        .card a.stretched-link::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
        }

        .card {
            transition: all .2s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15) !important;
            z-index: 10;
        }

        .card::before {
            content: attr(data-icon);
            position: absolute;
            top: 70%;
            left: 62%;
            transform: translate(-50%, -50%);
            font-size: 6rem;
            color: rgba(0, 0, 0, 0.4);
            opacity: 0;
            transition: opacity .3s ease-in-out;
            z-index: -1;
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-icon-beneficiarios::before {
            top: 54%;
        }
    </style>
    @endpush

</div>
@endsection