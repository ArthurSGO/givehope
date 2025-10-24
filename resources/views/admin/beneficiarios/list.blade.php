@extends('app')
@section('title', 'Beneficiários')
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
                    {{ __('Lista de Beneficiários do Sistema') }}
                </div>
                <div class="card-body">
                    <a href="{{ route('beneficiarios.create') }}" class="btn btn-success mb-3">
                        <i class="fa fa-plus"></i> Cadastrar Novo Beneficiário
                    </a>
                    <a href="{{ route('painel.dashboard') }}" class="btn btn-secondary mb-3 ms-1">
                        Voltar
                    </a>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Endereço</th>
                                    <th>Cidade / UF</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($beneficiarios as $beneficiario)
                                <tr>
                                    <td>{{ $beneficiario->id }}</td>
                                    <td>{{ $beneficiario->nome }}</td>
                                    <td>{{ $beneficiario->telefone_formatado ?? 'Não informado' }}</td>
                                    <td>
                                        {{ $beneficiario->endereco }}
                                        @if ($beneficiario->numero)
                                        , {{ $beneficiario->numero }}
                                        @endif
                                        @if ($beneficiario->bairro)
                                        <br><small class="text-muted">Bairro: {{ $beneficiario->bairro }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($beneficiario->cidade || $beneficiario->estado)
                                        {{ $beneficiario->cidade ?? 'Cidade não informada' }}
                                        @if ($beneficiario->estado)
                                        / {{ $beneficiario->estado }}
                                        @endif
                                        @if ($beneficiario->cep_formatado)
                                        <br><small class="text-muted">CEP: {{ $beneficiario->cep_formatado }}</small>
                                        @endif
                                        @else
                                        Não informado
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('beneficiarios.show', $beneficiario) }}"
                                            class="btn btn-info btn-sm m-1">
                                            <i class="fa-solid fa-eye"></i> Detalhes
                                        </a>
                                        <a href="{{ route('beneficiarios.edit', $beneficiario) }}"
                                            class="btn btn-primary btn-sm m-1">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm m-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal"
                                            data-delete-url="{{ route('beneficiarios.destroy', $beneficiario) }}">
                                            <i class="fa-solid fa-trash"></i> Excluir
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum beneficiário cadastrado.</td>
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

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este beneficiário? <br>
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
@endsection

@push('scripts')
<script>
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');

    confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const deleteUrl = button.getAttribute('data-delete-url');
        const deleteForm = document.getElementById('delete-form');
        deleteForm.setAttribute('action', deleteUrl);
    });
</script>
@endpush