<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios'; // Nome da tabela

    protected $fillable = [
        'nome',
        'email',
        'password',
        'tipo',
        'consultor_id',
        'token_meli',
        'refresh_token_meli',
        'meli_user_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setSenhaAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class, 'usuario_id');
    }

    public function consultor()
    {
        return $this->belongsTo(User::class, 'consultor_id');
    }

    public function usuariosVinculados()
    {
        return $this->hasMany(User::class, 'consultor_id');
    }
    public function getAuthPassword()
{
    return $this->password;
}

}
