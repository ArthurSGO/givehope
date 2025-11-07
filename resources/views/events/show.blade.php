@extends('app')
@section('title', $event->title)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ $backUrl ?? route('soon') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>

            @if ($canManage ?? false)
                <div class="d-flex gap-2">
                    <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <form action="{{ route('events.destroy', $event) }}" method="POST"
                        onsubmit="return confirm('Deseja realmente excluir este evento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fa-solid fa-trash"></i> Excluir
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card shadow-sm mb-4 overflow-hidden">
            <div class="position-relative">
                <img src="{{ $event->image_url }}" class="event-hero w-100" alt="Imagem do evento {{ $event->title }}">
                <div class="position-absolute top-0 start-0 m-3">
                    <span class="badge text-bg-info">{{ $event->status_label }}</span>
                </div>
                <div class="position-absolute top-0 end-0 m-3 text-end text-white fw-semibold">
                    @if ($event->start_date)
                        <div class="d-flex align-items-center justify-content-end gap-2 bg-dark bg-opacity-50 rounded-pill px-3 py-1 mb-2">
                            <i class="fa-regular fa-calendar"></i>
                            <span>Início {{ $event->start_date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if ($event->end_date)
                        <div class="d-flex align-items-center justify-content-end gap-2 bg-dark bg-opacity-50 rounded-pill px-3 py-1">
                            <i class="fa-solid fa-flag-checkered"></i>
                            <span>Término {{ $event->end_date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <h1 class="card-title h2">{{ $event->title }}</h1>
                @if ($event->tagline)
                    <p class="card-subtitle text-muted mb-3">{{ $event->tagline }}</p>
                @endif

                <div class="row g-3 mb-4">
                    @if ($event->location)
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100">
                                <div class="text-uppercase text-muted small">Localização</div>
                                <div class="fw-semibold"><i class="fa-solid fa-location-dot me-2"></i>{{ $event->location }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($event->highlight_needs)
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100">
                                <div class="text-uppercase text-muted small">Doações prioritárias</div>
                                <div class="fw-semibold"><i class="fa-solid fa-gift me-2"></i>{{ $event->highlight_needs }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="h5">Descrição</h2>
                    <p class="lead">{!! nl2br(e($event->description)) !!}</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .event-hero {
            max-height: 320px;
            object-fit: cover;
        }
    </style>
@endsection