@extends('app')
@section('title', 'Editar Beneficiário')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Beneficiário') }}</div>
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
                    $estados = [
                    'AC' => 'Acre',
                    'AL' => 'Alagoas',
                    'AP' => 'Amapá',
                    'AM' => 'Amazonas',
                    'BA' => 'Bahia',
                    'CE' => 'Ceará',
                    'DF' => 'Distrito Federal',
                    'ES' => 'Espírito Santo',
                    'GO' => 'Goiás',
                    'MA' => 'Maranhão',
                    'MT' => 'Mato Grosso',
                    'MS' => 'Mato Grosso do Sul',
                    'MG' => 'Minas Gerais',
                    'PA' => 'Pará',
                    'PB' => 'Paraíba',
                    'PR' => 'Paraná',
                    'PE' => 'Pernambuco',
                    'PI' => 'Piauí',
                    'RJ' => 'Rio de Janeiro',
                    'RN' => 'Rio Grande do Norte',
                    'RS' => 'Rio Grande do Sul',
                    'RO' => 'Rondônia',
                    'RR' => 'Roraima',
                    'SC' => 'Santa Catarina',
                    'SP' => 'São Paulo',
                    'SE' => 'Sergipe',
                    'TO' => 'Tocantins',
                    ];
                    @endphp

                    <form action="{{ route('beneficiarios.update', $beneficiario) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h5 class="fw-semibold border-bottom pb-2 mb-3">Informações pessoais</h5>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nome" class="form-label">Nome completo</label>
                                <input
                                    type="text"
                                    class="form-control @error('nome') is-invalid @enderror"
                                    id="nome"
                                    name="nome"
                                    value="{{ old('nome', $beneficiario->nome) }}"
                                    required>
                                @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="data_nascimento" class="form-label">Data de nascimento</label>
                                <input
                                    type="date"
                                    class="form-control @error('data_nascimento') is-invalid @enderror"
                                    id="data_nascimento"
                                    name="data_nascimento"
                                    value="{{ old('data_nascimento', optional($beneficiario->data_nascimento)->format('Y-m-d')) }}">
                                @error('data_nascimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cpf" class="form-label">CPF</label>
                                <input
                                    type="text"
                                    class="form-control @error('cpf') is-invalid @enderror"
                                    id="cpf"
                                    name="cpf"
                                    value="{{ old('cpf', $beneficiario->cpf_formatado ?? $beneficiario->cpf) }}"
                                    placeholder="000.000.000-00">
                                @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opcional. Informe apenas se disponível.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="rg" class="form-label">RG</label>
                                <input
                                    type="text"
                                    class="form-control @error('rg') is-invalid @enderror"
                                    id="rg"
                                    name="rg"
                                    value="{{ old('rg', $beneficiario->rg) }}">
                                @error('rg')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-semibold border-bottom pb-2 my-4">Contato</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input
                                    type="text"
                                    class="form-control @error('telefone') is-invalid @enderror"
                                    id="telefone"
                                    name="telefone"
                                    value="{{ old('telefone', $beneficiario->telefone_formatado ?? $beneficiario->telefone) }}"
                                    placeholder="(00) 00000-0000">
                                @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opcional, mas ajuda a manter contato.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $beneficiario->email) }}"
                                    placeholder="nome@exemplo.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-semibold border-bottom pb-2 my-4">Endereço</h5>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="endereco" class="form-label">Logradouro</label>
                                <input
                                    type="text"
                                    class="form-control @error('endereco') is-invalid @enderror"
                                    id="endereco"
                                    name="endereco"
                                    value="{{ old('endereco', $beneficiario->endereco) }}"
                                    required>
                                @error('endereco')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="numero" class="form-label">Número</label>
                                <input
                                    type="text"
                                    class="form-control @error('numero') is-invalid @enderror"
                                    id="numero"
                                    name="numero"
                                    value="{{ old('numero', $beneficiario->numero) }}">
                                @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input
                                    type="text"
                                    class="form-control @error('bairro') is-invalid @enderror"
                                    id="bairro"
                                    name="bairro"
                                    value="{{ old('bairro', $beneficiario->bairro) }}">
                                @error('bairro')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input
                                    type="text"
                                    class="form-control @error('complemento') is-invalid @enderror"
                                    id="complemento"
                                    name="complemento"
                                    value="{{ old('complemento', $beneficiario->complemento) }}"
                                    placeholder="Casa, apartamento, ponto de referência...">
                                @error('complemento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input
                                    type="text"
                                    class="form-control @error('cidade') is-invalid @enderror"
                                    id="cidade"
                                    name="cidade"
                                    value="{{ old('cidade', $beneficiario->cidade) }}">
                                @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select
                                    class="form-select @error('estado') is-invalid @enderror"
                                    id="estado"
                                    name="estado">
                                    <option value="">Selecione</option>
                                    @foreach ($estados as $sigla => $estado)
                                    <option value="{{ $sigla }}" {{ old('estado', $beneficiario->estado) === $sigla ? 'selected' : '' }}>{{ $sigla }} - {{ $estado }}</option>
                                    @endforeach
                                </select>
                                @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input
                                    type="text"
                                    class="form-control @error('cep') is-invalid @enderror"
                                    id="cep"
                                    name="cep"
                                    value="{{ old('cep', $beneficiario->cep_formatado ?? $beneficiario->cep) }}"
                                    placeholder="00000-000">
                                @error('cep')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="ponto_referencia" class="form-label">Ponto de referência</label>
                                <input
                                    type="text"
                                    class="form-control @error('ponto_referencia') is-invalid @enderror"
                                    id="ponto_referencia"
                                    name="ponto_referencia"
                                    value="{{ old('ponto_referencia', $beneficiario->ponto_referencia) }}"
                                    placeholder="Informações que ajudem a localizar o endereço">
                                @error('ponto_referencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-semibold border-bottom pb-2 my-4">Observações adicionais</h5>

                        <div class="mb-4">
                            <label for="observacoes" class="form-label">Histórico ou necessidades específicas</label>
                            <textarea
                                class="form-control @error('observacoes') is-invalid @enderror"
                                id="observacoes"
                                name="observacoes"
                                rows="4"
                                placeholder="Registre aqui informações importantes sobre o beneficiário (situação familiar, necessidades urgentes, etc.).">{{ old('observacoes', $beneficiario->observacoes) }}</textarea>
                            @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Campo livre para anotar informações que facilitem futuras doações.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('beneficiarios.show', $beneficiario) }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(function() {
        const phoneMask = function(val) {
            const numbers = val.replace(/\D/g, '');
            return numbers.length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        };

        $('#telefone').mask(phoneMask, {
            onKeyPress: function(val, e, field, options) {
                field.mask(phoneMask.apply({}, arguments), options);
            }
        });

        $('#cpf').mask('000.000.000-00', {
            clearIfNotMatch: true
        });
        $('#cep').mask('00000-000', {
            clearIfNotMatch: true
        });
    });
</script>
@endpush