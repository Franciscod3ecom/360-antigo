<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeliService
{
    protected $accessToken;
    protected $userId;

    // CEPs fixos (como no seu script)
    protected $ceps = [
        '70002900' => 'Brasília, DF',
        '01001000' => 'São Paulo, SP',
        '40020210' => 'Salvador, BA',
        '69005070' => 'Manaus, AM',
        '90010190' => 'Porto Alegre, RS',
    ];

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->userId = $this->getUserId();
    }

    /**
     * 🔑 Obter o User ID do Mercado Livre
     */
    public function getUserId()
    {
        $response = Http::withToken($this->accessToken)
            ->get('https://api.mercadolibre.com/users/me');

        if ($response->ok()) {
            return $response->json()['id'];
        }

        throw new \Exception('Erro ao obter User ID: ' . $response->body());
    }

    /**
     * 🔍 Buscar todos os anúncios ativos
     */
    public function buscarAnunciosAtivos()
    {
        $anuncios = [];
        $offset = 0;
        $limit = 50;

        do {
            $response = Http::withToken($this->accessToken)
                ->get("https://api.mercadolibre.com/users/{$this->userId}/items/search", [
                    'status' => 'active',
                    'offset' => $offset,
                    'limit' => $limit,
                ]);

            if (!$response->ok()) {
                throw new \Exception('Erro ao buscar anúncios: ' . $response->body());
            }

            $data = $response->json();
            $anuncios = array_merge($anuncios, $data['results']);
            $offset += $limit;
        } while ($offset < $data['paging']['total']);

        return $anuncios;
    }

    /**
     * 🔍 Buscar detalhes completos dos anúncios
     */
    public function buscarDetalhesAnuncios($itemIds)
    {
        $ids = implode(',', $itemIds);

        $response = Http::withToken($this->accessToken)
            ->get("https://api.mercadolibre.com/items", [
                'ids' => $ids
            ]);

        if (!$response->ok()) {
            throw new \Exception('Erro ao buscar detalhes dos anúncios: ' . $response->body());
        }

        return collect($response->json())->pluck('body')->filter();
    }

    /**
     * 🔍 Buscar detalhes da categoria (dimensões, restrições, logística)
     */
    public function buscarCategoriaDetalhes($categoryId)
    {
        $response = Http::withToken($this->accessToken)
            ->get("https://api.mercadolibre.com/categories/{$categoryId}/shipping_preferences");

        if (!$response->ok()) {
            throw new \Exception('Erro ao buscar detalhes da categoria: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * 🔍 Buscar nome da categoria
     */
    public function buscarCategoriaNome($categoryId)
    {
        $response = Http::withToken($this->accessToken)
            ->get("https://api.mercadolibre.com/categories/{$categoryId}");

        if ($response->ok()) {
            return $response->json()['name'] ?? '';
        }

        return '';
    }

    /**
     * 🚚 Buscar custo de envio para um CEP específico
     */
    public function buscarFretePorCep($itemId, $cep)
    {
        $response = Http::withToken($this->accessToken)
            ->get("https://api.mercadolibre.com/items/{$itemId}/shipping_options", [
                'zip_code' => $cep
            ]);

        if ($response->ok()) {
            $data = $response->json();
            return $data['options'][0]['list_cost'] ?? null;
        }

        return null;
    }

    /**
     * 🔍 Buscar envio nacional (peso e custo médio nacional)
     */
    public function buscarEnvioNacional($itemId)
    {
        $response = Http::withToken($this->accessToken)
            ->get("https://api.mercadolibre.com/users/{$this->userId}/shipping_options/free", [
                'item_id' => $itemId
            ]);

        if ($response->ok()) {
            $data = $response->json();
            return [
                'custo' => $data['coverage']['all_country']['list_cost'] ?? null,
                'peso'  => $data['coverage']['all_country']['billable_weight'] ?? null,
            ];
        }

        return null;
    }

    /**
     * ⚖️ Comparar Peso Faturável e Peso Ideal
     */
    public function calcularStatusPeso($pesoIdeal, $pesoFaturavel)
    {
        if (!$pesoIdeal || !$pesoFaturavel) {
            return 'N/A';
        }

        if ($pesoFaturavel == $pesoIdeal) {
            return '🟡 Peso aceitável';
        }

        if ($pesoFaturavel > $pesoIdeal) {
            return '🔴 Peso alto e errado';
        }

        if ($pesoFaturavel < $pesoIdeal) {
            return '🟢 Peso baixo e bom';
        }

        return 'N/A';
    }

    /**
     *  Função mestre — Buscar todos os dados completos de todos os anúncios
     */
    public function salvarTodosAnunciosCompletos(int $usuarioId): void
    {
        try {
            Cache::put("sync_status_{$usuarioId}", 'rodando', now()->addMinutes(10));

            $anunciosIds = $this->buscarAnunciosAtivos();
            $lotes = array_chunk($anunciosIds, 20);
            $total = count($anunciosIds);
            $contador = 0;

            foreach ($lotes as $lote) {
                
                $detalhes = $this->buscarDetalhesAnuncios($lote);
                
                foreach ($detalhes as $item) {

                    $contador++;
                    
                    if ($contador % 5 === 0 || $contador === $total) {
                        Cache::put("sync_progress_{$usuarioId}", [
                            'atual' => $contador,
                            'total' => $total,
                        ], now()->addMinutes(10));
                    }

                    try {
                        $categoriaDetalhes = $this->buscarCategoriaDetalhes($item['category_id']);
                        $categoriaNome = $this->buscarCategoriaNome($item['category_id']);
                        $envioNacional = $this->buscarEnvioNacional($item['id']);
                        $pesoFaturavel = $envioNacional['peso'] ?? null;
                        $custoEnvio = $envioNacional['custo'] ?? null;

                        $fretesPorRegiao = [];
                        foreach ($this->ceps as $cep => $cidade) {
                            $fretesPorRegiao[$cidade] = $this->buscarFretePorCep($item['id'], $cep);
                        }

                        $pesoIdeal = $categoriaDetalhes['dimensions']['weight'] ?? null;
                        $statusPeso = $this->calcularStatusPeso($pesoIdeal, $pesoFaturavel);
                        info($item);
                        
                        $dados = [
                            'usuario_id' => $usuarioId,
                            'item_id' => $item['id'],
                            'titulo' => $item['title'],
                            'imagem' => $item['thumbnail'],
                            'sku' => $item['seller_custom_field'] ?? null,
                            'estoque' => $item['available_quantity'] ?? 0,
                            'preco' => $item['price'] ?? 0,
                            'health' => $item['health'] ?? null,
                            'tags' => isset($item['tags']) ? implode(',', $item['tags']) : null,
                            'categoria' => $categoriaNome,
                            'dimensoes' => json_encode($categoriaDetalhes['dimensions'] ?? []),
                            'logisticas' => json_encode($categoriaDetalhes['logistics'] ?? []),
                            'restricoes_me2' => json_encode($categoriaDetalhes['me2_restrictions'] ?? []),
                            'restrito' => $categoriaDetalhes['restricted'] ? 'Sim' : 'Não',
                            'ultima_atualizacao_categoria' => $categoriaDetalhes['last_modified'] ?? null,
                            'preco_medio_categoria' => null,
                            'tipo_envio' => $item['shipping']['mode'] ?? null,
                            'frete_gratis_acima_79' => $item['shipping']['free_shipping'] ?? null,
                            'custo_envio' => $custoEnvio,
                            'peso_faturavel' => $pesoFaturavel,
                            'status_peso' => $statusPeso,
                            'frete_brasilia' => $fretesPorRegiao['Brasília, DF'] ?? null,
                            'frete_sao_paulo' => $fretesPorRegiao['São Paulo, SP'] ?? null,
                            'frete_salvador' => $fretesPorRegiao['Salvador, BA'] ?? null,
                            'frete_manaus' => $fretesPorRegiao['Manaus, AM'] ?? null,
                            'frete_porto_alegre' => $fretesPorRegiao['Porto Alegre, RS'] ?? null,
                            'status' => $item['status'] ?? 'ativo',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        DB::table('anuncios')->updateOrInsert(
                            ['item_id' => $dados['item_id']],
                            $dados
                        );
                    } catch (\Throwable $e) {
                        Log::error("Erro no item {$item['id']}: " . $e->getMessage());
                        continue;
                    }
                }

                if ($contador % 15 === 0) {
                    sleep(60);
                }
            }

            Cache::put("sync_status_{$usuarioId}", 'concluido', now()->addMinutes(10));

        } catch (\Throwable $e) {
            Cache::put("sync_status_{$usuarioId}", 'falhou', now()->addMinutes(10));
            Log::error("Erro geral ao salvar anúncios: " . $e->getMessage());
        }
    }

}
