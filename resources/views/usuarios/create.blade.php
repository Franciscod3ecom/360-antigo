@extends('layouts.app')

@section('title', 'Cadastrar Novo Usuário')

@section('content')
<h4>Cadastrar Novo Usuário</h4>
<div class="card p-4">
    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Senha</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <!-- Campo Tipo: Pré-definido como 'Usuário' para o consultor, e editável para o super_admin -->
        @if(Auth::user()->tipo != 'consultor')
            <div class="mb-3">
                <label>Tipo</label>
                <select name="tipo" class="form-select" required>
                    <option value="usuario" {{ old('tipo') == 'usuario' ? 'selected' : '' }}>Usuário</option>
                    <option value="consultor" {{ old('tipo') == 'consultor' ? 'selected' : '' }}>Consultor</option>
                    <option value="super_admin" {{ old('tipo') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
        @else
            <input type="hidden" name="tipo" value="consultor">
        @endif

        <!-- Campo Consultor: Escondido para consultor, mas visível para outros -->
        @if(Auth::user()->tipo != 'consultor')
            <div class="mb-3">
                <label>Consultor (opcional)</label>
                <select name="consultor_id" class="form-select">
                    <option value="">Nenhum</option>
                    @foreach ($consultores as $consultor)
                        <option value="{{ $consultor->id }}" {{ old('consultor_id') == $consultor->id ? 'selected' : '' }}>{{ $consultor->nome }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <input type="hidden" name="consultor_id" value="{{ Auth::user()->id }}">
        @endif

        <div class="mb-3">
            <label>Token Mercado Livre</label>
            <input type="text" name="token_meli" class="form-control">
        </div>

        <div class="mb-3">
            <label>Refresh Token Mercado Livre</label>
            <input type="text" name="refresh_token_meli" class="form-control">
        </div>

        <div class="d-grid">
            <button class="btn btn-meli">Salvar</button>
        </div>
    </form>
</div>
@endsection
