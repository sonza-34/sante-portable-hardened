@extends('layouts.app')

@section('title', 'Mes patients')

@section('content')
<div class="mb-6 flex justify-between items-start flex-wrap gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">👥 Mes patients</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $patients->count() }} patient(s) suivi(s)</p>
    </div>
    <form method="POST" action="{{ route('doctor.add-patient') }}" class="flex gap-2">
        @csrf
        <input type="text" name="record_code" class="input !w-56" placeholder="Code dossier (SP-MA-XXXXXXXX)" required>
        <button type="submit" class="btn-primary whitespace-nowrap">➕ Ajouter</button>
    </form>
</div>

@error('record_code')
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">{{ $message }}</div>
@enderror

@if($patients->count())
    <div class="grid md:grid-cols-2 gap-4">
        @foreach($patients as $p)
            <div class="card card-hover">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shrink-0">
                            {{ strtoupper(substr($p->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ $p->name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $p->date_of_birth ? $p->date_of_birth->age . ' ans' : '—' }}
                                • {{ $p->city ?? '—' }}
                            </div>
                        </div>
                    </div>
                    @if($p->medicalRecord)
                        <span class="badge bg-indigo-50 text-indigo-700 font-mono">
                            {{ $p->medicalRecord->record_code }}
                        </span>
                    @endif
                </div>

                @if($p->medicalRecord && $p->medicalRecord->chronic_conditions)
                    <div class="text-sm mb-3 bg-red-50 text-red-700 px-3 py-2 rounded-lg">
                        ⚠️ {{ \Illuminate\Support\Str::limit($p->medicalRecord->chronic_conditions, 80) }}
                    </div>
                @endif

                <a href="{{ route('doctor.show-patient', $p) }}" class="btn-primary w-full text-sm">
                    📋 Voir le dossier complet
                </a>
                <form method="POST" action="{{ route('doctor.remove-patient', $p) }}"
                      onsubmit="return confirm('Retirer {{ $p->name }} de votre liste ?');" class="mt-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger w-full text-xs">🗑️ Retirer de ma liste</button>
                </form>
            </div>
        @endforeach
    </div>
@else
    <div class="card text-center py-14">
        <div class="text-4xl mb-2">👥</div>
        <p class="text-gray-500">Vous n'avez pas encore de patients.</p>
        <p class="text-gray-400 text-sm mt-1">Ajoutez-en un avec son code dossier (visible sur son tableau de bord patient).</p>
    </div>
@endif
@endsection
