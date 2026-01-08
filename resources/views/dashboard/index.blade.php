@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row g-4">

        {{-- Card Status Mercado Livre --}}
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Status Mercado Livre</h5>
                    @if(Auth::user()->token_meli)
                        <p class="text-success fw-bold">🟢 Conectado</p>
                    @else
                        <p class="text-danger fw-bold">🔴 No Conectado</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Total de Anúncios --}}
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Anúncios</h5>
                    <p class="fw-bold display-6">{{ $totalAnuncios }}</p>
                </div>
            </div>
        </div>

        {{-- Card Ações --}}
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Ações</h5>
                    <div class="d-grid gap-2">
                        @if(!Auth::user()->token_meli)
                            <!-- Botão para conectar ao Mercado Livre -->
                            <a href="{{ route('meli.login') }}" class="btn btn-warning rounded-3">🔗 Conectar Mercado Livre</a>
                        @else
                            <!-- Formulário para desvincular a conta do Mercado Livre -->
                            <form method="POST" action="{{ route('meli.desvincular') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-3">🔴 Desvincular Mercado Livre</button>
                            </form>

                            <!-- Botão para sincronizar os anúncios -->
                            <form method="POST" action="{{ route('anuncios.sync') }}">
                                @csrf
                                <button type="submit" id="sync-button" class="btn btn-primary rounded-3" style="background-color: #300570; border-color: #300570;">🔄 Sincronizar Anúncios</button>
                            </form>
                            <div class="progress mt-3 d-none" id="sync-progress-wrapper" style="height: 25px;">
                                <div id="sync-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated fw-bold"
                                    role="progressbar" style="width: 0%">0%</div>
                            </div>

                        @endif

                        <!-- Botão para ver os anúncios -->
                        <a href="{{ route('anuncios.index') }}" class="btn btn-outline-dark rounded-3">📦 Ver Anúncios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimos anúncios --}}
    <div class="card mt-5 shadow border-0 rounded-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Últimos Anúncios</h5>
            @if($anuncios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Imagem</th>
                                <th>Título</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Categoria</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anuncios as $anuncio)
                                <tr>
                                    <td><img src="{{ $anuncio->imagem }}" alt="Imagem" style="height: 60px;"></td>
                                    <td>{{ $anuncio->titulo }}</td>
                                    <td>R$ {{ number_format($anuncio->preco, 2, ',', '.') }}</td>
                                    <td>{{ $anuncio->estoque }}</td>
                                    <td>{{ $anuncio->categoria }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>Nenhum anúncio encontrado.</p>
            @endif
        </div>
    </div>
</div>
@endsection
