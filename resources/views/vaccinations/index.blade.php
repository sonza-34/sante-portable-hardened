@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">💉 Carnet de vaccination</h1>
    <button onclick="document.getElementById('addVax').classList.toggle('hidden')"
            class="btn-primary">+ Ajouter</button>
</div>

<form id="addVax" method="POST" action="{{ route('vaccinations.store') }}"
      class="card mb-6 hidden">
    @csrf
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="label">Nom du vaccin *</label>
            <input type="text" name="vaccine_name" class="input" required
                   placeholder="DTC, ROR, BCG...">
        </div>
        <div>
            <label class="label">Date d'administration *</label>
            <input type="date" name="administration_date" class="input" required>
        </div>
        <div>
            <label class="label">Prochaine dose</label>
            <input type="date" name="next_dose_date" class="input">
        </div>
        <div>
            <label class="label">N° de lot</label>
            <input type="text" name="batch_number" class="input">
        </div>
        <div>
            <label class="label">Administré par</label>
            <input type="text" name="administered_by" class="input">
        </div>
        <div>
            <label class="label">Lieu</label>
            <input type="text" name="facility_name" class="input">
        </div>
    </div>
    <button type="submit" class="btn-primary mt-4">💾 Enregistrer</button>
</form>

@if($vaccinations->count())
    <div class="space-y-3">
        @foreach($vaccinations as $v)
            <div class="card flex justify-between items-center">
                <div>
                    <div class="font-bold text-lg">{{ $v->vaccine_name }}</div>
                    <div class="text-sm text-gray-600">
                        Administré le {{ $v->administration_date->format('d/m/Y') }}
                        @if($v->facility_name) — {{ $v->facility_name }} @endif
                    </div>
                </div>
                @if($v->next_dose_date)
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Prochaine dose</div>
                        <div class="font-bold text-blue-700">
                            {{ $v->next_dose_date->format('d/m/Y') }}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="card text-center py-10">
        <div class="text-4xl mb-2">💉</div>
        <p class="text-gray-600">Aucun vaccin enregistré</p>
    </div>
@endif
@endsection