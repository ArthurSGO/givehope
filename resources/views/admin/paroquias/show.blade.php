@extends('app')
@section('title', 'Detalhes da Paróquia')
@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detalhes da Paróquia: {{ $paroquia->nome_fantasia ?? $paroquia->nome }}</h5>
            <a href="{{ route('paroquias.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar para a Lista
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Dados da Instituição</h5>
                    <hr>
                    <p><strong>Nome Fantasia:</strong> {{ $paroquia->nome_fantasia ?? 'Não informado' }}</p>
                    <p><strong>Razão Social:</strong> {{ $paroquia->nome }}</p>
                    <p><strong>CNPJ:</strong> {{ $paroquia->cnpj }}</p>
                    <p><strong>Data de Abertura:</strong> {{ $paroquia->abertura }}</p>
                    <p><strong>Porte:</strong> {{ $paroquia->porte }}</p>
                    <p><strong>Natureza Jurídica:</strong> {{ $paroquia->natureza_juridica }}</p>
                    <p><strong>Situação:</strong> <span class="badge bg-success">{{ $paroquia->situacao }}</span></p>
                </div>

                <div class="col-md-6">
                    <h5>Contato e Endereço</h5>
                    <hr>
                    <p><strong>E-mail:</strong> {{ $paroquia->email ?? 'Não informado' }}</p>
                    <p><strong>Telefone:</strong> {{ $paroquia->telefone }}</p>
                    <br>
                    <p><strong>Endereço:</strong> {{ $paroquia->logradouro }}, {{ $paroquia->numero ?? 'S/N' }}</p>
                    <p><strong>Bairro:</strong> {{ $paroquia->bairro }}</p>
                    <p><strong>CEP:</strong> {{ $paroquia->cep }}</p>
                    <p><strong>Cidade/Estado:</strong> {{ $paroquia->cidade }}/{{ $paroquia->estado }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Usuários Vinculados ({{ $paroquia->users->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paroquia->users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('users.edit', ['user' => $user->id, 'redirect_to' => route('paroquias.show', $paroquia)]) }}" class="btn btn-primary btn-sm">Editar Usuário</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhum usuário vinculado a esta paróquia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Doações Recebidas ({{ $paroquia->doacoes->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Doador</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paroquia->doacoes as $doacao)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($doacao->data_doacao)->format('d/m/Y') }}</td>
                            <td>{{ $doacao->doador->nome ?? 'Anônimo' }}</td>
                            <td>{{ ucfirst($doacao->tipo) }}</td>
                            <td>
                                @if($doacao->unidade == 'R$')
                                {{ $doacao->unidade }} {{ number_format($doacao->quantidade, 2, ',', '.') }}
                                @else
                                {{ $doacao->quantidade }} {{ $doacao->unidade }}(s)
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma doação registrada para esta paróquia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection