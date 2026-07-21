@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
{{-- NAVBAR --}}
<nav class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md">🏥</div>
            <span class="font-bold text-gray-900 text-lg">Santé Portable</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="#fonctionnalites" class="hover:text-indigo-600 transition">Fonctionnalités</a>
            <a href="#comment-ca-marche" class="hover:text-indigo-600 transition">Comment ça marche</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary text-sm">Mon tableau de bord →</a>
            @else
                <a href="{{ route('login') }}" class="btn-secondary text-sm">Se connecter</a>
                <a href="{{ route('register') }}" class="btn-primary text-sm">Créer mon dossier</a>
            @endauth
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-b from-indigo-50/60 via-white to-white px-4 pt-16 pb-20">
    <div class="relative max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-sm font-semibold mb-6">
            🇲🇦 Conçu pour le Maroc
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 mb-6 leading-tight tracking-tight">
            Votre dossier médical,
            <span class="bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">
                partout avec vous
            </span>
        </h1>
        <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-9">
            Un dossier médical numérique unique, accessible par vous et vos soignants, dans tout le Maroc.
            Ordonnances, vaccins et rappels, réunis au même endroit.
        </p>
        <div class="flex gap-3 justify-center flex-wrap mb-10">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary text-base px-7 py-3.5">Mon tableau de bord →</a>
            @else
                <a href="{{ route('register') }}" class="btn-primary text-base px-7 py-3.5">Créer mon dossier gratuit</a>
                <a href="{{ route('login') }}" class="btn-secondary text-base px-7 py-3.5">Se connecter</a>
            @endauth
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-gray-500">
            <span class="flex items-center gap-1.5"><span class="text-green-500">✓</span> 100% gratuit</span>
            <span class="flex items-center gap-1.5"><span class="text-green-500">✓</span> Données chiffrées</span>
            <span class="flex items-center gap-1.5"><span class="text-green-500">✓</span> Accessible partout au Maroc</span>
            <span class="flex items-center gap-1.5"><span class="text-green-500">✓</span> Sans papier</span>
        </div>
    </div>
</section>

{{-- PROBLEME / SOLUTION --}}
<section id="fonctionnalites" class="max-w-5xl mx-auto px-4 py-20">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Le problème qu'on résout</h2>
        <p class="text-gray-500">Fini les dossiers papier qui se perdent d'un cabinet à l'autre.</p>
    </div>
    <div class="grid md:grid-cols-2 gap-5">
        <div class="card border-l-4 border-red-400">
            <div class="text-2xl mb-3">❌</div>
            <h3 class="font-bold text-lg mb-2 text-gray-900">Patient transféré sans historique</h3>
            <p class="text-gray-500 text-sm">Marrakech → Casablanca : le médecin repart de zéro.</p>
        </div>
        <div class="card border-l-4 border-green-500">
            <div class="text-2xl mb-3">✅</div>
            <h3 class="font-bold text-lg mb-2 text-gray-900">Continuité des soins garantie</h3>
            <p class="text-gray-500 text-sm">Code unique portable, visible partout au Maroc.</p>
        </div>
        <div class="card border-l-4 border-red-400">
            <div class="text-2xl mb-3">❌</div>
            <h3 class="font-bold text-lg mb-2 text-gray-900">Ordonnances perdues</h3>
            <p class="text-gray-500 text-sm">Papier froissé, carnet de vaccination oublié.</p>
        </div>
        <div class="card border-l-4 border-green-500">
            <div class="text-2xl mb-3">✅</div>
            <h3 class="font-bold text-lg mb-2 text-gray-900">Documents en sécurité</h3>
            <p class="text-gray-500 text-sm">Scannez et stockez radios, ordonnances, analyses.</p>
        </div>
    </div>
</section>

{{-- FONCTIONNALITES --}}
<section id="comment-ca-marche" class="bg-gray-50 border-y border-gray-100 py-20">
    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Tout ce qu'il vous faut, au même endroit</h2>
            <p class="text-gray-500">Une plateforme complète pour patients et médecins.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="card card-hover">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-2xl mb-4">📋</div>
                <h3 class="font-bold text-gray-900 mb-1.5">Dossier médical unique</h3>
                <p class="text-sm text-gray-500">Antécédents, allergies et traitements accessibles partout.</p>
            </div>
            <div class="card card-hover">
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-2xl mb-4">💊</div>
                <h3 class="font-bold text-gray-900 mb-1.5">Ordonnances centralisées</h3>
                <p class="text-sm text-gray-500">Toutes vos prescriptions, actives ou passées, en un clic.</p>
            </div>
            <div class="card card-hover">
                <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center text-2xl mb-4">💉</div>
                <h3 class="font-bold text-gray-900 mb-1.5">Carnet de vaccination</h3>
                <p class="text-sm text-gray-500">Suivez vos vaccins sans jamais perdre le carnet papier.</p>
            </div>
            <div class="card card-hover">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-2xl mb-4">⏰</div>
                <h3 class="font-bold text-gray-900 mb-1.5">Rappels intelligents</h3>
                <p class="text-sm text-gray-500">Ne manquez plus un rendez-vous ou une prise de médicament.</p>
            </div>
            <div class="card card-hover">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl mb-4">🔗</div>
                <h3 class="font-bold text-gray-900 mb-1.5">Partage sécurisé</h3>
                <p class="text-sm text-gray-500">Donnez un accès temporaire à un médecin via un lien.</p>
            </div>
            <div class="card card-hover">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-2xl mb-4">🤖</div>
                <h3 class="font-bold text-gray-900 mb-1.5">Assistant IA</h3>
                <p class="text-sm text-gray-500">Posez vos questions santé et naviguez plus facilement.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="max-w-3xl mx-auto text-center px-4 py-20">
    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Prêt à ne plus jamais perdre un document médical ?</h2>
    <p class="text-gray-500 mb-8">Créez votre dossier en quelques minutes, gratuitement.</p>
    @guest
        <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-4 inline-block">
            Créer mon dossier gratuitement
        </a>
    @endguest
</section>
@endsection
