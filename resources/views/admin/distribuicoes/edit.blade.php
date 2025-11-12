@extends('app')
@section('title', 'Editar Distribuição #' . $distribuicao->id)
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Editar Distribuição #{{ $distribuicao->id }}</div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @php
                            $temDisponivel = $estoques->contains(function ($estoque) {
                                return $estoque->quantidade_disponivel > 0;
                            });
                        @endphp

                        <form action="{{ route('distribuicoes.update', $distribuicao) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="beneficiario_id" class="form-label">Beneficiário</label>
                                <select name="beneficiario_id" id="beneficiario_id" class="form-select" required>
                                    <option value="">Selecione um beneficiário</option>
                                    @foreach ($beneficiarios as $beneficiario)
                                        <option value="{{ $beneficiario->id }}" @selected(old('beneficiario_id', $distribuicao->beneficiario_id) == $beneficiario->id)>
                                            {{ $beneficiario->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Itens disponíveis no estoque</label>
                                @php
                                    $formatQuantidade = function ($quantidade, $unidade) {
                                        $casas = $unidade === 'Kg' ? 3 : 2;
                                        $valor = number_format($quantidade, $casas, ',', '.');

                                        if ($casas > 0) {
                                            $valor = rtrim(rtrim($valor, '0'), ',');
                                        }

                                        return $valor;
                                    };
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Categoria</th>
                                                <th class="text-center">Unidade</th>
                                                <th class="text-center">Estoque atual</th>
                                                <th class="text-center">Reservado (Outros)</th>
                                                <th class="text-center">Máximo p/ esta Reserva</th>
                                                <th class="text-center" style="width: 180px;">Quantidade a reservar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($estoques as $estoque)
                                                @php
                                                    $maximoDisponivel = $estoque->quantidade_disponivel;
                                                    $valorAtual = $estoque->quantidade_reservada_nesta;
                                                    $step = $estoque->unidade === 'Kg' ? '0.001' : '0.01';
                                                @endphp
                                                <tr @class(['table-warning' => $maximoDisponivel <= 0 && $valorAtual <= 0])>
                                                    <td>{{ $estoque->item->nome }}</td>
                                                    <td>{{ $estoque->item->categoria ?? '-' }}</td>
                                                    <td class="text-center">{{ $estoque->unidade }}</td>
                                                    <td class="text-center">
                                                        {{ $formatQuantidade($estoque->quantidade, $estoque->unidade) }}</td>
                                                    <td class="text-center">
                                                        {{ $formatQuantidade($estoque->quantidade_reservada, $estoque->unidade) }}
                                                    </td>
                                                    <td class="text-center fw-semibold">
                                                        {{ $formatQuantidade($maximoDisponivel, $estoque->unidade) }}</td>
                                                    <td>
                                                        <input type="number" name="itens[{{ $estoque->id }}][quantidade]"
                                                            class="form-control @error('itens.' . $estoque->id . '.quantidade') is-invalid @enderror"
                                                            step="{{ $step }}" min="0" max="{{ $maximoDisponivel }}"
                                                            value="{{ old('itens.' . $estoque->id . '.quantidade', $valorAtual) }}"
                                                            @disabled($maximoDisponivel <= 0 && $valorAtual <= 0)>
                                                        @error('itens.' . $estoque->id . '.quantidade')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Nenhum item disponível para
                                                        distribuição no momento.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text">Informe apenas as quantidades que serão reservadas para esta
                                    distribuição.</div>
                                @unless ($temDisponivel || $estoques->sum('quantidade_reservada_nesta') > 0)
                                    <div class="alert alert-warning mt-3 mb-0">
                                        Não há estoque disponível para reserva.
                                    </div>
                                @endunless
                            </div>

                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea name="observacoes" id="observacoes" rows="3"
                                    class="form-control">{{ old('observacoes', $distribuicao->observacoes) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('distribuicoes.show', $distribuicao) }}"
                                    class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary" @disabled(!$temDisponivel && $estoques->sum('quantidade_reservada_nesta') <= 0)>Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection