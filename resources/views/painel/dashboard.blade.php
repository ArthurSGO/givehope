@extends('app')
@section('title', 'Painel da Paróquia')
@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-itens-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel da Paróquia:
                <strong>{{ $user->paroquia->nome_fantasia ?? 'Não associada' }}</strong>
            </h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $marqueeDuration = $inProgressEvents->count() ? max(18, $inProgressEvents->count() * 6) : 0;
        @endphp

        @if ($inProgressEvents->isNotEmpty())
            <div class="event-banner shadow-sm mb-4" style="--event-marquee-duration: {{ $marqueeDuration }}s;">
                <div class="event-banner__header">
                    <span class="event-banner__title">Eventos em andamento</span>
                    <span class="event-banner__subtitle">Veja rapidamente qual causa está ativa neste momento.</span>
                </div>
                <div class="event-banner__marquee px-3 pb-3">
                    <div class="event-banner__track">
                        <div class="event-banner__group">
                            @foreach ($inProgressEvents as $event)
                                <div class="event-banner__item">
                                    <span class="event-banner__bullet"></span>
                                    <span class="fw-semibold">{{ $event->title }}</span>
                                    <span class="event-banner__divider">•</span>
                                    @if ($event->highlight_needs)
                                        <span class="event-banner__meta">Foco: {{ $event->highlight_needs }}</span>
                                    @elseif ($event->tagline)
                                        <span class="event-banner__meta">{{ $event->tagline }}</span>
                                    @elseif ($event->location)
                                        <span class="event-banner__meta">{{ $event->location }}</span>
                                    @elseif ($event->start_date)
                                        <span class="event-banner__meta">Desde {{ $event->start_date->format('d/m') }}</span>
                                    @else
                                        <span class="event-banner__meta">Em andamento</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="event-banner__group" aria-hidden="true">
                            @foreach ($inProgressEvents as $event)
                                <div class="event-banner__item">
                                    <span class="event-banner__bullet"></span>
                                    <span class="fw-semibold">{{ $event->title }}</span>
                                    <span class="event-banner__divider">•</span>
                                    @if ($event->highlight_needs)
                                        <span class="event-banner__meta">Foco: {{ $event->highlight_needs }}</span>
                                    @elseif ($event->tagline)
                                        <span class="event-banner__meta">{{ $event->tagline }}</span>
                                    @elseif ($event->location)
                                        <span class="event-banner__meta">{{ $event->location }}</span>
                                    @elseif ($event->start_date)
                                        <span class="event-banner__meta">Desde {{ $event->start_date->format('d/m') }}</span>
                                    @else
                                        <span class="event-banner__meta">Em andamento</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="event-banner event-banner--empty shadow-sm mb-4">
                <div class="event-banner__header">
                    <span class="event-banner__title">Eventos em andamento</span>
                </div>
                <div class="px-3 pb-3 small text-muted">Nenhum evento em andamento no momento.</div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-itens-center mb-3">
            <h2 class="h4 mb-0 text-gray-800">Resumo (Últimos 30 dias)</h2>
        </div>

        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    Doações (Dinheiro)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$
                                    {{ number_format($stats['doacoes_30d'] ?? 0, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    Distribuições</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['distribuicoes_30d'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck-ramp-box fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    Beneficiários Atendidos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['beneficiarios_atendidos'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    Itens em Estoque</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['itens_em_estoque'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes-stacked fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-between align-itens-center mb-4">
            <h2 class="h4 mb-0 text-gray-800">Acesso Rápido</h2>
        </div>

        <div class="row">

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2" data-icon="">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Cadastrar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Novo Doador</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="{{ route('doadores.create', ['redirect_to' => route('painel.dashboard')]) }}"
                            class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2" data-icon="➕">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Cadastrar</div>
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
                <div class="card border-left-success shadow h-100 py-2" data-icon="">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Cadastrar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Nova Distribuição</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck-ramp-box fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="{{ route('distribuicoes.create') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2 card-icon-beneficiarios" data-icon="👥">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Cadastrar/Editar</div>
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

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2" data-icon="📋">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Gerenciar</div>
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
                <div class="card border-left-success shadow h-100 py-2" data-icon="🚛">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Gerenciar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Distribuições</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-people-carry-box fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="{{ route('distribuicoes.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2" data-icon="📊">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Consultar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Estoque</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes-stacked fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="{{ route('estoque.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>


            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 card-icon-item" data-icon="📦">
                    <div class="card-body">
                        <div class="row no-gutters align-itens-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Cadastrar/Editar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Itens</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-inbox fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="{{ route('itens.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

        </div>

        @push('styles')
            <style>
                .card .border-left-primary {
                    border-left: 0.25rem solid #4e73df !important;
                }

                .card .border-left-success {
                    border-left: 0.25rem solid #1cc88a !important;
                }

                .card .border-left-danger {
                    border-left: 0.25rem solid #e74a3b !important;
                }

                .card .border-left-info {
                    border-left: 0.25rem solid #36b9cc !important;
                }

                .card .border-left-secondary {
                    border-left: 0.25rem solid #858796 !important;
                }

                .card .border-left-warning {
                    border-left: 0.25rem solid #f6c23e !important;
                }

                .card .border-left-dark {
                    border-left: 0.25rem solid #5a5c69 !important;
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

                .card-icon-item::before {
                    top: 70%;
                }

                .dashboard-events-scroll {
                    margin-right: -0.75rem;
                    margin-left: -0.75rem;
                    padding-right: 0.75rem;
                    padding-left: 0.75rem;
                }

                .dashboard-event-card {
                    border: none;
                    border-radius: 1rem;
                    min-width: 260px;
                    max-width: 320px;
                    position: relative;
                    transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
                }

                .dashboard-event-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
                }

                .dashboard-event-image-wrapper {
                    height: 140px;
                    overflow: hidden;
                    border-top-left-radius: 1rem;
                    border-top-right-radius: 1rem;
                }

                .dashboard-event-image {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                @media (max-width: 575.98px) {
                    .dashboard-event-card {
                        min-width: 220px;
                    }
                }

                .event-banner {
                    background: var(--bs-body-bg);
                    border-radius: 0.75rem;
                    border: 1px solid rgba(var(--bs-body-color-rgb), 0.08);
                }

                .event-banner__header {
                    padding: 0.75rem 1.25rem 0.5rem;
                }

                .event-banner__title {
                    display: inline-block;
                    font-size: 0.75rem;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                    font-weight: 700;
                    color: var(--bs-primary);
                }

                .event-banner__subtitle {
                    display: block;
                    margin-top: 0.25rem;
                    font-size: 0.8rem;
                    color: var(--bs-secondary-color);
                }

                .event-banner__marquee {
                    position: relative;
                    overflow: hidden;
                    padding: 0.6rem 0;
                }

                .event-banner__marquee::before,
                .event-banner__marquee::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    width: 3rem;
                    pointer-events: none;
                    z-index: 1;
                }

                .event-banner__marquee::before {
                    left: 0;
                    background: linear-gradient(90deg, rgba(var(--bs-body-bg-rgb), 1) 0%, rgba(var(--bs-body-bg-rgb), 0) 100%);
                }

                .event-banner__marquee::after {
                    right: 0;
                    background: linear-gradient(270deg, rgba(var(--bs-body-bg-rgb), 1) 0%, rgba(var(--bs-body-bg-rgb), 0) 100%);
                }

                .event-banner__track {
                    display: flex;
                    width: max-content;
                    animation: event-banner-scroll var(--event-marquee-duration, 24s) linear infinite;
                }

                .event-banner__group {
                    display: inline-flex;
                    align-items: center;
                    gap: 2rem;
                    padding-right: 2rem;
                }

                .event-banner__item {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.6rem;
                    white-space: nowrap;
                    font-size: 0.95rem;
                    color: var(--bs-body-color);
                }

                .event-banner__bullet {
                    width: 0.45rem;
                    height: 0.45rem;
                    border-radius: 999px;
                    background: var(--bs-primary);
                    box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.12);
                }

                .event-banner__divider {
                    opacity: 0.35;
                }

                .event-banner__meta {
                    font-size: 0.85rem;
                    color: var(--bs-secondary-color);
                }

                .event-banner--empty .event-banner__marquee {
                    display: none;
                }

                @keyframes event-banner-scroll {
                    from {
                        transform: translateX(0);
                    }

                    to {
                        transform: translateX(-50%);
                    }
                }
            </style>
        @endpush

    </div>
@endsection