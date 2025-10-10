@extends('app')
@section('title', 'Usuários')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Lista de Usuários do Sistema') }}
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">
                            <i class="fa fa-plus"></i> Cadastrar Novo Usuário
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">
                            Voltar
                        </a>

                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Data de Criação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->is_admin)
                                                <span class="badge bg-danger">Administrador Geral</span>
                                            @else
                                                <span class="badge bg-primary">Responsável Paróquia</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">Ver</a>
                                            <a href="#" class="btn btn-sm btn-warning">Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection