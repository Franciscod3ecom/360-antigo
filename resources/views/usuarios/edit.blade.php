@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="container">
    <h4>Editar Usuário</h4>

    <div class="card p-4">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nome</label>
                <input type="text" name="nome" value="{{ old('nome', $usuario->nome) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Senha (Deixe em branco para manter)</label>
                <input type="password" name="password" class="form-control">
            </div>

            @if(Auth::user()->tipo == 'super_admin')
                <div class="mb-3">
                    <label>Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="usuario" {{ $usuario->tipo == 'usuario' ? 'selected' : '' }}>Usuário</option>
                        <option value="consultor" {{ $usuario->tipo == 'consultor' ? 'selected' : '' }}>Consultor</option>
                        <option value="super_admin" {{ $usuario->tipo == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
            @else
                <input type="hidden" name="tipo" value="consultor">
            @endif

            @if(Auth::user()->tipo == 'super_admin')
                <div class="mb-3">
                    <label>Consultor (opcional)</label>
                    <select name="consultor_id" class="form-select">
                        <option value="">Nenhum</option>
                        @foreach ($consultores as $consultor)
                            <option value="{{ $consultor->id }}" {{ $usuario->consultor_id == $consultor->id ? 'selected' : '' }}>{{ $consultor->nome }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="consultor_id" value="{{ Auth::user()->id }}">
            @endif

            <div class="d-grid">
                <button class="btn btn-meli">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
