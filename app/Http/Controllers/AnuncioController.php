<?php

namespace App\Http\Controllers;

use App\Jobs\SyncAnunciosJob;
use App\Models\Anuncio;
use App\Services\MeliService;
use App\Services\MeliAuthService;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnuncioController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->tipo == 'super_admin') {
            $anuncios = Anuncio::all();
        } elseif ($user->tipo == 'consultor') {
            $userIds = $user->usuariosVinculados()->pluck('id');
            $userIds->push($user->id);
            $anuncios = Anuncio::whereIn('usuario_id', $userIds)->get();
        } else {
            $anuncios = Anuncio::where('usuario_id', $user->id)->get();
        }

        return view('anuncios.index', compact('anuncios'));
    }


    public function sync(Request $request)
    {
        $user = Auth::user();

        if (!$user->token_meli) {
            return redirect()->back()->with('error', 'Token do Mercado Livre não cadastrado.');
        }

        SyncAnunciosJob::dispatch($user);

        return redirect()->route('anuncios.index')->with('success', 'Sincronização iniciada. Os anúncios serão processados em segundo plano.');

    }

    public function getSyncStatus(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $status = Cache::get("sync_status_{$userId}", 'inativo');
        $progresso = Cache::get("sync_progress_{$userId}", ['atual' => 0, 'total' => 1]);

        $percentual = ($progresso['total'] > 0)
            ? ($progresso['atual'] / $progresso['total']) * 100
            : 0;

        return response()->json([
            'status' => $status,
            'progresso' => round($percentual),
            'atual' => $progresso['atual'],
            'total' => $progresso['total']
        ]);
    }


}
