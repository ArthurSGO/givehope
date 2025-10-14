@extends('app')
@section('title', 'Usuários')
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
            {{ session('success') }}
            </div>
        @endif


        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
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
                                    <th>Paróquia</th>
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
                                        <td>
                                            @if ($user->paroquia)
                                                {{ $user->paroquia->nome }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal"
                                                    data-delete-url="{{ route('admin.users.delete', $user->id) }}">
                                                <i class="fa-solid fa-trash"></i> Excluir
                                            </button>
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

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir este usuário? <br>
                    <strong>Esta ação não pode ser desfeita.</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                    <form id="delete-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script>
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');

        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            
            const button = event.relatedTarget;

            const deleteUrl = button.getAttribute('data-delete-url');

            const deleteForm = document.getElementById('delete-form')

            deleteForm.setAttribute('action', deleteUrl);
        });
    </script>
@endpush
@endsection