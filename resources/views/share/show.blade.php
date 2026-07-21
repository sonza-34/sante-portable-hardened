<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier partagé — {{ $patient->name }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen">
<div class="bg-blue-600 text-white p-3 text-center text-sm">
    🔗 <strong>Accès partagé</strong> — Consultation via lien temporaire, tracé (IP + horodatage).
</div>
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="card mb-6">
        <h1 class="text-3xl font-bold mb-2">{{ $patient->name }}</h1>
        <div class="text-gray-600">
            {{ $patient->date_of_birth?->age ?? '—' }} ans • {{ $patient->city ?? '—' }}
        </div>
        @if($record)
            <div class="font-mono text-sm bg-blue-100 text-blue-700 inline-block px-2 py-1 rounded mt-2">
                {{ $record->record_code }}
            </div>
        @endif
    </div>

    @if($record)
        @if($record->chronic_conditions)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="font-bold text-red-700">⚠️ Maladies chroniques</div>
                <div>{{ $record->chronic_conditions }}</div>
            </div>
        @endif
        @if($record->allergies)
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-4">
                <div class="font-bold text-orange-700">🚨 Allergies</div>
                <div>{{ $record->allergies }}</div>
            </div>
        @endif
    @endif

    <div class="card mb-6">
        <h2 class="text-xl font-bold mb-4">🏥 Historique des consultations</h2>
        @forelse($consultations as $c)
            <div class="border-l-4 border-blue-500 pl-4 py-3 mb-3">
                <div class="font-bold">{{ $c->facility_name }} — {{ $c->facility_city }}</div>
                <div class="text-sm text-gray-600">
                    {{ $c->consultation_date->format('d/m/Y') }} • Dr. {{ $c->doctor->name }}
                </div>
                @if($c->diagnosis)
                    <div class="mt-1"><strong>Diagnostic :</strong> {{ $c->diagnosis }}</div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">Aucune consultation.</p>
        @endforelse
    </div>
</div>
</body>
</html>