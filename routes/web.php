<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\AuthController;
use App\Models\User;

//  Login / Logout
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
 //  OAuth Mercado Livre
Route::get('/meli/login', [App\Http\Controllers\MeliAuthController::class, 'redirectToMeli'])->name('meli.login');
Route::get('/meli/callback', [App\Http\Controllers\MeliAuthController::class, 'handleCallback'])->name('meli.callback');
Route::post('/meli/desvincular', [AuthController::class, 'desvincularMeli'])->name('meli.desvincular');

Route::post('/meli/notification', function () {
    return response()->json(['message' => 'Recebido'], 200);
})->name('meli.notification');

Route::get('/resetar-senha', function () {
    $email = 'teste@teste.com';
    $novaSenha = '1234';

    $user = User::where('email', $email)->first();

    if (!$user) {
        return "Usurio não encontrado para o email: $email";
    }

    $user->password = Hash::make($novaSenha);
    $user->save();

    return "Senha do usuário $email atualizada para '$novaSenha' com hash bcrypt.";
});

// 🔒 Rotas protegidas
Route::middleware(['auth'])->group(function () {

    // 🏠 Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //  Usuários
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    // 📦 Anúncios
    Route::get('/anuncios', [AnuncioController::class, 'index'])->name('anuncios.index');
    Route::post('/anuncios/sync', [AnuncioController::class, 'sync'])->name('anuncios.sync');
    Route::get('/sync/status', [AnuncioController::class, 'getSyncStatus'])->name('sync.status');

});

Route::get('aaa', function (){
return Hash::make('123');
});
