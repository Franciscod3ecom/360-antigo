<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->tipo == 'super_admin') {
            $anuncios = Anuncio::latest()->take(5)->get();
            $totalAnuncios = Anuncio::count();
        } elseif ($user->tipo == 'consultor') {
            $userIds = $user->usuariosVinculados()->pluck('id');
            $userIds->push($user->id);
            $anuncios = Anuncio::whereIn('usuario_id', $userIds)->latest()->take(5)->get();
            $totalAnuncios = Anuncio::whereIn('usuario_id', $userIds)->count();
        } else {
            $anuncios = Anuncio::where('usuario_id', $user->id)->latest()->take(5)->get();
            $totalAnuncios = Anuncio::where('usuario_id', $user->id)->count();
        }

        return view('dashboard.index', compact('anuncios', 'totalAnuncios'));
    }
}
