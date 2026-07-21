@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
{{-- En-tête --}}
<div class="mb-8 flex justify-between items-start flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">
            Bonjour {{ explode(' ', $user->name)[0] }} 👋
        </h1>
        <p class="text-gray-500 mt-1">
            @if($user->medicalRecord)
                Code dossier :
                <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100 ml-1">
                    {{ $user->medicalRecord->record_code }}
                </span>
            @elseif($isDoctor)
                <span class="text-violet-700 font-medium">Espace médecin — Dr. {{ $user->name }}</span>
            @else
                Bienvenue sur Santé Portable
            @endif
        </p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl px-4 py-2 text-right shadow-sm">
        <div class="text-sm font-medium text-gray-700">{{ now()->format('l d F Y') }}</div>
        <div class="text-xs text-gray-400">{{ now()->format('H:i') }}</div>
    </div>
</div>

{{-- Vitals cards --}}
@if($isPatient)
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="vital-card">
        <div class="vital-icon bg-indigo-50 text-indigo-600">💊</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $activePrescriptions }}</div>
        <div class="text-sm text-gray-500 mt-1">Ordonnances actives</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-emerald-50 text-emerald-600">🏥</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $totalConsultations }}</div>
        <div class="text-sm text-gray-500 mt-1">Consultations</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-violet-50 text-violet-600">💉</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $vaccinationsCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Vaccins</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-orange-50 text-orange-600">⏰</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $upcomingRemindersCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Rappels à venir</div>
    </div>
</div>
@elseif($isDoctor)
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="vital-card">
        <div class="vital-icon bg-indigo-50 text-indigo-600">👥</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $patientsCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Patients suivis</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-emerald-50 text-emerald-600">🩺</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $myConsultations }}</div>
        <div class="text-sm text-gray-500 mt-1">Consultations données</div>
    </div>
    <div class="vital-card">
        <div class="vital-icon bg-violet-50 text-violet-600">💊</div>
        <div class="text-3xl font-extrabold text-gray-900">{{ $activePrescriptions }}</div>
        <div class="text-sm text-gray-500 mt-1">Ordonnances en cours</div>
    </div>
</div>
@endif

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Accès rapide --}}
    <div class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="text-xl">🚀</span> Accès rapide
        </h2>
        <div class="space-y-2">
            @if($isPatient)
                <a href="{{ route('medical-record') }}" class="quick-link group">
                    <span class="text-xl">📋</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Mon dossier médical</div>
                        <div class="text-xs text-gray-400">Antécédents, allergies, traitements</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
                <a href="{{ route('prescriptions') }}" class="quick-link group">
                    <span class="text-xl">💊</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Mes ordonnances</div>
                        <div class="text-xs text-gray-400">{{ $activePrescriptions }} actives</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
                <a href="{{ route('vaccinations') }}" class="quick-link group">
                    <span class="text-xl">💉</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Carnet de vaccination</div>
                        <div class="text-xs text-gray-400">{{ $vaccinationsCount }} vaccins</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
                <a href="{{ route('doctors.index') }}" class="quick-link group">
                    <span class="text-xl">🩺</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-sm">Trouver un médecin</div>
                        <div class="text-xs text-gray-400">Choisissez qui accède à votre dossier</div>
                    </div>
                    <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
                </a>
            @endif
            <a href="{{ route('reminders') }}" class="quick-link group">
                <span class="text-xl">⏰</span>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">Rappels</div>
                    <div class="text-xs text-gray-400">{{ $upcomingRemindersCount }} à venir</div>
                </div>
                <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
            </a>
            <a href="{{ route('share.index') }}" class="quick-link group">
                <span class="text-xl">🔗</span>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">Partager mon dossier</div>
                    <div class="text-xs text-gray-400">QR code temporaire</div>
                </div>
                <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
            </a>
            <a href="{{ route('chatbot') }}" class="quick-link group">
                <span class="text-xl">🤖</span>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">Assistant IA</div>
                    <div class="text-xs text-gray-400">Posez vos questions santé</div>
                </div>
                <span class="text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all">→</span>
            </a>
            @if($isDoctor)
                <a href="{{ route('doctor.patients') }}" class="quick-link group bg-gradient-to-r from-indigo-50 to-violet-50 border-indigo-200">
                    <span class="text-xl">👥</span>
                    <div class="flex-1">
                        <div class="font-bold text-indigo-800 text-sm">Mes patients</div>
                        <div class="text-xs text-indigo-500">{{ $patientsCount }} patient(s) suivi(s)</div>
                    </div>
                    <span class="text-indigo-500 group-hover:translate-x-1 transition-all">→</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Prochains rappels --}}
    <div class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="text-xl">🔔</span> Prochains rappels
        </h2>
        @if($upcomingRemindersCount)
            <div class="space-y-2">
                @foreach($upcomingReminders as $r)
                    <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <div class="text-xl">
                            @switch($r->type)
                                @case('medication') 💊 @break
                                @case('appointment') 🏥 @break
                                @case('vaccination') 💉 @break
                                @case('checkup') 🩺 @break
                            @endswitch
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-800 truncate">{{ $r->title }}</div>
                            <div class="text-xs text-gray-500">{{ $r->due_at->format('d/m/Y à H:i') }}</div>
                        </div>
                        <span class="badge bg-amber-100 text-amber-700 whitespace-nowrap">
                            {{ $r->due_at->diffForHumans(null, true) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 text-gray-400">
                <div class="text-4xl mb-2">🎉</div>
                <p class="font-medium text-gray-500">Aucun rappel en attente</p>
                <a href="{{ route('reminders') }}" class="btn-primary inline-block mt-4 text-sm">Créer un rappel</a>
            </div>
        @endif
    </div>
</div>
@endsection
