

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">📋 Mon dossier médical</h1>
        <p class="text-gray-600 mt-1">
            Code unique :
            <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded">
                <?php echo e($record->record_code); ?>

            </span>
        </p>
    </div>
</div>

<form method="POST" action="<?php echo e(route('medical-record.update')); ?>" class="card">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <h2 class="text-lg font-bold mb-3 border-b pb-2">Informations physiques</h2>
    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="label">Taille (cm)</label>
            <input type="number" step="0.1" name="height_cm" class="input" value="<?php echo e($record->height_cm); ?>">
        </div>
        <div>
            <label class="label">Poids (kg)</label>
            <input type="number" step="0.1" name="weight_kg" class="input" value="<?php echo e($record->weight_kg); ?>">
        </div>
        <div>
            <label class="label">Groupe sanguin</label>
            <input type="text" name="blood_type_patient" class="input"
                   value="<?php echo e($user->blood_type); ?>" disabled>
            <input type="hidden" name="blood_type" value="<?php echo e($user->blood_type); ?>">
            <p class="text-xs text-gray-500 mt-1">Modifiable dans votre profil</p>
        </div>
    </div>

    <h2 class="text-lg font-bold mb-3 border-b pb-2">Antécédents médicaux</h2>
    <div class="space-y-4 mb-6">
        <div>
            <label class="label">Maladies chroniques</label>
            <textarea name="chronic_conditions" class="input" rows="2"
                placeholder="Diabète, hypertension, asthme..."><?php echo e($record->chronic_conditions); ?></textarea>
        </div>
        <div>
            <label class="label">Allergies</label>
            <textarea name="allergies" class="input" rows="2"
                placeholder="Pénicilline, pollen, fruits de mer..."><?php echo e($record->allergies); ?></textarea>
        </div>
        <div>
            <label class="label">Traitements en cours</label>
            <textarea name="current_treatments" class="input" rows="2"
                placeholder="Metformine 500mg 2x/jour..."><?php echo e($record->current_treatments); ?></textarea>
        </div>
        <div>
            <label class="label">Antécédents familiaux</label>
            <textarea name="family_history" class="input" rows="2"><?php echo e($record->family_history); ?></textarea>
        </div>
        <div>
            <label class="label">Antécédents chirurgicaux</label>
            <textarea name="surgical_history" class="input" rows="2"><?php echo e($record->surgical_history); ?></textarea>
        </div>
    </div>

    <h2 class="text-lg font-bold mb-3 border-b pb-2">Contact d'urgence</h2>
    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="label">Nom</label>
            <input type="text" name="emergency_contact_name" class="input"
                   value="<?php echo e($record->emergency_contact_name); ?>">
        </div>
        <div>
            <label class="label">Téléphone</label>
            <input type="text" name="emergency_contact_phone" class="input"
                   value="<?php echo e($record->emergency_contact_phone); ?>">
        </div>
    </div>

    <h2 class="text-lg font-bold mb-3 border-b pb-2">Assurance</h2>
    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="label">Assureur</label>
            <input type="text" name="insurance_provider" class="input"
                   value="<?php echo e($record->insurance_provider); ?>" placeholder="CNOPS, CNSS, AMO...">
        </div>
        <div>
            <label class="label">Numéro d'assuré</label>
            <input type="text" name="insurance_number" class="input"
                   value="<?php echo e($record->insurance_number); ?>">
        </div>
    </div>

    <button type="submit" class="btn-primary">💾 Enregistrer</button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/medical-record/show.blade.php ENDPATH**/ ?>