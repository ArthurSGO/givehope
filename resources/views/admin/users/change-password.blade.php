@extends('app') 
@section('title', 'Alterar Senha')
@section('content')
<div class="container">
    <h3>Alterar Senha</h3>
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="current_password" class="form-label">Senha atual</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nova senha</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Confirmar nova senha</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('painel.dashboard') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
