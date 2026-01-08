<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->tipo == 'super_admin') {
            $usuarios = User::all();
        } elseif ($user->tipo == 'consultor') {
            $usuarios = User::where('consultor_id', $user->id)->get();
        } else {
            abort(403, 'Acesso não autorizado');
        }

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->tipo == 'super_admin') {
            $consultores = User::where('tipo', 'consultor')->get();
        } elseif ($user->tipo == 'consultor') {
            $consultores = collect([$user]);
        } else {
            abort(403, 'Acesso não autorizado');
        }

        return view('usuarios.create', compact('consultores'));
    }

    public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'nome' => 'required',
        'email' => 'required|email|unique:usuarios,email',
        'password' => 'required', // senha vem com o nome 'password'
        'tipo' => 'required|in:usuario,consultor,super_admin',
    ]);

    // Cria o novo usuário
    $novoUsuario = new User();
    $novoUsuario->nome = $request->nome;
    $novoUsuario->email = $request->email;
    $novoUsuario->password = Hash::make($request->password);  // Cria o hash explicitamente
    $novoUsuario->tipo = $request->tipo;
    $novoUsuario->consultor_id = $request->consultor_id ?? null;
    $novoUsuario->token_meli = $request->token_meli;
    $novoUsuario->refresh_token_meli = $request->refresh_token_meli;
    $novoUsuario->save();

    return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
}

   public function edit(User $usuario)
{
    $user = Auth::user();

    // Verifica se o usuário é um Super Admin (que pode editar qualquer usuário)
    // Ou se o usuário logado é um consultor tentando editar o seu próprio perfil
    // Ou se o consultor está tentando editar um usuário vinculado a ele
    if (
        $user->tipo == 'super_admin' || 
        ($user->tipo == 'consultor' && $usuario->consultor_id == $user->id)  // Consultor editando seu próprio perfil ou um usuário vinculado
    ) {
        return view('usuarios.edit', compact('usuario'));
    }

    // Caso contrário, bloqueia o acesso
    abort(403, 'Acesso não autorizado');
}


    

    public function update(Request $request, User $usuario)
{
    $user = Auth::user();

    // Validação dos dados
    $request->validate([
        'nome' => 'required',
        'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
        'password' => 'nullable', // Senha pode ser nula
        'tipo' => 'required|in:usuario,consultor,super_admin',
    ]);

    // Verifica se o usuário tem permissão para editar
    if (
        $user->tipo == 'super_admin' ||
        ($user->tipo == 'consultor' && $usuario->consultor_id == $user->id)
    ) {
        $usuario->nome = $request->nome;
        $usuario->email = $request->email;

        // Se a senha foi fornecida, atualiza a senha
        if ($request->password) {
            $usuario->password = Hash::make($request->password);
        }

        // Permissão de alterar o tipo (somente para super_admin)
        if ($user->tipo == 'super_admin') {
            $usuario->tipo = $request->tipo;
        }

        // Permissão de alterar o consultor_id (somente para super_admin)
        if ($user->tipo == 'super_admin' && $request->consultor_id) {
            $usuario->consultor_id = $request->consultor_id;
        }

        // Atualização dos tokens do Mercado Livre
        $usuario->token_meli = $request->token_meli;
        $usuario->refresh_token_meli = $request->refresh_token_meli;

        // Salvar as alterações
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado!');
    }

    abort(403, 'Acesso não autorizado');
}

    public function destroy(User $usuario)
    {
        $user = Auth::user();

        if (
            $user->tipo == 'super_admin' ||
            ($user->tipo == 'consultor' && $usuario->consultor_id == $user->id)
        ) {
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('success', 'Usuário excluído!');
        }

        abort(403, 'Acesso não autorizado');
    }
}
