@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">
    <div class="card shadow border-0 rounded-4 mb-5">
        <div class="card-body">
            <h1 class="card-title mb-4">📦 Meus Anúncios</h1>

            <form method="POST" action="{{ route('anuncios.sync') }}" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-primary rounded-3" style="background-color: #300570; border-color: #300570;">
                    🔄 Sincronizar Anúncios
                </button>
            </form>

            @if($anuncios->count() > 0)
                <div class="table-responsive">
                    <table id="tabela-anuncios" class="table table-bordered table-hover align-middle text-nowrap" style="font-size: 0.875rem;">
                        <thead class="table-light" style="background-color: #300570; color: #ffe600;">
                            <tr>
                                <th>Imagem</th>
                                <th>ID do Item</th>
                                <th>Título</th>
                                <th>SKU</th>
                                <th>Estoque</th>
                                <th>Preço</th>
                                <th>Health</th>
                                <th>Tags</th>
                                <th>Categoria</th>
                                <th>Dimensões</th>
                                <th>Logísticas</th>
                                <th>Restrições ME2</th>
                                <th>Restrito</th>
                                <th>Última Atualização Categoria</th>
                                <th>Preço Médio Categoria</th>
                                <th>Tipo de Envio</th>
                                <th>Frete Grátis > R$79</th>
                                <th>Custo Envio</th>
                                <th>Peso Faturável</th>
                                <th>Status Peso</th>
                                <th>Frete Brasília</th>
                                <th>Frete SP</th>
                                <th>Frete Salvador</th>
                                <th>Frete Manaus</th>
                                <th>Frete POA</th>
                                <th>Status</th>
                                <th>Data Atualização</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anuncios as $anuncio)
                                <tr>
                                    <td>
                                        @if($anuncio->imagem)
                                            <img src="{{ $anuncio->imagem }}" alt="Imagem" style="height: 60px;">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $anuncio->item_id }}</td>
                                    <td>{{ $anuncio->titulo }}</td>
                                    <td>{{ $anuncio->sku }}</td>
                                    <td>{{ $anuncio->estoque }}</td>
                                    <td>R$ {{ number_format($anuncio->preco, 2, ',', '.') }}</td>
                                    <td>{{ $anuncio->health }}</td>
                                    <td style="white-space: pre-wrap;">{{ $anuncio->tags }}</td>
                                    <td>{{ $anuncio->categoria }}</td>
                                    <td>
                                        @php
                                            $dim = json_decode($anuncio->dimensoes, true);
                                        @endphp

                                        @if($dim)
                                            Altura: <b> {{ $dim['height'] ?? '?' }} cm </b> Largura: <b> </strong>{{ $dim['width'] ?? '?' }} cm  </b> Comprimento: <b>{{ $dim['length'] ?? '?' }} cm  </b> Peso: <b>{{ $dim['weight'] ?? '?' }} g </b>
                                        @else
                                            <span class="text-muted">Não informado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $logs = json_decode($anuncio->logisticas, true);
                                        @endphp

                                        @if($logs && is_array($logs))
                                            {{ collect($logs)->map(function($log) {
                                                return strtoupper($log['mode']) . ': ' . implode(', ', $log['types'] ?? []);
                                            })->implode(' | ') }}
                                        @else
                                            <span class="text-muted">Não informado</span>
                                        @endif
                                    </td>

                                    <td style="white-space: pre-wrap;">{{ $anuncio->restricoes_me2 }}</td>
                                    <td>{{ $anuncio->restrito }}</td>
                                    <td>{{ $anuncio->ultima_atualizacao_categoria }}</td>
                                    <td>{{ $anuncio->preco_medio_categoria }}</td>
                                    <td>{{ $anuncio->tipo_envio }}</td>
                                    <td>{{ $anuncio->frete_gratis_acima_79 }}</td>
                                    <td>{{ $anuncio->custo_envio }}</td>
                                    <td>{{ $anuncio->peso_faturavel }}</td>
                                    <td>{{ $anuncio->status_peso }}</td>
                                    <td>{{ $anuncio->frete_brasilia }}</td>
                                    <td>{{ $anuncio->frete_sao_paulo }}</td>
                                    <td>{{ $anuncio->frete_salvador }}</td>
                                    <td>{{ $anuncio->frete_manaus }}</td>
                                    <td>{{ $anuncio->frete_porto_alegre }}</td>
                                    <td>{{ $anuncio->status }}</td>
                                    <td>{{ (new DateTime($anuncio->updated_at))->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Nenhum anúncio encontrado.</p>
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $('#tabela-anuncios').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: '📥 Exportar CSV',
                    className: 'btn btn-success mb-3'
                }
            ],
            responsive: true,
            ordering: true,
            pageLength: 25
        });
    });
</script>
@endpush
