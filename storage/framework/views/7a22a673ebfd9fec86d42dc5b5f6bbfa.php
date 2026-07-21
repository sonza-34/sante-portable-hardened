

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-2">🤖 Assistant Santé IA</h1>
    <p class="text-gray-600 mb-6">Posez vos questions en français.</p>

    <div id="chatBox" class="card min-h-[500px] max-h-[600px] overflow-y-auto mb-4 space-y-3 bg-gray-50">
        <div class="flex gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">🤖</div>
            <div class="bg-white border rounded-lg p-3 max-w-[80%]">
                <p class="font-medium">Bonjour <?php echo e(auth()->user()->name); ?> ! 👋</p>
                <p class="text-sm mt-1">Je peux vous guider, vulgariser des termes médicaux, et répondre sur vos données.</p>
            </div>
        </div>
    </div>

    <form id="chatForm" class="flex gap-2">
        <?php echo csrf_field(); ?>
        <input type="text" id="msgInput" class="input flex-1" placeholder="Posez votre question..." required maxlength="1000">
        <button type="submit" id="sendBtn" class="btn-primary">Envoyer</button>
    </form>

    <p class="text-xs text-gray-500 mt-2 text-center">
        ⚠️ Cet assistant ne pose pas de diagnostic. En cas d'urgence, appelez le <strong>15</strong> (SAMU Maroc).
    </p>
</div>

<script>
const chatBox = document.getElementById('chatBox');
const chatForm = document.getElementById('chatForm');
const msgInput = document.getElementById('msgInput');
const sendBtn = document.getElementById('sendBtn');

function esc(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

function addMsg(c, isU, intent) {
    const d = document.createElement('div');
    d.className = 'flex gap-3 ' + (isU ? 'justify-end' : '');
    let h = '';
    if (!isU) h += '<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">🤖</div>';
    const cls = isU ? 'bg-blue-600 text-white' : 'bg-white border';
    const b = intent === 'urgence' ? ' border-2 border-red-500' : '';
    h += `<div class="${cls} rounded-lg p-3 max-w-[80%]${b}">${c}</div>`;
    if (isU) h += '<div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 text-sm">👤</div>';
    d.innerHTML = h;
    chatBox.appendChild(d);
    chatBox.scrollTop = chatBox.scrollHeight;
}

chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const m = msgInput.value.trim();
    if (!m) return;
    addMsg(esc(m), true);
    msgInput.value = '';
    sendBtn.disabled = true; sendBtn.textContent = '...';
    try {
        const r = await fetch('<?php echo e(route("chatbot.message")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify({ message: m })
        });
        const d = await r.json();
        addMsg(esc(d.reply).replace(/\n/g, '<br>'), false, d.intent);
    } catch (e) {
        addMsg('❌ Erreur réseau', false);
    } finally {
        sendBtn.disabled = false; sendBtn.textContent = 'Envoyer';
        msgInput.focus();
    }
});

msgInput.focus();
chatBox.scrollTop = chatBox.scrollHeight;
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/chatbot/index.blade.php ENDPATH**/ ?>