<?php $__env->startSection('title', 'Ordonnances'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">💊 Mes ordonnances</h1>
    <p class="text-gray-500 text-sm mt-1"><?php echo e($prescriptions->count()); ?> ordonnance(s)</p>
</div>

<?php if($prescriptions->count()): ?>
    <div class="space-y-4">
        <?php $__currentLoopData = $prescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card overflow-hidden !p-0">
                
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                    <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <span class="text-indigo-500 text-base">℞</span> Ordonnance
                    </div>
                    <span class="badge <?php echo e($p->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'); ?>">
                        <?php echo e($p->is_active ? '● Active' : 'Terminée'); ?>

                    </span>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900"><?php echo e($p->medication_name); ?></h3>

                    
                    <div class="grid grid-cols-3 gap-3 mt-4">
                        <div class="bg-indigo-50/70 rounded-xl px-3 py-2.5 text-center">
                            <div class="text-[11px] font-medium text-indigo-400 uppercase tracking-wide">Dosage</div>
                            <div class="font-bold text-indigo-800 text-sm mt-0.5"><?php echo e($p->dosage); ?></div>
                        </div>
                        <div class="bg-violet-50/70 rounded-xl px-3 py-2.5 text-center">
                            <div class="text-[11px] font-medium text-violet-400 uppercase tracking-wide">Fréquence</div>
                            <div class="font-bold text-violet-800 text-sm mt-0.5"><?php echo e($p->frequency); ?></div>
                        </div>
                        <div class="bg-fuchsia-50/70 rounded-xl px-3 py-2.5 text-center">
                            <div class="text-[11px] font-medium text-fuchsia-400 uppercase tracking-wide">Durée</div>
                            <div class="font-bold text-fuchsia-800 text-sm mt-0.5"><?php echo e($p->duration); ?></div>
                        </div>
                    </div>

                    <?php if($p->instructions): ?>
                        <div class="mt-4 flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-xl px-3.5 py-2.5 text-sm text-amber-800">
                            <span>💡</span> <?php echo e($p->instructions); ?>

                        </div>
                    <?php endif; ?>

                    
                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center text-sm flex-wrap gap-2">
                        <div class="text-gray-500">
                            Dr. <span class="font-semibold text-gray-800"><?php echo e($p->doctor->name ?? '—'); ?></span>
                        </div>
                        <div class="text-gray-400 text-xs">
                            Du <?php echo e($p->start_date->format('d/m/Y')); ?>

                            <?php if($p->end_date): ?> au <?php echo e($p->end_date->format('d/m/Y')); ?> <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="card text-center py-14">
        <div class="text-4xl mb-2">💊</div>
        <p class="text-gray-500">Aucune ordonnance pour le moment</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/prescriptions/index.blade.php ENDPATH**/ ?>