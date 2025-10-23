@extends('app')
@section('title', 'Detalhes da Doação #'.$doacao->id)
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalhes da Doação #{{ $doacao->id }}</h5>
                    <a href="{{ url()->previous() ?: route('doacoes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6><i class="fa-solid fa-gift me-2 text-primary"></i>Doação</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Data:</dt>
                                <dd class="col-sm-8">{{ \Carbon\Carbon::parse($doacao->data_doacao)->format('d/m/Y') }}</dd>

                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8"><span class="badge bg-info text-dark">{{ ucfirst($doacao->tipo) }}</span></dd>

                                <dt class="col-sm-4">Descrição:</dt>
                                <dd class="col-sm-8">{{ $doacao->descricao ?: 'Nenhuma' }}</dd>

                                @if($doacao->tipo === 'dinheiro')
                                <dt class="col-sm-4">Valor:</dt>
                                <dd class="col-sm-8 fw-bold text-success">R$ {{ number_format($doacao->quantidade, 2, ',', '.') }}</dd>
                                @endif
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <h6><i class="fa-solid fa-user me-2 text-primary"></i>Doador</h6>
                            @php $doador = $doacao->doador; @endphp
                            @if($doador && $doador->nome !== 'Anônimo')
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Nome:</dt>
                                <dd class="col-sm-8">{{ $doador->nome }}</dd>

                                <dt class="col-sm-4">CPF/CNPJ:</dt>
                                <dd class="col-sm-8">{{ $doador->cpf_cnpj ?? 'Não informado' }}</dd>

                                <dt class="col-sm-4">Telefone:</dt>
                                <dd class="col-sm-8">{{ $doador->telefone ?? 'Não informado' }}</dd>

                                @php
                                $enderecoParts = collect([
                                $doador->logradouro,
                                $doador->numero,
                                $doador->cidade,
                                $doador->estado,
                                $doador->cep ? 'CEP: '.$doador->cep : null,
                                ])->filter()->implode(', ');
                                @endphp
                                @if($enderecoParts)
                                <dt class="col-sm-4">Endereço:</dt>
                                <dd class="col-sm-8">{{ $enderecoParts }}</dd>
                                @endif
                            </dl>
                            @else
                            <p class="text-muted mb-0">Doação anônima.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($doacao->tipo === 'item' && $doacao->items->isNotEmpty())
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Itens Doados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Quantidade</th>
                                    <th>Unidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doacao->items as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td class="text-end">{{ $item->formatted_quantidade ?? $item->pivot->quantidade }}</td>
                                    <td>{{ $item->pivot->unidade }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(!empty($resumoItens))
                <div class="card-footer bg-light text-muted small d-flex justify-content-end">
                    @foreach($resumoItens as $un => $totalFormatado)
                    <span class="ms-4">
                        Total {{ $un }}: <strong>
                            {{ $totalFormatado }}
                        </strong>
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            @elseif($doacao->tipo === 'item')
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Itens Doados</h5>
                </div>
                <div class="card-body text-center text-muted">
                    Nenhum item vinculado a esta doação.
                </div>
            </div>
            @endif

            @if(isset($logs) && $logs->count())
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-clipboard-list me-2"></i>Histórico de Alterações</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($logs as $log)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $log->description }}</h6>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }} ({{ $log->created_at->format('d/m/Y H:i') }})</small>
                        </div>
                        <p class="mb-1 small">Realizado por: {{ $log->causer->name ?? 'Sistema' }}</p>
                        @php
                        $props = $log->properties?->toArray() ?? [];
                        $changes = $props['attributes'] ?? null;
                        $old = $props['old'] ?? null;
                        @endphp
                        @if($log->event === 'updated' && $changes && $old)
                        <details class="mt-2 small">
                            <summary class="text-primary" style="cursor: pointer;">Ver detalhes da alteração</summary>
                            <ul class="list-unstyled mt-2 mb-0">
                                @foreach($changes as $campo => $valor)
                                @if(isset($old[$campo]))
                                <li>
                                    <strong>{{ ucfirst($campo) }}:</strong>
                                    <span class="text-danger"><s>{{ $old[$campo] ?? '—' }}</s></span> &#10141;
                                    <span class="text-success">{{ $valor ?? '—' }}</span>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </details>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection