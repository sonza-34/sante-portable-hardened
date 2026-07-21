

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">🔗 Partager mon dossier</h1>
        <p class="text-gray-600 mt-1">Génère un lien temporaire pour partager ton dossier avec un médecin.</p>
    </div>
    <button onclick="document.getElementById('newLinkForm').classList.toggle('hidden')" class="btn-primary">
        ➕ Nouveau partage
    </button>
</div>

<form id="newLinkForm" method="POST" action="<?php echo e(route('share.store')); ?>" class="card mb-6 hidden">
    <?php echo csrf_field(); ?>
    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <label class="label">Durée *</label>
            <select name="duration" class="input" required>
                <option value="1h">⏱️ 1 heure</option>
                <option value="24h" selected>📅 24 heures</option>
                <option value="7d">🗓️ 7 jours</option>
            </select>
        </div>
        <div>
            <label class="label">Médecin (optionnel)</label>
            <select name="doctor_id" class="input">
                <option value="">— Aucun en particulier —</option>
                <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>">Dr. <?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Note (optionnel)</label>
            <input type="text" name="notes" class="input" placeholder="Ex: Consultation cardiologue">
        </div>
    </div>
    <button type="submit" class="btn-primary mt-4">🔗 Générer le lien</button>
</form>

<?php if($links->count()): ?>
    <div class="space-y-3">
        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <?php if($link->isValid()): ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">✅ ACTIF</span>
                            <?php elseif($link->is_revoked): ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">🚫 RÉVOQUÉ</span>
                            <?php else: ?>
                                <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-bold">⏱️ EXPIRÉ</span>
                            <?php endif; ?>
                            <span class="text-sm text-gray-500">
                                Expire le <?php echo e($link->expires_at->format('d/m/Y à H:i')); ?>

                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            👁️ <?php echo e($link->access_count); ?> accès
                            <?php if($link->last_accessed_at): ?>
                                • Dernier accès : <?php echo e($link->last_accessed_at->diffForHumans()); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <?php if($link->isValid()): ?>
                            <button onclick="showQR('<?php echo e(url('/s/'.$link->token)); ?>')" class="btn-primary text-sm">
                                📱 QR Code
                            </button>
                            <button onclick="copyLink('<?php echo e(url('/s/'.$link->token)); ?>')" class="btn-secondary text-sm">
                                📋 Copier
                            </button>
                            <form method="POST" action="<?php echo e(route('share.revoke', $link)); ?>" onsubmit="return confirm('Révoquer ?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm hover:bg-red-200">🚫 Révoquer</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="card text-center py-10">
        <div class="text-4xl mb-2">🔗</div>
        <p class="text-gray-600">Aucun partage en cours</p>
    </div>
<?php endif; ?>

<div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-sm text-center">
        <h3 class="font-bold text-xl mb-4">📱 QR Code</h3>
        <div id="qrContainer" class="bg-white p-4 inline-block border rounded-lg"></div>
        <p class="text-xs text-gray-500 mt-4" id="qrLinkDisplay"></p>
        <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="btn-secondary mt-4">Fermer</button>
    </div>
</div>

<script>
function copyLink(url) {
    navigator.clipboard.writeText(url);
    alert('✅ Lien copié !\n\n' + url);
}
function showQR(url) {
    document.getElementById('qrContainer').innerHTML =
        `<img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}" class="w-64 h-64">`;
    document.getElementById('qrLinkDisplay').textContent = url;
    document.getElementById('qrModal').classList.remove('hidden');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/share/index.blade.php ENDPATH**/ ?>