@extends('app')
@section('title', "Extrato de Estoque: {$estoque->item->nome}")
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="d-flex justify-content-between align-itens-center mb-3">
                <h2 class="h4 mb-0">
                    Extrato de Movimentações
                </h2>
                <a href="{{ route('estoque.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Voltar para Estoque
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    Item: <strong>{{ $estoque->item->nome }} ({{ $estoque->unidade }})</strong>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Saldo Atual: {{ number_format($estoque->quantidade, 2, ',', '.') }} {{ $estoque->unidade }}</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Histórico de Movimentações</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Quantidade</th>
                                    <th>Motivo/Origem</th>
                                    <th>Usuário</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movimentacoes as $mov)
                                <tr>
                                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        @if ($mov->tipo == 'entrada')
                                        <span class="badge bg-success">Entrada</span>
                                        @else
                                        <span class="badge bg-danger">Saída</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ $mov->tipo == 'entrada' ? '+' : '-' }}
                                        {{ number_format($mov->quantidade, 2, ',', '.') }} {{ $mov->unidade }}
                                    </td>
                                    <td>
                                        {{ $mov->motivo }}
                                        @if ($mov->doacao_id)
                                        (Doação <a href="{{ route('doacoes.show', $mov->doacao_id) }}">#{{ $mov->doacao_id }}</a>)
                                        @elseif ($mov->distribuicao_id)
                                        (Distribuição <a href="{{ route('distribuicoes.show', $mov->distribuicao_id) }}">#{{ $mov->distribuicao_id }}</a>)
                                        @endif
                                    </td>
                                    <td>{{ $mov->user->name ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhuma movimentação registrada para este item.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($movimentacoes->hasPages())
                <div class="card-footer">
                    {{ $movimentacoes->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection