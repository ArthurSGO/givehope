@extends('app')
@section('title', 'Relatórios Gerais')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="badge rounded-pill text-bg-primary px-3 py-2 mb-3">Transparência</span>
            <h1 class="display-5 fw-semibold">Panorama das Doações</h1>
            <p class="lead text-muted">Acompanhe de forma clara tudo o que foi arrecadado e quem está ajudando a transformar
                vidas através do GiveHope.</p>
        </div>

        <section class="mb-5">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card h-100">
                        <div class="stat-label">Doações registradas</div>
                        <div class="stat-value">{{ $summary['total_donations'] }}</div>
                        <p class="stat-help">Entradas únicas de doações cadastradas na plataforma.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card h-100">
                        <div class="stat-label">Valor arrecadado</div>
                        <div class="stat-value">R$
                            {{ number_format($summary['monetary_total'], 2, ',', '.') }}
                        </div>
                        <p class="stat-help">Somatório de doações classificadas como financeiras.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card h-100">
                        <div class="stat-label">Doadores</div>
                        <div class="stat-value">{{ $summary['donors_count'] }}</div>
                        <p class="stat-help">Pessoas e empresas que já realizaram contribuições.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="stat-card h-100">
                        <div class="stat-label">Itens registrados</div>
                        <div class="stat-value">{{ $summary['item_entries'] }}</div>
                        <p class="stat-help">Quantidade de itens vinculados às doações recebidas.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Rede de paróquias</h2>
                            @if (!empty($topParishes))
                                <ul class="list-group list-group-flush">
                                    @foreach ($topParishes as $parish)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fw-semibold">{{ $parish['name'] }}</div>
                                                    <small class="text-muted">{{ $parish['donations_count'] }} doações
                                                        registradas</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="badge rounded-pill text-bg-primary">R$
                                                        {{ number_format($parish['monetary_total'], 2, ',', '.') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">Cadastre doações associadas às paróquias para acompanhar aqui.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
                        <div>
                            <h2 class="h4 mb-1">Itens mais doados</h2>
                            <p class="text-muted mb-0">Lista consolidada dos itens cadastrados em todas as campanhas.</p>
                        </div>
                    </div>

                    @if (!empty($itemBreakdown))
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-center">Unidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($itemBreakdown)->take(12) as $item)
                                        <tr>
                                            <td class="fw-semibold">{{ $item['name'] }}</td>
                                            <td class="text-center">
                                                {{ rtrim(rtrim(number_format($item['quantity'], 2, ',', '.'), '0'), ',') }}</td>
                                            <td class="text-center"><span
                                                    class="badge text-bg-tertiary">{{ $item['unit'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if (count($itemBreakdown) > 12)
                            <p class="text-muted small mt-3 mb-0">Exibindo os 12 itens com maior volume entre
                                {{ count($itemBreakdown) }}
                                cadastrados.</p>
                        @endif
                    @else
                        <p class="text-muted mb-0">Nenhum item foi registrado nas doações até o momento.</p>
                    @endif
                </div>
            </div>
        </section>

        <section>
            <div class="card shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
                        <div>
                            <h2 class="h4 mb-1">Linha do tempo das arrecadações</h2>
                            <p class="text-muted mb-0">Acompanhe a evolução mês a mês das doações registradas.</p>
                        </div>
                    </div>

                    @if (!empty($monthlyTimeline))
                        <div class="position-relative border-start border-primary border-2 mt-4 ps-4">

                            @foreach ($monthlyTimeline as $month)
                                <div class="position-relative mb-4">
                                    
                                    <span class="position-absolute start-0 translate-middle-x bg-primary rounded-pill"
                                          style="width: 0.9rem; height: 0.9rem; top: 0.35rem;"></span>

                                    <div class="card shadow-sm ms-2">
                                        <div class="card-body p-3">
                                            <h3 class="h6 mb-1">{{ $month['label'] }}</h3>
                                            <div class="text-muted small">{{ $month['donations_count'] }} doações registradas
                                            </div>
                                            <div class="fw-semibold text-success">R$
                                                {{ number_format($month['monetary_total'], 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <p class="text-muted mb-0 mt-4">Os dados aparecerão aqui conforme as doações forem sendo lançadas.</p>
                    @endif

                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            background: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color-translucent);
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bs-secondary-color);
        }

        .stat-value {
            font-size: 1.7rem;
            font-weight: 600;
            color: var(--bs-emphasis-color);
        }

        .stat-help {
            font-size: 0.8rem;
            color: var(--bs-secondary-color);
            margin-bottom: 0;
        }

        @media (max-width: 767.98px) {
            .stat-card {
                padding: 1.25rem;
            }
        }
    </style>
@endpush