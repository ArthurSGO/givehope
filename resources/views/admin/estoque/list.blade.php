@extends('app')
@section('title', $isAdmin ? 'Visão geral do estoque' : 'Estoque da Paróquia')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ $isAdmin ? 'Controle de Estoque das Paróquias' : 'Controle de Estoque da Paróquia' }}
                </div>
                <div class="card-body">
                    <a href="{{ $isAdmin ? route('admin.dashboard') : route('painel.dashboard') }}" class="btn btn-secondary mb-3">
                        Voltar ao Painel
                    </a>

                    @php
                    $formatarQuantidade = function ($quantidade, $unidade) {
                    $casasDecimais = $unidade === 'Kg' ? 3 : 0;
                    $valorFormatado = number_format($quantidade, $casasDecimais, ',', '.');

                    if ($casasDecimais > 0) {
                    $valorFormatado = rtrim(rtrim($valorFormatado, '0'), ',');
                    }

                    return $valorFormatado . ' ' . $unidade;
                    };
                    @endphp

                    @if ($isAdmin)
                    @forelse ($estoquesPorParoquia as $grupo)
                    <div class="mb-4">
                        <h5 class="fw-bold">
                            {{ optional($grupo['paroquia'])->nome_fantasia ?? optional($grupo['paroquia'])->nome ?? 'Paróquia não identificada' }}
                        </h5>

                        @if ($grupo['estoques']->isEmpty())
                        <p class="text-muted">Nenhum item em estoque para esta paróquia.</p>
                        @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Categoria</th>
                                        <th>Quantidade</th>
                                        <th>Atualizado em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($grupo['estoques'] as $estoque)
                                    <tr>
                                        <td>{{ optional($estoque->item)->nome ?? 'Item removido' }}</td>
                                        <td>{{ optional($estoque->item)->categoria ?? '-' }}</td>
                                        <td>{{ $formatarQuantidade($estoque->quantidade, $estoque->unidade) }}</td>
                                        <td>{{ optional($estoque->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    @empty
                    <p class="text-muted">Nenhuma paróquia possui itens em estoque no momento.</p>
                    @endforelse
                    @else
                    <h5 class="fw-bold mb-3">
                        Paróquia: {{ optional($paroquia)->nome_fantasia ?? optional($paroquia)->nome ?? 'Não associada' }}
                    </h5>

                    @if ($estoques->isEmpty())
                    <p class="text-muted">Nenhum item registrado no estoque desta paróquia ainda.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Categoria</th>
                                    <th>Quantidade</th>
                                    <th>Atualizado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estoques as $estoque)
                                <tr>
                                    <td>{{ optional($estoque->item)->nome ?? 'Item removido' }}</td>
                                    <td>{{ optional($estoque->item)->categoria ?? '-' }}</td>
                                    <td>{{ $formatarQuantidade($estoque->quantidade, $estoque->unidade) }}</td>
                                    <td>{{ optional($estoque->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection