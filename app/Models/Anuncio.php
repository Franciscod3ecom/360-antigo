<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{
    use HasFactory;

    protected $table = 'anuncios';

    protected $fillable = [
        'usuario_id',
        'item_id',
        'titulo',
        'imagem',
        'sku',
        'estoque',
        'preco',
        'health',
        'tags',
        'categoria',
        'dimensoes',
        'logisticas',
        'restricoes_me2',
        'restrito',
        'ultima_atualizacao_categoria',
        'preco_medio_categoria',
        'tipo_envio',
        'frete_gratis_acima_79',
        'custo_envio',
        'peso_faturavel',
        'status_peso',
        'frete_brasilia',
        'frete_sao_paulo',
        'frete_salvador',
        'frete_manaus',
        'frete_porto_alegre',
        'status',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
