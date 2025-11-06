@extends('app')
@section('title', $heading)

@php
    use Illuminate\Support\Str;
    $badgeClass = 'text-bg-primary';
@endphp

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $heading }}</h1>
                <p class="text-muted mb-0">Acompanhe os eventos que estão acontecendo agora e descubra como ajudar.</p>
            </div>
            <a href="{{ url('/') }}" class="btn btn-light border">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

        @if ($events->isEmpty())
            <div class="border rounded-3 p-5 text-center bg-body-tertiary">
                <h2 class="h5">Nenhum evento em andamento.</h2>
                <p class="text-muted mb-0">Assim que novas ações começarem elas aparecerão aqui.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($events as $event)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $event->image_url }}" class="card-img-top event-card-img" alt="Imagem do evento {{ $event->title }}">
                            <div class="card-body d-flex flex-column">
                                <span class="badge {{ $badgeClass }} align-self-start mb-3">{{ $event->status_label }}</span>
                                <h2 class="h5">{{ $event->title }}</h2>
                                @if ($event->tagline)
                                    <p class="text-muted">{{ $event->tagline }}</p>
                                @endif
                                <ul class="list-unstyled small text-muted mb-3">
                                    @if ($event->start_date)
                                        <li><i class="fa-regular fa-calendar me-1"></i> Início {{ $event->start_date->format('d/m/Y') }}</li>
                                    @endif
                                    @if ($event->end_date)
                                        <li><i class="fa-solid fa-flag-checkered me-1"></i> Termina {{ $event->end_date->format('d/m/Y') }}</li>
                                    @endif
                                    @if ($event->location)
                                        <li><i class="fa-solid fa-location-dot me-1"></i> {{ $event->location }}</li>
                                    @endif
                                </ul>
                                <p class="small flex-grow-1">{{ Str::limit($event->description, 120) }}</p>
                                <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary mt-3">Ver detalhes</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .event-card-img {
            height: 200px;
            object-fit: cover;
        }
    </style>
@endsection