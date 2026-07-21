<?php $__env->startSection('title', 'Mes patients'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex justify-between items-start flex-wrap gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">👥 Mes patients</h1>
        <p class="text-gray-500 text-sm mt-1"><?php echo e($patients->count()); ?> patient(s) suivi(s)</p>
    </div>
    <form method="POST" action="<?php echo e(route('doctor.add-patient')); ?>" class="flex gap-2">
        <?php echo csrf_field(); ?>
        <input type="text" name="record_code" class="input !w-56" placeholder="Code dossier (SP-MA-XXXXXXXX)" required>
        <button type="submit" class="btn-primary whitespace-nowrap">➕ Ajouter</button>
    </form>
</div>

<?php $__errorArgs = ['record_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm"><?php echo e($message); ?></div>
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

<?php if($patients->count()): ?>
    <div class="grid md:grid-cols-2 gap-4">
        <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-hover">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shrink-0">
                            <?php echo e(strtoupper(substr($p->name, 0, 1))); ?>

                        </div>
                        <div>
                            <div class="font-bold text-gray-900"><?php echo e($p->name); ?></div>
                            <div class="text-sm text-gray-500">
                                <?php echo e($p->date_of_birth ? $p->date_of_birth->age . ' ans' : '—'); ?>

                                • <?php echo e($p->city ?? '—'); ?>

                            </div>
                        </div>
                    </div>
                    <?php if($p->medicalRecord): ?>
                        <span class="badge bg-indigo-50 text-indigo-700 font-mono">
                            <?php echo e($p->medicalRecord->record_code); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <?php if($p->medicalRecord && $p->medicalRecord->chronic_conditions): ?>
                    <div class="text-sm mb-3 bg-red-50 text-red-700 px-3 py-2 rounded-lg">
                        ⚠️ <?php echo e(\Illuminate\Support\Str::limit($p->medicalRecord->chronic_conditions, 80)); ?>

                    </div>
                <?php endif; ?>

                <a href="<?php echo e(route('doctor.show-patient', $p)); ?>" class="btn-primary w-full text-sm">
                    📋 Voir le dossier complet
                </a>
                <form method="POST" action="<?php echo e(route('doctor.remove-patient', $p)); ?>"
                      onsubmit="return confirm('Retirer <?php echo e($p->name); ?> de votre liste ?');" class="mt-2">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-danger w-full text-xs">🗑️ Retirer de ma liste</button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="card text-center py-14">
        <div class="text-4xl mb-2">👥</div>
        <p class="text-gray-500">Vous n'avez pas encore de patients.</p>
        <p class="text-gray-400 text-sm mt-1">Ajoutez-en un avec son code dossier (visible sur son tableau de bord patient).</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/doctor/patients.blade.php ENDPATH**/ ?>