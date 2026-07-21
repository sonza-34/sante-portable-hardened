@extends('layouts.app')

@section('content')
<a href="{{ route('doctor.show-patient', $patient) }}" class="text-sm text-blue-600 hover:underline">
    ← Retour au dossier de {{ $patient->name }}
</a>
<h1 class="text-3xl font-bold mt-2 mb-6">➕ Nouvelle consultation</h1>

@if($record && $record->chronic_conditions)
    <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
        <strong>⚠️ À savoir :</strong> {{ $record->chronic_conditions }}
        @if($record->allergies) • <strong>Allergies :</strong> {{ $record->allergies }} @endif
    </div>
@endif

<form method="POST" action="{{ route('doctor.store-consultation', $patient) }}" class="card">
    @csrf
    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="label">Établissement *</label>
            <input type="text" name="facility_name" class="input" required value="Hôpital Ibn Rochd">
        </div>
        <div>
            <label class="label">Ville *</label>
            <input type="text" name="facility_city" class="input" required value="Casablanca">
        </div>
        <div>
            <label class="label">Date *</label>
            <input type="date" name="consultation_date" class="input" required value="{{ date('Y-m-d') }}">
        </div>
        <div>
            <label class="label">Constantes</label>
            <input type="text" name="vital_signs" class="input" placeholder="TA: 130/80, Glycémie: 1.20 g/L">
        </div>
    </div>

    <div class="mb-4">
        <label class="label">Motif de consultation *</label>
        <input type="text" name="reason" class="input" required
               placeholder="Ex: Contrôle diabète, Fièvre persistante...">
    </div>

    <div class="mb-4">
        <label class="label">Diagnostic</label>
        <textarea name="diagnosis" class="input" rows="2"></textarea>
    </div>

    <div class="mb-6">
        <label class="label">Notes / Observations</label>
        <textarea name="notes" class="input" rows="3"></textarea>
    </div>

    <h2 class="text-lg font-bold mb-3 border-b pb-2">💊 Ordonnances</h2>

    <div id="prescriptions">
        @for($i = 0; $i < 3; $i++)
            <div class="border rounded-lg p-3 mb-3 bg-gray-50">
                <div class="text-sm font-bold mb-2 text-gray-700">Médicament {{ $i + 1 }}</div>
                <div class="grid md:grid-cols-2 gap-3">
                    <input type="text" name="prescriptions[{{ $i }}][medication_name]"
                           class="input" placeholder="Nom (ex: Metformine)">
                    <input type="text" name="prescriptions[{{ $i }}][dosage]"
                           class="input" placeholder="Dosage (ex: 1000mg)">
                    <input type="text" name="prescriptions[{{ $i }}][frequency]"
                           class="input" placeholder="Fréquence (ex: 2x/jour)">
                    <input type="text" name="prescriptions[{{ $i }}][duration]"
                           class="input" placeholder="Durée (ex: 3 mois)">
                </div>
            </div>
        @endfor
    </div>

    <button type="submit" class="btn-primary w-full mt-4 text-lg">
        💾 Enregistrer la consultation
    </button>
</form>
@endsection