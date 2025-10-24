@extends('app')
@section('title', 'Detalhes da Distribuição')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0">Distribuição #{{ $distribuicao->id }}</h2>
                <a href="{{ route('distribuicoes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">Informações Gerais</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Beneficiário:</strong>
                            <p class="mb-0">{{ $distribuicao->beneficiario->nome ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status atual:</strong>
                            @php
                                $statusBadges = [
                                    'reservado' => 'warning',
                                    'enviado' => 'info',
                                    'entregue' => 'success',
                                ];
                                $badge = $statusBadges[$distribuicao->status] ?? 'secondary';
                            @endphp
                            <p class="mb-0"><span class="badge bg-{{ $badge }} text-uppercase">{{ $distribuicao->status }}</span></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Reservado em:</strong>
                            <p class="mb-0">{{ optional($distribuicao->reservado_em)->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Enviado em:</strong>
                            <p class="mb-0">{{ optional($distribuicao->enviado_em)->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Entregue em:</strong>
                            <p class="mb-0">{{ optional($distribuicao->entregue_em)->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Observações:</strong>
                            <p class="mb-0">{!! $distribuicao->observacoes ? nl2br(e($distribuicao->observacoes)) : '-' !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Itens Reservados</div>
                <div class="card-body">
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Categoria</th>
                                    <th class="text-center">Unidade</th>
                                    <th class="text-center">Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($distribuicao->items as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->categoria ?? '-' }}</td>
                                    <td class="text-center">{{ $item->pivot->unidade }}</td>
                                    <td class="text-center">{{ $formatQuantidade($item->pivot->quantidade, $item->pivot->unidade) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Atualizar Status</div>
                <div class="card-body">
                    <form action="{{ route('distribuicoes.update', $distribuicao) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach ($statusDisponiveis as $status)
                                    <option value="{{ $status }}" @selected($distribuicao->status === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea name="observacoes" id="observacoes" rows="3" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $distribuicao->observacoes) }}</textarea>
                                @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Salvar alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection