@extends('app')
@section('title', 'Detalhes do Log #'.$log->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-magnifying-glass me-2"></i>Detalhes do Log #{{ $log->id }}</h5>
                    <a href="{{ url()->previous() ?: route('logs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Descrição</dt>
                        <dd class="col-sm-8">{{ $log->description }}</dd>

                        <dt class="col-sm-4">Evento</dt>
                        <dd class="col-sm-8">
                            @php
                            $eventLabel = match ($log->event) {
                            'created' => 'Criação',
                            'updated' => 'Atualização',
                            'deleted' => 'Exclusão',
                            default => ucfirst($log->event ?? 'Evento'),
                            };
                            @endphp
                            <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $eventLabel }}</span>
                        </dd>

                        <dt class="col-sm-4">Data</dt>
                        <dd class="col-sm-8">{{ $log->created_at->format('d/m/Y H:i') }} ({{ $log->created_at->diffForHumans() }})</dd>

                        <dt class="col-sm-4">Usuário</dt>
                        <dd class="col-sm-8">{{ $log->causer->name ?? 'Sistema' }}</dd>

                        <dt class="col-sm-4">Registro afetado</dt>
                        <dd class="col-sm-8">
                            @if ($log->subject instanceof \App\Models\Doacao)
                            <div class="mb-2">
                                <strong>Doação #{{ $log->subject->id }}</strong>
                                <div class="small text-muted">
                                    {{ ucfirst($log->subject->tipo) }}
                                    @if($log->subject->doador)
                                    · {{ $log->subject->doador->nome }}
                                    @endif
                                    @if($log->subject->data_doacao)
                                    · {{ \Carbon\Carbon::parse($log->subject->data_doacao)->format('d/m/Y') }}
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('doacoes.show', $log->subject->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye"></i> Ver doação
                            </a>
                            @else
                            <span class="text-muted">Registro removido ou indisponível.</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Origem</dt>
                        <dd class="col-sm-8">{{ $log->log_name }} · {{ class_basename($log->subject_type ?? 'Desconhecido') }}</dd>
                    </dl>
                </div>
            </div>

            @php
            $properties = $log->properties?->toArray() ?? [];
            $attributes = $properties['attributes'] ?? [];
            $old = $properties['old'] ?? [];
            @endphp

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fa-solid fa-file-lines me-2"></i>Resumo da Operação</h6>
                </div>
                <div class="card-body">
                    @if(empty($attributes) && empty($old))
                    <p class="text-muted mb-0">Nenhum dado adicional foi registrado para este log.</p>
                    @else
                    <div class="row g-3">
                        @if(!empty($attributes))
                        <div class="col-12">
                            <h6 class="text-success">Valores atuais</h6>
                            <ul class="list-group list-group-flush">
                                @foreach($attributes as $campo => $valor)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $campo)) }}</div>
                                        <div class="text-muted small">Atualizado para</div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success">{{ $valor ?? '—' }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(!empty($old))
                        <div class="col-12">
                            <h6 class="text-danger mt-3">Valores anteriores</h6>
                            <ul class="list-group list-group-flush">
                                @foreach($old as $campo => $valor)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $campo)) }}</div>
                                        <div class="text-muted small">Substituído por</div>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger">{{ $valor ?? '—' }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection