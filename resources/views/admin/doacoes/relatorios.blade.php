@extends('app')
@section('title', 'Relatórios de Doações')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="d-flex justify-content-between align-itens-center mb-3">
                    <h2 class="h4 mb-0">Relatórios de Doações</h2>
                    <a href="{{ route('doacoes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Filtros</div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('doacoes.relatorios') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="data_inicio" class="form-label">Data inicial</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                                    value="{{ old('data_inicio', $filtros['data_inicio'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="data_fim" class="form-label">Data final</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control"
                                    value="{{ old('data_fim', $filtros['data_fim'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="doador_id" class="form-label">Doador</label>
                                <select name="doador_id" id="doador_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach ($doadores as $doador)
                                        <option value="{{ $doador->id }}" @selected(($filtros['doador_id'] ?? null) == $doador->id)>{{ $doador->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select name="tipo" id="tipo" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="dinheiro" @selected(($filtros['tipo'] ?? null) == 'dinheiro')>Dinheiro
                                    </option>
                                    <option value="item" @selected(($filtros['tipo'] ?? null) == 'item')>Item</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('doacoes.relatorios') }}" class="btn btn-outline-secondary">Limpar
                                    filtros</a>
                                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                            </div>
                        </form>
                    </div>
                </div>

                @php
                    $queryExport = array_filter($filtros ?? [], fn($value) => $value !== null && $value !== '');
                @endphp

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-itens-center">
                        <span>Doações recebidas</span>
                        <a href="{{ route('doacoes.relatorios.export', $queryExport) }}"
                            class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-file-csv me-1"></i> Exportar CSV
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Doador</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-end">Valor (R$)</th>
                                        <th class="text-end">Itens (Qtd)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dadosRelatorio as $linha)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($linha->data_doacao)->format('d/m/Y') }}</td>
                                            <td>{{ $linha->doador->nome ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                @if ($linha->tipo == 'dinheiro')
                                                    <span class="badge bg-success">Dinheiro</span>
                                                @else
                                                    <span class="badge bg-info">Item</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($linha->tipo == 'dinheiro')
                                                    {{ number_format($linha->quantidade, 2, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($linha->tipo == 'item')
                                                    @php
                                                        $quantidade = (float) $linha->total_itens;
                                                        $unidade = $linha->unidade_itens;
                                                        $casasDecimais = ($unidade === 'Kg') ? 3 : 0;
                                                        $valorFormatado = number_format($quantidade, $casasDecimais, ',', '.');

                                                        if ($casasDecimais > 0) {
                                                            $valorFormatado = rtrim(rtrim($valorFormatado, '0'), ',');
                                                        }

                                                        $unidadeFormatada = $unidade;
                                                        if ($unidade === 'Unidade' && $quantidade != 1.0) {
                                                            $unidadeFormatada = 'Unidades';
                                                        }
                                                    @endphp
                                                    {{ $valorFormatado }} {{ $unidadeFormatada }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Nenhuma doação encontrada para os filtros
                                                informados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($dadosRelatorio->hasPages())
                        <div class="card-footer">
                            {{ $dadosRelatorio->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection