

<?php $__env->startSection('title', 'Editar Usuário'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Editar Usuário</h4>

    <div class="card p-4">
        <form method="POST" action="<?php echo e(route('usuarios.update', $usuario)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-3">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo e(old('nome', $usuario->nome)); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo e(old('email', $usuario->email)); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Senha (Deixe em branco para manter)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <?php if(Auth::user()->tipo == 'super_admin'): ?>
                <div class="mb-3">
                    <label>Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="usuario" <?php echo e($usuario->tipo == 'usuario' ? 'selected' : ''); ?>>Usuário</option>
                        <option value="consultor" <?php echo e($usuario->tipo == 'consultor' ? 'selected' : ''); ?>>Consultor</option>
                        <option value="super_admin" <?php echo e($usuario->tipo == 'super_admin' ? 'selected' : ''); ?>>Super Admin</option>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="tipo" value="consultor">
            <?php endif; ?>

            <?php if(Auth::user()->tipo == 'super_admin'): ?>
                <div class="mb-3">
                    <label>Consultor (opcional)</label>
                    <select name="consultor_id" class="form-select">
                        <option value="">Nenhum</option>
                        <?php $__currentLoopData = $consultores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consultor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($consultor->id); ?>" <?php echo e($usuario->consultor_id == $consultor->id ? 'selected' : ''); ?>><?php echo e($consultor->nome); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="consultor_id" value="<?php echo e(Auth::user()->id); ?>">
            <?php endif; ?>

            <div class="d-grid">
                <button class="btn btn-meli">Salvar</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/meli.webtechdesk.com.br/resources/views/usuarios/edit.blade.php ENDPATH**/ ?>