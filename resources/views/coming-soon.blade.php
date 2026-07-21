@extends('layouts.app')

@section('content')
<div class="card text-center py-16">
    <div class="text-6xl mb-4">🚧</div>
    <h1 class="text-3xl font-bold mb-2">{{ $feature }}</h1>
    <p class="text-gray-600 mb-6">Cette page arrive dans l'étape suivante.</p>
    <a href="{{ route('dashboard') }}" class="btn-primary">← Retour au tableau de bord</a>
</div>
@endsection