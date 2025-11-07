@extends('app')
@section('title', 'Editar evento')

@php
    $previewImage = $event->image_url;
@endphp

@section('content')
    <div class="container py-4">
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="fa-solid fa-arrow-left"></i> Voltar para os eventos
        </a>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ops!</strong> Corrija os campos destacados antes de salvar.
            </div>
        @endif

        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm mb-3">
                <div class="card-header py-3">
                    <h1 class="h4 mb-0">Editar evento de doação</h1>
                    <small class="text-muted">Os status são calculados automaticamente usando as datas informadas.</small>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Imagem de destaque</label>
                        <div class="border rounded-3 overflow-hidden mb-2">
                            <img src="{{ $previewImage }}" class="img-fluid w-100 event-hero" alt="Imagem atual do evento">
                        </div>
                        <input type="file" name="image" accept="image/*"
                            class="form-control @error('image') is-invalid @enderror">
                        <div class="form-text">Use arquivos JPG, PNG, WEBP ou AVIF com até 5&nbsp;MB. Envie um arquivo
                            apenas se quiser trocar a imagem atual.</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $event->title) }}"
                                class="form-control @error('title') is-invalid @enderror" maxlength="150" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Data de início</label>
                            <input type="date" id="start_date" name="start_date"
                                value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}"
                                class="form-control @error('start_date') is-invalid @enderror">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8">
                            <label for="tagline" class="form-label">Subtítulo</label>
                            <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $event->tagline) }}"
                                class="form-control @error('tagline') is-invalid @enderror" maxlength="255"
                                placeholder="Resumo curto do evento">
                            @error('tagline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Data de término</label>
                            <input type="date" id="end_date" name="end_date"
                                value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}"
                                class="form-control @error('end_date') is-invalid @enderror">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Localização</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $event->location) }}"
                                class="form-control @error('location') is-invalid @enderror" maxlength="255"
                                placeholder="Onde a ação acontece">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="highlight_needs" class="form-label">Doações prioritárias</label>
                            <input type="text" id="highlight_needs" name="highlight_needs"
                                value="{{ old('highlight_needs', $event->highlight_needs) }}"
                                class="form-control @error('highlight_needs') is-invalid @enderror"
                                placeholder="Ex.: Cobertores, alimentos não perecíveis">
                            @error('highlight_needs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Descrição completa</label>
                            <textarea id="description" name="description" rows="6"
                                class="form-control @error('description') is-invalid @enderror"
                                required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Atualizar evento
                    </button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .event-hero {
            max-height: 280px;
            object-fit: cover;
        }
    </style>
@endsection