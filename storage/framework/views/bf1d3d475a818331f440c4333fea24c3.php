<?php $__env->startSection('title', 'Trouver un médecin'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">🩺 Trouver un médecin</h1>
    <p class="text-gray-500 text-sm mt-1">Choisissez un ou plusieurs médecins qui pourront accéder à votre dossier.</p>
</div>

<?php if($doctors->count()): ?>
    <div class="grid md:grid-cols-2 gap-4">
        <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $isFollowed = in_array($doc->id, $myDoctorIds); ?>
            <div class="card <?php echo e($isFollowed ? 'border-indigo-200 bg-indigo-50/30' : ''); ?>">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shrink-0">
                        <?php echo e(strtoupper(substr($doc->name, 0, 1))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-900">Dr. <?php echo e($doc->name); ?></div>
                        <div class="text-sm text-gray-500"><?php echo e($doc->city ?? 'Ville non renseignée'); ?></div>
                    </div>
                    <?php if($isFollowed): ?>
                        <span class="badge bg-indigo-100 text-indigo-700">✓ Ajouté</span>
                    <?php endif; ?>
                </div>

                <?php if($isFollowed): ?>
                    <form method="POST" action="<?php echo e(route('doctors.unfollow', $doc)); ?>"
                          onsubmit="return confirm('Retirer Dr. <?php echo e($doc->name); ?> de vos médecins ?');">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-secondary w-full text-sm">Retirer</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('doctors.follow', $doc)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-primary w-full text-sm">➕ Choisir ce médecin</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="card text-center py-14">
        <div class="text-4xl mb-2">🩺</div>
        <p class="text-gray-500">Aucun médecin inscrit sur la plateforme pour le moment.</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/doctors/index.blade.php ENDPATH**/ ?>