@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 lg:p-8">
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden grid lg:grid-cols-2">

        {{-- Panneau gauche --}}
        <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-10 text-white relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-56 h-56 bg-white/10 rounded-full"></div>
            <div class="absolute bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full"></div>

            <a href="{{ url('/') }}" class="flex items-center gap-2.5 relative z-10">
                <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">🏥</div>
                <span class="font-bold">Santé Portable</span>
            </a>

            <div class="relative z-10">
                <div class="text-6xl mb-6">🩺</div>
                <h2 class="text-2xl font-bold mb-3 leading-snug">Votre santé, toujours avec vous.</h2>
                <p class="text-indigo-100 text-sm mb-6">Un dossier médical unique, partagé en toute sécurité avec vos soignants partout au Maroc.</p>
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur px-3 py-1.5 rounded-full text-xs font-medium mr-2">🇲🇦 Fait pour le Maroc</div>
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur px-3 py-1.5 rounded-full text-xs font-medium">🔒 100% sécurisé</div>
                </div>
            </div>

            <div></div>
        </div>

        {{-- Panneau droit : formulaire --}}
        <div class="p-8 sm:p-12 flex flex-col justify-center">
            <div class="lg:hidden flex items-center gap-2.5 mb-8">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white">🏥</div>
                <span class="font-bold text-gray-900">Santé Portable</span>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Bon retour 👋</h1>
            <p class="text-gray-500 text-sm mb-7">Connectez-vous pour accéder à votre espace santé.</p>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" class="input" value="{{ old('email') }}" placeholder="vous@exemple.com" required autofocus>
                </div>
                <div x-data="{ show: false }">
                    <label class="label">Mot de passe</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" class="input pr-11" placeholder="••••••••" required>
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 text-sm">
                            <span x-text="show ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full mt-2">Se connecter</button>
            </form>

            <p class="text-center mt-6 text-gray-500 text-sm">
                Pas encore de compte ? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Créer mon dossier</a>
            </p>
        </div>
    </div>
</div>
@endsection
