@extends('layouts.app')

@section('title', $patient->name)

@section('content')
<div class="mb-6 flex justify-between items-start flex-wrap gap-4">
    <div>
        <a href="{{ route('doctor.patients') }}" class="text-sm text-indigo-600 hover:underline">
            ← Retour à mes patients
        </a>
        <div class="flex items-center gap-3 mt-2">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold text-lg shrink-0">
                {{ strtoupper(substr($patient->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $patient->name }}</h1>
                <div class="text-gray-500 text-sm">
                    {{ $patient->date_of_birth ? $patient->date_of_birth->age . ' ans' : '—' }}
                    • {{ $patient->gender === 'male' ? 'Homme' : ($patient->gender === 'female' ? 'Femme' : '—') }}
                    • {{ $patient->city ?? '—' }}
                </div>
            </div>
        </div>
        @if($record)
            <div class="badge bg-indigo-50 text-indigo-700 font-mono mt-3">
                {{ $record->record_code }}
            </div>
        @endif
    </div>
    <a href="{{ route('doctor.create-consultation', $patient) }}" class="btn-primary">
        ➕ Nouvelle consultation
    </a>
</div>

{{-- Actions rapides médecin --}}
<div class="grid md:grid-cols-3 gap-4 mb-6" x-data="{ open: null }">
    {{-- Nouvelle ordonnance --}}
    <div class="card !p-0 overflow-hidden">
        <button @click="open = open === 'rx' ? null : 'rx'" class="w-full flex items-center justify-between px-5 py-4 font-semibold text-gray-800 text-sm">
            <span>💊 Nouvelle ordonnance</span>
            <span x-text="open === 'rx' ? '−' : '+'" class="text-indigo-500 text-lg"></span>
        </button>
        <div x-show="open === 'rx'" x-cloak class="px-5 pb-5 space-y-3 border-t border-gray-100 pt-4">
            <form method="POST" action="{{ route('doctor.store-prescription', $patient) }}" class="space-y-3">
                @csrf
                <input type="text" name="medication_name" class="input" placeholder="Médicament *" required>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="dosage" class="input" placeholder="Dosage *" required>
                    <input type="text" name="frequency" class="input" placeholder="Fréquence *" required>
                </div>
                <input type="text" name="duration" class="input" placeholder="Durée *" required>
                <textarea name="instructions" class="input" placeholder="Instructions (optionnel)" rows="2"></textarea>
                <button type="submit" class="btn-primary w-full text-sm">Créer l'ordonnance</button>
            </form>
        </div>
    </div>

    {{-- Nouveau rappel --}}
    <div class="card !p-0 overflow-hidden">
        <button @click="open = open === 'reminder' ? null : 'reminder'" class="w-full flex items-center justify-between px-5 py-4 font-semibold text-gray-800 text-sm">
            <span>⏰ Nouveau rappel</span>
            <span x-text="open === 'reminder' ? '−' : '+'" class="text-indigo-500 text-lg"></span>
        </button>
        <div x-show="open === 'reminder'" x-cloak class="px-5 pb-5 space-y-3 border-t border-gray-100 pt-4">
            <form method="POST" action="{{ route('doctor.store-reminder', $patient) }}" class="space-y-3">
                @csrf
                <select name="type" class="input" required>
                    <option value="medication">💊 Médicament</option>
                    <option value="appointment">🏥 Rendez-vous</option>
                    <option value="vaccination">💉 Vaccination</option>
                    <option value="checkup">🩺 Contrôle</option>
                </select>
                <input type="text" name="title" class="input" placeholder="Titre *" required>
                <textarea name="description" class="input" placeholder="Description (optionnel)" rows="2"></textarea>
                <input type="datetime-local" name="due_at" class="input" required>
                <button type="submit" class="btn-primary w-full text-sm">Créer le rappel</button>
            </form>
        </div>
    </div>

    {{-- Modifier dossier médical --}}
    <div class="card !p-0 overflow-hidden">
        <button @click="open = open === 'record' ? null : 'record'" class="w-full flex items-center justify-between px-5 py-4 font-semibold text-gray-800 text-sm">
            <span>📋 Modifier le dossier</span>
            <span x-text="open === 'record' ? '−' : '+'" class="text-indigo-500 text-lg"></span>
        </button>
        <div x-show="open === 'record'" x-cloak class="px-5 pb-5 space-y-3 border-t border-gray-100 pt-4">
            <form method="POST" action="{{ route('doctor.update-medical-record', $patient) }}" class="space-y-3">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" step="0.01" name="height_cm" class="input" placeholder="Taille (cm)" value="{{ optional($record)->height_cm }}">
                    <input type="number" step="0.01" name="weight_kg" class="input" placeholder="Poids (kg)" value="{{ optional($record)->weight_kg }}">
                </div>
                <textarea name="chronic_conditions" class="input" placeholder="Maladies chroniques" rows="2">{{ optional($record)->chronic_conditions }}</textarea>
                <textarea name="allergies" class="input" placeholder="Allergies" rows="2">{{ optional($record)->allergies }}</textarea>
                <textarea name="current_treatments" class="input" placeholder="Traitements en cours" rows="2">{{ optional($record)->current_treatments }}</textarea>
                <button type="submit" class="btn-primary w-full text-sm">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

@if($record)
    <div class="space-y-3 mb-6">
        @if($record->chronic_conditions)
            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <div class="font-bold text-red-700 text-sm mb-1">⚠️ Maladies chroniques</div>
                <div class="text-sm text-red-700/80">{{ $record->chronic_conditions }}</div>
            </div>
        @endif
        @if($record->allergies)
            <div class="bg-orange-50 border border-orange-100 rounded-xl p-4">
                <div class="font-bold text-orange-700 text-sm mb-1">🚨 Allergies</div>
                <div class="text-sm text-orange-700/80">{{ $record->allergies }}</div>
            </div>
        @endif
        @if($record->current_treatments)
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                <div class="font-bold text-indigo-700 text-sm mb-1">💊 Traitements en cours</div>
                <div class="text-sm text-indigo-700/80">{{ $record->current_treatments }}</div>
            </div>
        @endif
    </div>
@endif

<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <div class="vital-card text-center">
        <div class="text-xs text-gray-400 font-medium mb-1">Taille</div>
        <div class="text-2xl font-extrabold text-gray-900">{{ $record->height_cm ?? '—' }} <span class="text-sm font-medium text-gray-400">cm</span></div>
    </div>
    <div class="vital-card text-center">
        <div class="text-xs text-gray-400 font-medium mb-1">Poids</div>
        <div class="text-2xl font-extrabold text-gray-900">{{ $record->weight_kg ?? '—' }} <span class="text-sm font-medium text-gray-400">kg</span></div>
    </div>
    <div class="vital-card text-center">
        <div class="text-xs text-gray-400 font-medium mb-1">Groupe sanguin</div>
        <div class="text-2xl font-extrabold text-gray-900">{{ $patient->blood_type ?? '—' }}</div>
    </div>
</div>

<div class="card">
    <h2 class="text-lg font-bold text-gray-900 mb-4">🏥 Historique des consultations</h2>
    @forelse($consultations as $c)
        <div class="border-l-4 {{ $c->doctor_id === auth()->id() ? 'border-emerald-500' : 'border-gray-200' }} pl-4 py-3 mb-3 last:mb-0">
            <div class="flex justify-between items-start flex-wrap gap-2">
                <div>
                    <div class="font-bold text-gray-900">{{ $c->facility_name }} — {{ $c->facility_city }}</div>
                    <div class="text-sm text-gray-500">
                        {{ $c->consultation_date->format('d/m/Y') }} • Dr. {{ $c->doctor->name }}
                        @if($c->doctor_id === auth()->id())
                            <span class="text-emerald-600 font-bold">(vous)</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-700"><strong>Motif :</strong> {{ $c->reason }}</div>
            @if($c->diagnosis)
                <div class="mt-1 text-sm text-gray-700"><strong>Diagnostic :</strong> {{ $c->diagnosis }}</div>
            @endif
            @if($c->vital_signs)
                <div class="mt-2 text-sm bg-gray-50 border border-gray-100 px-3 py-2 rounded-lg text-gray-600">📊 {{ $c->vital_signs }}</div>
            @endif
            @if($c->prescriptions->count())
                <div class="mt-2 text-sm">
                    <strong class="text-gray-700">Ordonnances :</strong>
                    <ul class="list-disc ml-5 mt-1 text-gray-600">
                        @foreach($c->prescriptions as $rx)
                            <li>{{ $rx->medication_name }} — {{ $rx->dosage }} ({{ $rx->frequency }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-400 text-sm">Aucune consultation enregistrée.</p>
    @endforelse
</div>
@endsection
