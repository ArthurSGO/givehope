@extends('app') 
@section('title', 'Cadastro')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cadastrar Novo Usuário (Paróquia)') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}" id="user-form">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nome') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-mail') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Senha') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirmar Senha') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="is_admin" class="col-md-4 col-form-label text-md-end">{{ __('É Administrador?') }}</label>
                            <div class="col-md-6">
                                <select id="is_admin" class="form-control @error('is_admin') is-invalid @enderror" name="is_admin" required>
                                    <option value="0" selected>Não (Responsável Paróquia)</option>
                                    <option value="1">Sim (Administrador Geral)</option>
                                </select>
                                @error('is_admin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="paroquia_id" class="col-md-4 col-form-label text-md-end">Paróquia Responsável</label>
                            <div class="col-md-6">
                                <select name="paroquia_id" id="paroquia_id" class="form-control @error('paroquia_id') is-invalid @enderror">
                                    <option value="">Selecione uma paróquia (Obrigatório para Responsáveis)</option>
                                    @foreach ($paroquias as $paroquia)
                                        <option value="{{ $paroquia->id }}" {{ old('paroquia_id') == $paroquia->id ? 'selected' : '' }}>
                                            {{ $paroquia->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('paroquia_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" id="submit-button" disabled>
                                    Cadastrar Usuário
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function()
    {
        const userForm = document.getElementById('user-form');
        const submitButton = document.getElementById('submit-button');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password-confirm');
        const isAdminSelect = document.getElementById('is_admin');

        const fieldsToValidate = [
            nameInput,
            emailInput,
            passwordInput,
            passwordConfirmInput,
            isAdminSelect
        ];

        function validateForm()
        {
            let isFormValid = true;

            for (const field of fieldsToValidate)
            {
                if (field.value.trim() === '')
                {
                    isFormValid = false;
                    break;
                }
            }

            if (isFormValid && passwordInput.value !== passwordConfirmInput.value)
            {
                isFormValid = false;
            }

            submitButton.disabled = !isFormValid;
        }

        fieldsToValidate.forEach(field => {
            field.addEventListener('input', validateForm);
        });
    });
</script>
@endpush
@endsection