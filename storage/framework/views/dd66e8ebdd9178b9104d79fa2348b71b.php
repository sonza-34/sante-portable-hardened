

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">⏰ Rappels</h1>
    <button onclick="document.getElementById('addRem').classList.toggle('hidden')"
            class="btn-primary">+ Nouveau rappel</button>
</div>

<form id="addRem" method="POST" action="<?php echo e(route('reminders.store')); ?>"
      class="card mb-6 hidden">
    <?php echo csrf_field(); ?>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="label">Type *</label>
            <select name="type" class="input" required>
                <option value="medication">💊 Médicament</option>
                <option value="appointment">🏥 Rendez-vous</option>
                <option value="vaccination">💉 Vaccination</option>
                <option value="checkup">🩺 Bilan / Contrôle</option>
            </select>
        </div>
        <div>
            <label class="label">Date et heure *</label>
            <input type="datetime-local" name="due_at" class="input" required>
        </div>
    </div>
    <div class="mt-4">
        <label class="label">Titre *</label>
        <input type="text" name="title" class="input" required
               placeholder="Ex: Prendre Metformine 500mg">
    </div>
    <div class="mt-4">
        <label class="label">Description</label>
        <textarea name="description" class="input" rows="2"></textarea>
    </div>
    <button type="submit" class="btn-primary mt-4">💾 Créer le rappel</button>
</form>

<?php if($reminders->count()): ?>
    <div class="space-y-3">
        <?php $__currentLoopData = $reminders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card flex justify-between items-center <?php echo e($r->is_completed ? 'opacity-50' : ''); ?>">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <?php if($r->is_completed): ?>
                            <span>✅</span>
                        <?php else: ?>
                            <span>🔔</span>
                        <?php endif; ?>
                        <div class="font-bold <?php echo e($r->is_completed ? 'line-through' : ''); ?>">
                            <?php echo e($r->title); ?>

                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        <?php echo e($r->due_at->format('d/m/Y à H:i')); ?>

                        • <?php echo e(ucfirst($r->type)); ?>

                    </div>
                    <?php if($r->description): ?>
                        <div class="text-sm text-gray-500 mt-1"><?php echo e($r->description); ?></div>
                    <?php endif; ?>
                </div>
                <?php if(!$r->is_completed): ?>
                    <form method="POST" action="<?php echo e(route('reminders.complete', $r)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button class="btn-secondary text-sm">✓ Fait</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="card text-center py-10">
        <div class="text-4xl mb-2">⏰</div>
        <p class="text-gray-600">Aucun rappel. Créez-en un pour ne rien oublier !</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/reminders/index.blade.php ENDPATH**/ ?>