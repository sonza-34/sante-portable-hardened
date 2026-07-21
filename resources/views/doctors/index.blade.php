@extends('layouts.app')

@section('title', 'Trouver un médecin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">🩺 Trouver un médecin</h1>
    <p class="text-gray-500 text-sm mt-1">Choisissez un ou plusieurs médecins qui pourront accéder à votre dossier.</p>
</div>

@if($doctors->count())
    <div class="grid md:grid-cols-2 gap-4">
        @foreach($doctors as $doc)
            @php $isFollowed = in_array($doc->id, $myDoctorIds); @endphp
            <div class="card {{ $isFollowed ? 'border-indigo-200 bg-indigo-50/30' : '' }}">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shrink-0">
                        {{ strtoupper(substr($doc->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-900">Dr. {{ $doc->name }}</div>
                        <div class="text-sm text-gray-500">{{ $doc->city ?? 'Ville non renseignée' }}</div>
                    </div>
                    @if($isFollowed)
                        <span class="badge bg-indigo-100 text-indigo-700">✓ Ajouté</span>
                    @endif
                </div>

                @if($isFollowed)
                    <form method="POST" action="{{ route('doctors.unfollow', $doc) }}"
                          onsubmit="return confirm('Retirer Dr. {{ $doc->name }} de vos médecins ?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-secondary w-full text-sm">Retirer</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('doctors.follow', $doc) }}">
                        @csrf
                        <button type="submit" class="btn-primary w-full text-sm">➕ Choisir ce médecin</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="card text-center py-14">
        <div class="text-4xl mb-2">🩺</div>
        <p class="text-gray-500">Aucun médecin inscrit sur la plateforme pour le moment.</p>
    </div>
@endif
@endsection
