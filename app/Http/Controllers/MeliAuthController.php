<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MeliAuthController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    public function __construct()
    {
        $this->clientId = env('MELI_CLIENT_ID');
        $this->clientSecret = env('MELI_CLIENT_SECRET');
        $this->redirectUri = env('MELI_REDIRECT_URI');
    }

    /**
     * 🔗 Redireciona o usuário para autenticar no Mercado Livre
     */
    public function redirectToMeli()
    {
        $url = "https://auth.mercadolivre.com.br/authorization?" . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
        ]);

        return redirect()->away($url);
    }

    /**
     * 🎯 Callback do Mercado Livre → troca code por tokens
     */
    public function handleCallback(Request $request)
    {
        if ($request->has('code')) {
            $code = $request->get('code');

            // 🔥 Faz a troca do code pelos tokens
            $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ]);

            if ($response->ok()) {
                $data = $response->json();

                $user = Auth::user();
                $user->token_meli = $data['access_token'];
                $user->refresh_token_meli = $data['refresh_token'];
                $user->meli_user_id = $data['user_id'] ?? null;
                $user->save();

                return redirect()->route('dashboard')->with('success', 'Conta Mercado Livre conectada com sucesso!');
            } else {
                return redirect()->route('dashboard')->with('error', 'Erro ao conectar com Mercado Livre: ' . $response->body());
            }
        }

        return redirect()->route('dashboard')->with('error', 'Código de autorização não encontrado.');
    }
}
