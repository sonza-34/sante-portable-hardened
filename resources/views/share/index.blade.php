@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold">🔗 Partager mon dossier</h1>
        <p class="text-gray-600 mt-1">Génère un lien temporaire pour partager ton dossier avec un médecin.</p>
    </div>
    <button onclick="document.getElementById('newLinkForm').classList.toggle('hidden')" class="btn-primary">
        ➕ Nouveau partage
    </button>
</div>

<form id="newLinkForm" method="POST" action="{{ route('share.store') }}" class="card mb-6 hidden">
    @csrf
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
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}">Dr. {{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Note (optionnel)</label>
            <input type="text" name="notes" class="input" placeholder="Ex: Consultation cardiologue">
        </div>
    </div>
    <button type="submit" class="btn-primary mt-4">🔗 Générer le lien</button>
</form>

@if($links->count())
    <div class="space-y-3">
        @foreach($links as $link)
            <div class="card">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @if($link->isValid())
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">✅ ACTIF</span>
                            @elseif($link->is_revoked)
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">🚫 RÉVOQUÉ</span>
                            @else
                                <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-bold">⏱️ EXPIRÉ</span>
                            @endif
                            <span class="text-sm text-gray-500">
                                Expire le {{ $link->expires_at->format('d/m/Y à H:i') }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            👁️ {{ $link->access_count }} accès
                            @if($link->last_accessed_at)
                                • Dernier accès : {{ $link->last_accessed_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        @if($link->isValid())
                            <button onclick="showQR('{{ url('/s/'.$link->token) }}')" class="btn-primary text-sm">
                                📱 QR Code
                            </button>
                            <button onclick="copyLink('{{ url('/s/'.$link->token) }}')" class="btn-secondary text-sm">
                                📋 Copier
                            </button>
                            <form method="POST" action="{{ route('share.revoke', $link) }}" onsubmit="return confirm('Révoquer ?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm hover:bg-red-200">🚫 Révoquer</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card text-center py-10">
        <div class="text-4xl mb-2">🔗</div>
        <p class="text-gray-600">Aucun partage en cours</p>
    </div>
@endif

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
@endsection