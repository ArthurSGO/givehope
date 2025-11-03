@extends('app')
@section('title', 'Consultar doações')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="text-center mb-4">
            <h1 class="fw-bold">Consulte suas doações</h1>
            <p class="text-muted mb-0">Digite seu CPF ou CNPJ para verificar doações registradas pelas paróquias parceiras.</p>
        </div>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('seek') }}" class="row g-3 align-itens-end">
                    <div class="col-md-8">
                        <label for="cpf" class="form-label">CPF ou CNPJ</label>
                        <input type="text" class="form-control @if($errorMessage) is-invalid @endif" id="cpf" name="cpf"
                            value="{{ old('cpf', $cpf) }}" inputmode="numeric" autocomplete="off" placeholder="000.000.000-00">
                        @if($errorMessage)
                        <div class="invalid-feedback d-block">
                            {{ $errorMessage }}
                        </div>
                        @endif
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Consultar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($searched)
        @if($doador)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-itens-md-center">
                    <div>
                        <h2 class="h4 mb-1">{{ $doador->nome }}</h2>
                        <p class="mb-0 text-muted">CPF: {{ $doador->documento_formatado ?? 'Não informado' }}</p>
                    </div>
                    <div class="text-md-end mt-3 mt-md-0">
                        <span class="badge bg-secondary">{{ $doacoes->count() }} {{ \Illuminate\Support\Str::plural('doação', $doacoes->count()) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($doacoes->isNotEmpty())
        @foreach($doacoes as $doacao)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-itens-lg-center mb-3">
                    <div class="d-flex align-itens-center">
                        <span class="badge bg-{{ $doacao->status_badge }} me-2">{{ $doacao->status_distribuicao }}</span>
                        <span class="text-muted"><i class="fa-regular fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($doacao->data_doacao)->format('d/m/Y') }}</span>
                    </div>
                    <span class="badge bg-info text-dark mt-2 mt-lg-0 text-uppercase">{{ $doacao->paroquia->nome_fantasia ?? 'Paróquia não informada' }}</span>
                </div>

                <h3 class="h5">{{ $doacao->tipo === 'dinheiro' ? 'Doação em dinheiro' : 'Doação de itens' }}</h3>

                @if($doacao->tipo === 'dinheiro')
                <p class="fs-5 fw-semibold text-success mb-3">{{ $doacao->quantidade_formatada ?? 'Valor não informado' }}</p>
                @else
                @if(isset($doacao->detalhes_estoque) && $doacao->detalhes_estoque->isNotEmpty())
                <p class="text-muted mb-2">Acompanhamento do estoque para cada item doado:</p>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-borderless align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Doado</th>
                                <th class="text-end">Reservado</th>
                                <th class="text-end">Enviado</th>
                                <th class="text-end">Entregue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doacao->detalhes_estoque as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['nome'] }}</td>
                                <td class="text-end">{{ $item['quantidade_doada'] }}</td>
                                <td class="text-end">{{ $item['reservado'] }}</td>
                                <td class="text-end">{{ $item['enviado'] }}</td>
                                <td class="text-end">{{ $item['entregue'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="small text-muted mb-3">Reservas, envios e entregas são distribuídos considerando a ordem em que as doações foram registradas pela paróquia. O saldo disponível indica o que ainda pode ser direcionado desta doação específica, enquanto o saldo na paróquia mostra o estoque total desse item disponível para todos os beneficiários.</p>
                @else
                <p class="text-muted">Nenhum item detalhado para esta doação.</p>
                @endif
                @endif

                <div class="mt-3">
                    <p class="mb-1 text-muted">Observações da paróquia</p>
                    <div class="p-3 border rounded bg-light">
                        {{ $doacao->descricao ? $doacao->descricao : 'Ainda não há informações sobre a distribuição desta doação.' }}
                    </div>
                </div>

                <p class="small text-muted mt-3 mb-0">
                    Última atualização: {{ optional($doacao->updated_at)->format('d/m/Y H:i') ?? 'Não disponível' }}
                </p>
            </div>
        </div>
        @endforeach
        @else
        <div class="alert alert-info" role="alert">
            Não encontramos doações registradas para este CPF ainda. Assim que a paróquia registrar uma doação,
            ela aparecerá aqui.
        </div>
        @endif
        @elseif(!$errorMessage)
        <div class="alert alert-warning" role="alert">
            Não encontramos nenhum cadastro de doador com o CPF informado.
        </div>
        @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('cpf');
        if (!input) return;

        const formatDocument = (value) => {
            const digits = (value || '').replace(/\D/g, '').slice(0, 14);

            if (digits.length <= 11) {
                return digits
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }

            return digits
                .replace(/(\d{2})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1/$2')
                .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
        };

        const handleInput = () => {
            const start = input.selectionStart;
            const formatted = formatDocument(input.value);
            input.value = formatted;
            if (typeof start === 'number') {
                input.setSelectionRange(formatted.length, formatted.length);
            }
        };

        input.addEventListener('input', handleInput);
        handleInput();
    });
</script>
@endpush