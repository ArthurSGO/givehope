@extends('app')
@section('title', 'Início')

@section('content')
<div class="container my-5">
    <div class="row text-center mb-5">
        <div class="col">
            <h1 class="display-4">Bem-vindo ao GiveHope!</h1>
            <p class="lead">A plataforma que conecta a generosidade à necessidade.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        
        <div class="col">
            <a href="{{ route('soon') }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm" data-icon="🗓️">
                    <div class="card-body text-center">
                        <h5 class="card-title">Eventos Futuros</h5>
                        <p class="card-text">Confira as próximas campanhas e eventos de arrecadação agendados.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('inprogress') }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm" data-icon="🏃‍♂️">
                    <div class="card-body text-center">
                        <h5 class="card-title">Campanhas em Andamento</h5>
                        <p class="card-text">Veja as campanhas que estão ativas e saiba como você pode contribuir agora.</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col">
            <a href="{{ route('finished') }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm" data-icon="🏁">
                    <div class="card-body text-center">
                        <h5 class="card-title">Resultados Finalizados</h5>
                        <p class="card-text">Explore os resultados de campanhas passadas e o impacto que elas geraram.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="row justify-content-center text-center mt-5 pt-4 border-top">
        <div class="col-lg-8">
            <h2>Transparência é a Nossa Missão</h2>
            <p class="lead">Acompanhe o percurso de cada doação e veja os relatórios gerais de arrecadação do último mês.</p>
        </div>
    </div>

    <div class="row justify-content-center g-4 mt-2">

        <div class="col-lg-6">
            <a href="{{ route('seek') }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm" data-icon="🔍">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title">Consulte Sua Doação</h5>
                        <p class="card-text">Use seu CPF/CNPJ para rastrear o status da sua contribuição.</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-6">
            <a href="{{ route('reports.general') }}" class="text-decoration-none text-dark"> 
                <div class="card h-100 shadow-sm" data-icon="📊">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title">Relatórios Gerais</h5>
                        <p class="card-text">Veja os gráficos e estatísticas completas de arrecadação e distribuição.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card:hover {
        border-top: 0.25rem solid #4e73df !important;
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
        border-top: 0.25rem solid !important;
    }

    .card:hover {
        transform: scale(1.03); 
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
        z-index: 10;
    }

    .card::before {
        content: attr(data-icon);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 5rem; 
        color: rgba(0, 0, 0, 0.15); 
        opacity: 0;
        transition: opacity .3s ease-in-out;
        z-index: -1;
    }

    .card:hover::before {
        opacity: 1;
    }
</style>
@endpush
@endsection