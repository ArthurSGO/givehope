@extends('app')
@section('title', 'Detalhes do Beneficiário')
@section('content')
<div class="container">
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-itens-center">
                    <span>{{ $beneficiario->nome }}</span>
                    <span class="badge text-bg-light">ID {{ $beneficiario->id }}</span>
                </div>
                <div class="card-body">
                    @php
                    $enderecoLinha = trim(collect([
                    $beneficiario->endereco,
                    $beneficiario->numero,
                    $beneficiario->complemento,
                    ])->filter()->implode(', '));

                    $bairroCidade = collect([
                    $beneficiario->bairro,
                    trim(collect([
                    $beneficiario->cidade,
                    $beneficiario->estado,
                    ])->filter()->implode(' / ')),
                    ])->filter()->implode(' - ');
                    @endphp

                    <h6 class="text-uppercase text-muted">Informações pessoais</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Nome completo</dt>
                        <dd class="col-sm-8">{{ $beneficiario->nome }}</dd>

                        <dt class="col-sm-4">Data de nascimento</dt>
                        <dd class="col-sm-8">{{ $beneficiario->data_nascimento?->format('d/m/Y') ?? 'Não informada' }}</dd>

                        <dt class="col-sm-4">CPF</dt>
                        <dd class="col-sm-8">{{ $beneficiario->cpf_formatado ?? 'Não informado' }}</dd>

                        <dt class="col-sm-4">RG</dt>
                        <dd class="col-sm-8">{{ $beneficiario->rg ?? 'Não informado' }}</dd>
                    </dl>

                    <h6 class="text-uppercase text-muted mt-4">Contato</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Telefone</dt>
                        <dd class="col-sm-8">{{ $beneficiario->telefone_formatado ?? 'Não informado' }}</dd>

                        <dt class="col-sm-4">E-mail</dt>
                        <dd class="col-sm-8">{{ $beneficiario->email ?? 'Não informado' }}</dd>
                    </dl>

                    <h6 class="text-uppercase text-muted mt-4">Endereço</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Logradouro</dt>
                        <dd class="col-sm-8">{{ $enderecoLinha ?: 'Não informado' }}</dd>

                        <dt class="col-sm-4">Bairro / Cidade</dt>
                        <dd class="col-sm-8">{{ $bairroCidade ?: 'Não informado' }}</dd>

                        <dt class="col-sm-4">CEP</dt>
                        <dd class="col-sm-8">{{ $beneficiario->cep_formatado ?? 'Não informado' }}</dd>

                        <dt class="col-sm-4">Ponto de referência</dt>
                        <dd class="col-sm-8">{{ $beneficiario->ponto_referencia ?? 'Não informado' }}</dd>
                    </dl>

                    @if ($beneficiario->observacoes)
                    <h6 class="text-uppercase text-muted mt-4">Observações</h6>
                    <p class="mb-0">{!! nl2br(e($beneficiario->observacoes)) !!}</p>
                    @endif

                    <h6 class="text-uppercase text-muted mt-4">Registro</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Cadastrado em</dt>
                        <dd class="col-sm-8">{{ $beneficiario->created_at?->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Atualizado em</dt>
                        <dd class="col-sm-8">{{ $beneficiario->updated_at?->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
                <div class="card-footer d-flex justify-content-between flex-wrap gap-2">
                    <a href="{{ route('beneficiarios.index') }}" class="btn btn-secondary">Voltar para a lista</a>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('beneficiarios.edit', $beneficiario) }}" class="btn btn-primary">
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </a>
                        <form action="{{ route('beneficiarios.destroy', $beneficiario) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir este beneficiário? Esta ação não pode ser desfeita.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection