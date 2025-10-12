@extends('app')
@section('title', 'Início')
@section('content')

<div class="container">
    <div class="row justify-content-center mb-4">
        <div class="col-auto">
            <h1>Bem Vindo a GiveHope!</h1>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col mb-5">
            <a href="{{ route('soon') }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        Em Breve
                    </div>
                    <div class="card-body">
                        <p class="card-text">Futuros eventos.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col mb-5">
            <a href="{{ route('inprogress') }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        Em Andamento
                    </div>
                    <div class="card-body">
                        <p class="card-text">Doações ainda sendo recebidas e em processo de distribuição.</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col mb-5">
            <a href="{{ route('finished') }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        Finalizados
                    </div>
                    <div class="card-body">
                        <p class="card-text">Doações já distruibuídas</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row justify-content-center mb-4 mt-5">
        <div class="col-auto">
            <h2>Consulte Suas Doações</h2>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header">
                    Acompanhe suas doações
                </div>
                <div class="card-body row justify-content-center">
                    <p class="card-text">
                        Nossa prioridade é a transparência! <br>
                        Veja e acompanhe para onde suas doações foram!
                    </p>
                    <a href="{{ route('seek') }}" class="text-decoration-none btn btn-success mb-3">Buscar</a>
                </div>
            </div>
        </div>
    </div>



    <div class="row justify-content-center mb-4 mt-5">
        <div class="col-auto">
            <h2>Gráfico Mensal Geral</h2>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col mb-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header">
                    Total em Dinheiro Doado (R$)
                </div>
                <div class="card-body">
                    <p class="card-text">Mui Dinero</p>
                </div>
            </div>
        </div>

        <div class="col mb-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header">
                    Total em Itens Doado (UN)
                </div>
                <div class="card-body">
                    <p class="card-text">Muta Cosa</p>
                </div>
            </div>
        </div>
        
        <div class="col mb-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header">
                    Total em Alimentos Doado (KG)
                </div>
                <div class="card-body">
                    <p class="card-text">Muta Cosa</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
