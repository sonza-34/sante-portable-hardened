@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">⏰ Rappels</h1>
    <button onclick="document.getElementById('addRem').classList.toggle('hidden')"
            class="btn-primary">+ Nouveau rappel</button>
</div>

<form id="addRem" method="POST" action="{{ route('reminders.store') }}"
      class="card mb-6 hidden">
    @csrf
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

@if($reminders->count())
    <div class="space-y-3">
        @foreach($reminders as $r)
            <div class="card flex justify-between items-center {{ $r->is_completed ? 'opacity-50' : '' }}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        @if($r->is_completed)
                            <span>✅</span>
                        @else
                            <span>🔔</span>
                        @endif
                        <div class="font-bold {{ $r->is_completed ? 'line-through' : '' }}">
                            {{ $r->title }}
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $r->due_at->format('d/m/Y à H:i') }}
                        • {{ ucfirst($r->type) }}
                    </div>
                    @if($r->description)
                        <div class="text-sm text-gray-500 mt-1">{{ $r->description }}</div>
                    @endif
                </div>
                @if(!$r->is_completed)
                    <form method="POST" action="{{ route('reminders.complete', $r) }}">
                        @csrf @method('PATCH')
                        <button class="btn-secondary text-sm">✓ Fait</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="card text-center py-10">
        <div class="text-4xl mb-2">⏰</div>
        <p class="text-gray-600">Aucun rappel. Créez-en un pour ne rien oublier !</p>
    </div>
@endif
@endsection