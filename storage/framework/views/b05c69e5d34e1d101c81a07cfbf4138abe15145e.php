

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row g-4">

        
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Status Mercado Livre</h5>
                    <?php if(Auth::user()->token_meli): ?>
                        <p class="text-success fw-bold">🟢 Conectado</p>
                    <?php else: ?>
                        <p class="text-danger fw-bold">🔴 No Conectado</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Anúncios</h5>
                    <p class="fw-bold display-6"><?php echo e($totalAnuncios); ?></p>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Ações</h5>
                    <div class="d-grid gap-2">
                        <?php if(!Auth::user()->token_meli): ?>
                            <!-- Botão para conectar ao Mercado Livre -->
                            <a href="<?php echo e(route('meli.login')); ?>" class="btn btn-warning rounded-3">🔗 Conectar Mercado Livre</a>
                        <?php else: ?>
                            <!-- Formulário para desvincular a conta do Mercado Livre -->
                            <form method="POST" action="<?php echo e(route('meli.desvincular')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-danger rounded-3">🔴 Desvincular Mercado Livre</button>
                            </form>

                            <!-- Botão para sincronizar os anúncios -->
                            <form method="POST" action="<?php echo e(route('anuncios.sync')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" id="sync-button" class="btn btn-primary rounded-3" style="background-color: #300570; border-color: #300570;">🔄 Sincronizar Anúncios</button>
                            </form>
                            <div class="progress mt-3 d-none" id="sync-progress-wrapper" style="height: 25px;">
                                <div id="sync-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated fw-bold"
                                    role="progressbar" style="width: 0%">0%</div>
                            </div>

                        <?php endif; ?>

                        <!-- Botão para ver os anúncios -->
                        <a href="<?php echo e(route('anuncios.index')); ?>" class="btn btn-outline-dark rounded-3">📦 Ver Anúncios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card mt-5 shadow border-0 rounded-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Últimos Anúncios</h5>
            <?php if($anuncios->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Imagem</th>
                                <th>Título</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Categoria</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $anuncios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anuncio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><img src="<?php echo e($anuncio->imagem); ?>" alt="Imagem" style="height: 60px;"></td>
                                    <td><?php echo e($anuncio->titulo); ?></td>
                                    <td>R$ <?php echo e(number_format($anuncio->preco, 2, ',', '.')); ?></td>
                                    <td><?php echo e($anuncio->estoque); ?></td>
                                    <td><?php echo e($anuncio->categoria); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Nenhum anúncio encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/melifretes.d3ecom.com.br/public_html/resources/views/dashboard/index.blade.php ENDPATH**/ ?>