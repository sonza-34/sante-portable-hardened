

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">💉 Carnet de vaccination</h1>
    <button onclick="document.getElementById('addVax').classList.toggle('hidden')"
            class="btn-primary">+ Ajouter</button>
</div>

<form id="addVax" method="POST" action="<?php echo e(route('vaccinations.store')); ?>"
      class="card mb-6 hidden">
    <?php echo csrf_field(); ?>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="label">Nom du vaccin *</label>
            <input type="text" name="vaccine_name" class="input" required
                   placeholder="DTC, ROR, BCG...">
        </div>
        <div>
            <label class="label">Date d'administration *</label>
            <input type="date" name="administration_date" class="input" required>
        </div>
        <div>
            <label class="label">Prochaine dose</label>
            <input type="date" name="next_dose_date" class="input">
        </div>
        <div>
            <label class="label">N° de lot</label>
            <input type="text" name="batch_number" class="input">
        </div>
        <div>
            <label class="label">Administré par</label>
            <input type="text" name="administered_by" class="input">
        </div>
        <div>
            <label class="label">Lieu</label>
            <input type="text" name="facility_name" class="input">
        </div>
    </div>
    <button type="submit" class="btn-primary mt-4">💾 Enregistrer</button>
</form>

<?php if($vaccinations->count()): ?>
    <div class="space-y-3">
        <?php $__currentLoopData = $vaccinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card flex justify-between items-center">
                <div>
                    <div class="font-bold text-lg"><?php echo e($v->vaccine_name); ?></div>
                    <div class="text-sm text-gray-600">
                        Administré le <?php echo e($v->administration_date->format('d/m/Y')); ?>

                        <?php if($v->facility_name): ?> — <?php echo e($v->facility_name); ?> <?php endif; ?>
                    </div>
                </div>
                <?php if($v->next_dose_date): ?>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Prochaine dose</div>
                        <div class="font-bold text-blue-700">
                            <?php echo e($v->next_dose_date->format('d/m/Y')); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="card text-center py-10">
        <div class="text-4xl mb-2">💉</div>
        <p class="text-gray-600">Aucun vaccin enregistré</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/vaccinations/index.blade.php ENDPATH**/ ?>