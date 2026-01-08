<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MeliAuthService;
use App\Services\MeliService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncAnunciosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        try {
            $meliAuth = new MeliAuthService();
            $novoToken = $meliAuth->refreshToken($this->user);
            $meli = new MeliService($novoToken);
            $meli->salvarTodosAnunciosCompletos($this->user->id);
        } catch (\Exception $e) {
            Log::error('Erro em SyncAnunciosJob', ['mensagem' => $e->getMessage()]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Cache::put("sync_status_{$this->user->id}", 'falhou', now()->addMinutes(10));
        Log::error('Job falhou definitivamente', ['erro' => $exception->getMessage()]);
    }

}
