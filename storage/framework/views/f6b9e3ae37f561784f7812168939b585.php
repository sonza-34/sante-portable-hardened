<?php $__env->startSection('title', 'Tableau de bord'); ?>

<?php $__env->startSection('content'); ?>

<div class="mb-8 flex justify-between items-start flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">
            Bonjour <?php echo e(explode(' ', $user->name)[0]); ?> 👋
        </h1>
        <p class="text-gray-500 mt-1">
            <?php if($user->medicalRecord): ?>
                Code dossier :
                <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100 ml-1">
                    <?php echo e($user->medicalRecord->record_code); ?>

                </span>
            <?php elseif($isDoctor): ?>
                <span class="text-violet-700 font-medium">Espace médecin — Dr. <?php echo e($user->name); ?></span>
            <?php else: ?>
                Bienvenue sur Santé Portable
            <?php endif; ?>
        </p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl px-4 py-2 text-right shadow-sm">
        <div class="text-sm font-medium text-gray-700"><?php echo e(now()->format('l d F Y')); ?></div>
        <div class="text-xs text-gray-400"><?php echo e(now()->format('H:i')); ?></div>
    </div>
</div>


<?php if($isPatient): ?>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="vital-card">
        <div class="vital-icon bg-indigo-50 text-indigo-600">💊</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($activePrescriptions); ?></div>
        <div class="text-sm text-gray-500 mt-1">Ordonnances actives</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-emerald-50 text-emerald-600">🏥</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($totalConsultations); ?></div>
        <div class="text-sm text-gray-500 mt-1">Consultations</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-violet-50 text-violet-600">💉</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($vaccinationsCount); ?></div>
        <div class="text-sm text-gray-500 mt-1">Vaccins</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-orange-50 text-orange-600">⏰</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($upcomingRemindersCount); ?></div>
        <div class="text-sm text-gray-500 mt-1">Rappels à venir</div>
    </div>
</div>
<?php elseif($isDoctor): ?>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="vital-card">
        <div class="vital-icon bg-indigo-50 text-indigo-600">👥</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($patientsCount); ?></div>
        <div class="text-sm text-gray-500 mt-1">Patients suivis</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-emerald-50 text-emerald-600">🩺</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($myConsultations); ?></div>
        <div class="text-sm text-gray-500 mt-1">Consultations données</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-violet-50 text-violet-600">💊</div>
        <div class="text-3xl font-extrabold text-gray-900"><?php echo e($activePrescriptions); ?></div>
        <div class="text-sm text-gray-500 mt-1">Ordonnances en cours</div>
    </div>
</div>
<?php endif; ?>

<div class="grid lg:grid-cols-2 gap-6">
    
    <div class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="text-xl">🚀</span> Accès rapide
        </h2>
        <div class="space-y-2">
            <?php if($isPatient): ?>
                <a href="<?php echo e(route('medical-record')); ?>" class="quick-link group">
                    <span class="text-xl">📋</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Mon dossier médical</div>
                        <div class="text-xs text-gray-400">Antécédents, allergies, traitements</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
                <a href="<?php echo e(route('prescriptions')); ?>" class="quick-link group">
                    <span class="text-xl">💊</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Mes ordonnances</div>
                        <div class="text-xs text-gray-400"><?php echo e($activePrescriptions); ?> actives</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
                <a href="<?php echo e(route('vaccinations')); ?>" class="quick-link group">
                    <span class="text-xl">💉</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Carnet de vaccination</div>
                        <div class="text-xs text-gray-400"><?php echo e($vaccinationsCount); ?> vaccins</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
                <a href="<?php echo e(route('doctors.index')); ?>" class="quick-link group">
                    <span class="text-xl">🩺</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Trouver un médecin</div>
                        <div class="text-xs text-gray-400">Choisissez qui accède à votre dossier</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('reminders')); ?>" class="quick-link group">
                <span class="text-xl">⏰</span>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">Rappels</div>
                    <div class="text-xs text-gray-400"><?php echo e($upcomingRemindersCount); ?> à venir</div>
                </div>
                <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
            </a>
            <a href="<?php echo e(route('share.index')); ?>" class="quick-link group">
                <span class="text-xl">🔗</span>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">Partager mon dossier</div>
                    <div class="text-xs text-gray-400">QR code temporaire</div>
                </div>
                <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
            </a>
            <a href="<?php echo e(route('chatbot')); ?>" class="quick-link group">
                <span class="text-xl">🤖</span>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">Assistant IA</div>
                    <div class="text-xs text-gray-400">Posez vos questions santé</div>
                </div>
                <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
            </a>
            <?php if($isDoctor): ?>
                <a href="<?php echo e(route('doctor.patients')); ?>" class="quick-link group bg-gradient-to-r from-indigo-50 to-violet-50 border-indigo-200">
                    <span class="text-xl">👥</span>
                    <div class="flex-1">
                        <div class="font-bold text-indigo-800 text-sm">Mes patients</div>
                        <div class="text-xs text-indigo-500"><?php echo e($patientsCount); ?> patient(s) suivi(s)</div>
                    </div>
                    <span class="text-indigo-500 group-hover:translate-x-1 transition-all">→</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="text-xl">🔔</span> Prochains rappels
        </h2>
        <?php if($upcomingRemindersCount): ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $upcomingReminders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <div class="text-xl">
                            <?php switch($r->type):
                                case ('medication'): ?> 💊 <?php break; ?>
                                <?php case ('appointment'): ?> 🏥 <?php break; ?>
                                <?php case ('vaccination'): ?> 💉 <?php break; ?>
                                <?php case ('checkup'): ?> 🩺 <?php break; ?>
                            <?php endswitch; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-800 truncate"><?php echo e($r->title); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($r->due_at->format('d/m/Y à H:i')); ?></div>
                        </div>
                        <span class="badge bg-amber-100 text-amber-700 whitespace-nowrap">
                            <?php echo e($r->due_at->diffForHumans(null, true)); ?>

                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10 text-gray-400">
                <div class="text-4xl mb-2">🎉</div>
                <p class="font-medium text-gray-500">Aucun rappel en attente</p>
                <a href="<?php echo e(route('reminders')); ?>" class="btn-primary inline-block mt-4 text-sm">Créer un rappel</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/dashboard.blade.php ENDPATH**/ ?>