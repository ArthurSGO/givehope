@extends('app')
@section('title', 'Logs de Atividade')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Logs de Auditoria do Sistema') }}
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">
                        <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
                    </a>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Data</th>
                                    <th style="width: 15%;">Realizado por</th>
                                    <th style="width: 20%;">Ação</th>
                                    <th style="width: 50%;">Detalhes das Alterações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $activity->causer->name ?? 'Sistema' }}</td>
                                        <td>{{ $activity->description }}</td>
                                        <td>
                                            @if ($activity->properties->has('old') || $activity->properties->has('attributes'))
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($activity->properties['attributes'] as $key => $value)
                                                        <li>
                                                            <strong>{{ ucfirst($key) }}:</strong>
                                                            @if ($activity->properties->has('old') && isset($activity->properties['old'][$key]))
                                                                <span class="text-danger" style="text-decoration: line-through;">{{ $activity->properties['old'][$key] }}</span> →
                                                            @endif
                                                            <span class="text-success">{{ $value }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum log de atividade registrado.</td>
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
@endsection