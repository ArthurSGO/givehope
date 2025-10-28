@extends('app')
@section('title', 'Distribuições')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Distribuições da Paróquia') }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('painel.dashboard') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Voltar
                        </a>
                        <a href="{{ route('distribuicoes.relatorios') }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-chart-pie me-1"></i> Relatórios
                        </a>
                        <a href="{{ route('distribuicoes.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus me-1"></i> Nova Distribuição
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Beneficiário</th>
                                    <th>Status</th>
                                    <th>Itens</th>
                                    <th>Reservado em</th>
                                    <th>Enviado em</th>
                                    <th>Entregue em</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusBadges = [
                                        'reservado' => 'warning',
                                        'enviado' => 'info',
                                        'entregue' => 'success',
                                    ];
                                    $formatQuantidade = function ($quantidade, $unidade) {
                                        $casas = $unidade === 'Kg' ? 3 : 2;
                                        $valor = number_format($quantidade, $casas, ',', '.');

                                        if ($casas > 0) {
                                            $valor = rtrim(rtrim($valor, '0'), ',');
                                        }

                                        return $valor;
                                    };
                                @endphp
                                @forelse ($distribuicoes as $distribuicao)
                                <tr>
                                    <td>{{ $distribuicao->id }}</td>
                                    <td>{{ $distribuicao->beneficiario->nome ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badge = $statusBadges[$distribuicao->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badge }} text-uppercase">{{ ucfirst($distribuicao->status) }}</span>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($distribuicao->items as $item)
                                            <li>{{ $item->nome }} - {{ $formatQuantidade($item->pivot->quantidade, $item->pivot->unidade) }} {{ $item->pivot->unidade }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ optional($distribuicao->reservado_em)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ optional($distribuicao->enviado_em)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ optional($distribuicao->entregue_em)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('distribuicoes.show', $distribuicao) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nenhuma distribuição registrada até o momento.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $distribuicoes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection