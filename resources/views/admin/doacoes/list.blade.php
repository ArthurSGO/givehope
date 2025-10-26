    @extends('app')
    @section('title', 'Doações da Paróquia')
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
                    <div class="card-header">
                        {{ __('Registro de Doações da Paróquia') }}
                    </div>
                    <div class="card-body">

                        <a href="{{ route('painel.dashboard') }}" class="btn btn-secondary mb-3">
                            Voltar ao Painel
                        </a>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Doador</th>
                                        <th>Tipo</th>
                                        <th>Quantidade</th>
                                        <th>Descrição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($doacoes as $doacao)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($doacao->data_doacao)->format('d/m/Y') }}</td>
                                        <td>{{ $doacao->doador->nome ?? 'Anônimo' }}</td>
                                        <td>{{ ucfirst($doacao->tipo) }}</td>
                                        <td>
                                            @if($doacao->unidade == 'R$')
                                            {{ $doacao->unidade }} {{ number_format($doacao->quantidade, 2, ',', '.') }}
                                            @elseif ($doacao->items->count() > 1)
                                            Múltiplos itens
                                            @else
                                            {{ $doacao->quantidade }} {{ $doacao->unidade }}
                                            @endif
                                        </td>
                                        <td>{{ $doacao->descricao ?? '-' }}</td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('doacoes.show', $doacao->id) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa-solid fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('doacoes.edit', $doacao->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhuma doação registrada para esta paróquia.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection