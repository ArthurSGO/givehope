@extends('app')
@section('title', 'Detalhes do Log #' . $log->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-itens-center">
                        <h5 class="mb-0"><i class="fa-solid fa-magnifying-glass me-2"></i>Detalhes do Log #{{ $log->id }}
                        </h5>
                        <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <dd class="col-sm-8">{{ $log->created_at->format('d/m/Y H:i') }}
                                ({{ $log->created_at->diffForHumans() }})</dd>

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
                                    <a href="{{ route('doacoes.show', $log->subject->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-eye"></i> Ver doação
                                    </a>
                                @else
                                    <span class="text-muted">Registro removido ou indisponível.</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Origem</dt>
                            <dd class="col-sm-8">{{ $log->log_name }} ·
                                {{ class_basename($log->subject_type ?? 'Desconhecido') }}
                            </dd>
                        </dl>
                    </div>
                </div>

                @php
                    $props = $log->properties ? $log->properties->toArray() : [];
                    $old = $props['old'] ?? [];
                    $changes = $props['attributes'] ?? [];
                    $alteracoes = data_get($props, 'extra.alteracoes', []);
                @endphp

                @if(is_array($alteracoes) && count($alteracoes))
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Unidade</th>
                                    <th class="text-end">Anterior</th>
                                    <th class="text-end">Atual</th>
                                    <th class="text-end">Δ</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alteracoes as $a)
                                    <tr>
                                        <td>{{ $a['nome'] ?? ('#' . $a['item_id']) }}</td>
                                        <td>{{ $a['unidade'] ?? '—' }}</td>
                                        <td class="text-end">{{ isset($a['anterior']) ? $a['anterior'] : '—' }}</td>
                                        <td class="text-end">{{ isset($a['atual']) ? $a['atual'] : '—' }}</td>
                                        <td class="text-end">{{ isset($a['delta']) ? $a['delta'] : '—' }}</td>
                                        <td>{{ $a['acao'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection