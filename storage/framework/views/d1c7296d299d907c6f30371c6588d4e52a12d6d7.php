

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="container">
    <div class="card shadow border-0 rounded-4 mb-5">
        <div class="card-body">
            <h1 class="card-title mb-4">📦 Meus Anúncios</h1>

            <form method="POST" action="<?php echo e(route('anuncios.sync')); ?>" class="mb-4">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-primary rounded-3" style="background-color: #300570; border-color: #300570;">
                    🔄 Sincronizar Anúncios
                </button>
            </form>

            <?php if($anuncios->count() > 0): ?>
                <div class="table-responsive">
                    <table id="tabela-anuncios" class="table table-bordered table-hover align-middle text-nowrap" style="font-size: 0.875rem;">
                        <thead class="table-light" style="background-color: #300570; color: #ffe600;">
                            <tr>
                                <th>Imagem</th>
                                <th>ID do Item</th>
                                <th>Título</th>
                                <th>SKU</th>
                                <th>Estoque</th>
                                <th>Preço</th>
                                <th>Health</th>
                                <th>Tags</th>
                                <th>Categoria</th>
                                <th>Dimensões</th>
                                <th>Logísticas</th>
                                <th>Restrições ME2</th>
                                <th>Restrito</th>
                                <th>Última Atualização Categoria</th>
                                <th>Preço Médio Categoria</th>
                                <th>Tipo de Envio</th>
                                <th>Frete Grátis > R$79</th>
                                <th>Custo Envio</th>
                                <th>Peso Faturável</th>
                                <th>Status Peso</th>
                                <th>Frete Brasília</th>
                                <th>Frete SP</th>
                                <th>Frete Salvador</th>
                                <th>Frete Manaus</th>
                                <th>Frete POA</th>
                                <th>Status</th>
                                <th>Data Atualização</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $anuncios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anuncio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($anuncio->imagem): ?>
                                            <img src="<?php echo e($anuncio->imagem); ?>" alt="Imagem" style="height: 60px;">
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($anuncio->item_id); ?></td>
                                    <td><?php echo e($anuncio->titulo); ?></td>
                                    <td><?php echo e($anuncio->sku); ?></td>
                                    <td><?php echo e($anuncio->estoque); ?></td>
                                    <td>R$ <?php echo e(number_format($anuncio->preco, 2, ',', '.')); ?></td>
                                    <td><?php echo e($anuncio->health); ?></td>
                                    <td style="white-space: pre-wrap;"><?php echo e($anuncio->tags); ?></td>
                                    <td><?php echo e($anuncio->categoria); ?></td>
                                    <td>
                                        <?php
                                            $dim = json_decode($anuncio->dimensoes, true);
                                        ?>

                                        <?php if($dim): ?>
                                            Altura: <b> <?php echo e($dim['height'] ?? '?'); ?> cm </b> Largura: <b> </strong><?php echo e($dim['width'] ?? '?'); ?> cm  </b> Comprimento: <b><?php echo e($dim['length'] ?? '?'); ?> cm  </b> Peso: <b><?php echo e($dim['weight'] ?? '?'); ?> g </b>
                                        <?php else: ?>
                                            <span class="text-muted">Não informado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $logs = json_decode($anuncio->logisticas, true);
                                        ?>

                                        <?php if($logs && is_array($logs)): ?>
                                            <?php echo e(collect($logs)->map(function($log) {
                                                return strtoupper($log['mode']) . ': ' . implode(', ', $log['types'] ?? []);
                                            })->implode(' | ')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Não informado</span>
                                        <?php endif; ?>
                                    </td>

                                    <td style="white-space: pre-wrap;"><?php echo e($anuncio->restricoes_me2); ?></td>
                                    <td><?php echo e($anuncio->restrito); ?></td>
                                    <td><?php echo e($anuncio->ultima_atualizacao_categoria); ?></td>
                                    <td><?php echo e($anuncio->preco_medio_categoria); ?></td>
                                    <td><?php echo e($anuncio->tipo_envio); ?></td>
                                    <td><?php echo e($anuncio->frete_gratis_acima_79); ?></td>
                                    <td><?php echo e($anuncio->custo_envio); ?></td>
                                    <td><?php echo e($anuncio->peso_faturavel); ?></td>
                                    <td><?php echo e($anuncio->status_peso); ?></td>
                                    <td><?php echo e($anuncio->frete_brasilia); ?></td>
                                    <td><?php echo e($anuncio->frete_sao_paulo); ?></td>
                                    <td><?php echo e($anuncio->frete_salvador); ?></td>
                                    <td><?php echo e($anuncio->frete_manaus); ?></td>
                                    <td><?php echo e($anuncio->frete_porto_alegre); ?></td>
                                    <td><?php echo e($anuncio->status); ?></td>
                                    <td><?php echo e((new DateTime($anuncio->updated_at))->format('d/m/Y H:i:s')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Nenhum anúncio encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function () {
        $('#tabela-anuncios').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: '📥 Exportar CSV',
                    className: 'btn btn-success mb-3'
                }
            ],
            responsive: true,
            ordering: true,
            pageLength: 25
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/meli.webtechdesk.com.br/resources/views/anuncios/index.blade.php ENDPATH**/ ?>