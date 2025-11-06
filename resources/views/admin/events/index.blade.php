@extends('app')
@section('title', 'Eventos de doação')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Eventos de doação</h1>
                <p class="text-muted mb-0">Gerencie as campanhas e acompanhe automaticamente o status pelo calendário.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar ao painel
                </a>
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Novo evento
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @foreach ($statuses as $statusKey => $statusLabel)
            @php
                $events = $eventsByStatus->get($statusKey, collect());
                $badgeClass = match ($statusKey) {
                    \App\Models\Event::STATUS_SOON => 'text-bg-info',
                    \App\Models\Event::STATUS_IN_PROGRESS => 'text-bg-primary',
                    default => 'text-bg-secondary',
                };
            @endphp

            <section class="mb-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <h2 class="h5 mb-0 text-uppercase text-secondary">{{ $statusLabel }}</h2>
                    <span class="badge bg-light text-secondary border">{{ $events->count() }} evento(s)</span>
                </div>

                @if ($events->isEmpty())
                    <div class="border rounded-3 p-4 bg-body-tertiary text-center">
                        <p class="fw-semibold mb-1">Nenhum evento para este período.</p>
                        <p class="text-muted mb-0">Clique em "Novo evento" para cadastrar uma nova ação.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($events as $event)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <img src="{{ $event->image_url }}" class="card-img-top event-card-img" alt="Imagem do evento {{ $event->title }}">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge {{ $badgeClass }}">{{ $event->status_label }}</span>
                                            <div class="text-muted small text-end">
                                                @if ($event->start_date)
                                                    <div><i class="fa-regular fa-calendar"></i> Início {{ $event->start_date->format('d/m/Y') }}</div>
                                                @endif
                                                @if ($event->end_date)
                                                    <div><i class="fa-regular fa-flag"></i> Término {{ $event->end_date->format('d/m/Y') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <h3 class="h5">{{ $event->title }}</h3>
                                        @if ($event->tagline)
                                            <p class="text-muted mb-2">{{ $event->tagline }}</p>
                                        @endif

                                        <p class="small flex-grow-1">{{ Str::limit($event->description, 140) }}</p>

                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Editar
                                            </a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="ms-auto"
                                                onsubmit="return confirm('Deseja realmente excluir este evento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fa-solid fa-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    <style>
        .event-card-img {
            height: 200px;
            object-fit: cover;
        }
    </style>
@endsection