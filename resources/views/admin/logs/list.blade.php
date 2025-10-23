@extends('app')
@section('title', 'Logs de Atividade')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-clipboard-list me-2"></i>Logs de Auditoria do Sistema</h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Evento</th>
                                    <th>Registro</th>
                                    <th class="text-end">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                @php
                                $properties = $log->properties?->toArray() ?? [];
                                $attributes = $properties['attributes'] ?? [];
                                $old = $properties['old'] ?? [];
                                $eventLabel = match ($log->event) {
                                'created' => 'Criação',
                                'updated' => 'Atualização',
                                'deleted' => 'Exclusão',
                                default => ucfirst($log->event ?? 'Evento'),
                                };
                                @endphp
                                <tr>
                                    <td>
                                        <span class="d-block fw-semibold">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>{{ $log->causer->name ?? 'Sistema' }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $eventLabel }}</span>
                                        <div class="small text-muted">{{ $log->description }}</div>
                                    </td>
                                    <td>
                                        @if ($log->subject instanceof \App\Models\Doacao)
                                        <div class="fw-semibold">Doação #{{ $log->subject->id }}</div>
                                        <div class="small text-muted">
                                            {{ ucfirst($log->subject->tipo) }}
                                            @if($log->subject->doador)
                                            · {{ $log->subject->doador->nome }}
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">Registro indisponível</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-magnifying-glass"></i> Ver detalhes
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum log de atividade registrado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer bg-light border-top-0">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection