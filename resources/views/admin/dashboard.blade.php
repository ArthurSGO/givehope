@extends('layouts.app')
@section('title', 'Administração')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard do Administrador') }}</div>

                    <div class="card-body">
                        @if (session('success'))
                           <div class="alert alert-success" role="alert">
                              {{ session('success') }}
                            </div>
                        @endif
                        
                        Bem-vindo à área de administração do GiveHope! 
                        <br><br>
                        
                        <h4>Gerenciamento de Usuários</h4>
                        <a href="{{ route('admin.users.list') }}" class="btn btn-success mb-3">
                            Ver todos os usuários
                        </a>
                        <a href="{{ route('paroquias.index') }}" class="btn btn-success mb-3">
                            Ver todas as paróquias
                        </a>
                        
                        <hr>
                        Aqui você poderá gerenciar todas as paróquias cadastradas, verificar dados do sistema, etc.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection