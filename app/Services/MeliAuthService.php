<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class MeliAuthService
{
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->clientId = env('MELI_CLIENT_ID');
        $this->clientSecret = env('MELI_CLIENT_SECRET');
    }

    /**
     * 🔄 Faz o refresh do token do usuário
     */
    public function refreshToken(User $user)
    {
        if (!$user->refresh_token_meli) {
            throw new \Exception('Usuário não possui refresh_token do Mercado Livre.');
        }

        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $user->refresh_token_meli,
        ]);

        if ($response->ok()) {
            $data = $response->json();

            $user->token_meli = $data['access_token'];
            $user->refresh_token_meli = $data['refresh_token'] ?? $user->refresh_token_meli;
            $user->save();

            return $data['access_token'];
        } else {
            throw new \Exception('Erro ao renovar token: ' . $response->body());
        }
    }
}
