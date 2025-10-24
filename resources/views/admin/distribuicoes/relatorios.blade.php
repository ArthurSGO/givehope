@extends('app')
@section('title', 'Relatórios de Distribuição')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0">Relatórios de Distribuição</h2>
                <a href="{{ route('distribuicoes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header">Filtros</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('distribuicoes.relatorios') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="data_inicio" class="form-label">Data inicial</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ old('data_inicio', $filtros['data_inicio'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="data_fim" class="form-label">Data final</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ old('data_fim', $filtros['data_fim'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="item_id" class="form-label">Item</label>
                            <select name="item_id" id="item_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach ($items as $item)
                                <option value="{{ $item->id }}" @selected(($filtros['item_id'] ?? null) == $item->id)>{{ $item->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="beneficiario_id" class="form-label">Beneficiário</label>
                            <select name="beneficiario_id" id="beneficiario_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach ($beneficiarios as $beneficiario)
                                <option value="{{ $beneficiario->id }}" @selected(($filtros['beneficiario_id'] ?? null) == $beneficiario->id)>{{ $beneficiario->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('distribuicoes.relatorios') }}" class="btn btn-outline-secondary">Limpar filtros</a>
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>
                </div>
            </div>

            @php
                $queryExport = array_filter($filtros ?? [], fn ($value) => $value !== null && $value !== '');
                $formatQuantidade = function ($quantidade, $unidade) {
                    $casas = $unidade === 'Kg' ? 3 : 2;
                    $valor = number_format($quantidade, $casas, ',', '.');

                    if ($casas > 0) {
                        $valor = rtrim(rtrim($valor, '0'), ',');
                    }

                    return $valor;
                };
            @endphp

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Resumo das distribuições</span>
                    <a href="{{ route('distribuicoes.relatorios.export', $queryExport) }}" class="btn btn-outline-success btn-sm">
                        <i class="fa-solid fa-file-csv me-1"></i> Exportar CSV
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Beneficiário</th>
                                    <th>Item</th>
                                    <th class="text-center">Unidade</th>
                                    <th class="text-center">Total distribuído</th>
                                    <th class="text-center">Distribuições</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dadosRelatorio as $linha)
                                <tr>
                                    <td>{{ $linha->beneficiario_nome }}</td>
                                    <td>{{ $linha->item_nome }}</td>
                                    <td class="text-center">{{ $linha->unidade }}</td>
                                    <td class="text-center">{{ $formatQuantidade($linha->total_quantidade, $linha->unidade) }}</td>
                                    <td class="text-center">{{ $linha->total_distribuicoes }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhuma distribuição encontrada para os filtros informados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($dadosRelatorio->isNotEmpty())
                    @php
                        $totaisPorUnidade = $dadosRelatorio->groupBy('unidade')->map(function ($grupo) {
                            return [
                                'unidade' => $grupo->first()->unidade,
                                'total' => $grupo->sum('total_quantidade'),
                            ];
                        });
                    @endphp
                    <div class="alert alert-info mt-3 mb-0">
                        <strong>Total distribuído:</strong>
                        @foreach ($totaisPorUnidade as $total)
                        <span class="badge bg-primary">{{ $formatQuantidade($total['total'], $total['unidade']) }} {{ $total['unidade'] }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection