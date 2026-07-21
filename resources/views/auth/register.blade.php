@extends('layouts.guest')

@section('title', 'Créer mon dossier')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 py-10">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 mb-6">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white">🏥</div>
                <span class="font-bold text-gray-900">Santé Portable</span>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Créer mon dossier médical</h1>
            <p class="text-gray-500 text-sm mt-1">Gratuit, sécurisé, en moins de 2 minutes.</p>
        </div>

        <div class="card"
             x-data="{
                step: {{ $errors->hasAny(['password','password_confirmation']) ? 3 : ($errors->hasAny(['date_of_birth','city','role']) ? 2 : 1) }},
                totalSteps: 3,
                next() {
                    const fields = this.$refs['step' + this.step].querySelectorAll('[required]');
                    for (const f of fields) { if (!f.reportValidity()) return; }
                    if (this.step < this.totalSteps) this.step++;
                },
                prev() { if (this.step > 1) this.step--; }
             }">

            {{-- Barre de progression --}}
            <div class="flex items-center gap-2 mb-8">
                <template x-for="s in totalSteps" :key="s">
                    <div class="flex-1 h-1.5 rounded-full" :class="s <= step ? 'bg-gradient-to-r from-indigo-600 to-violet-600' : 'bg-gray-100'"></div>
                </template>
            </div>
            <div class="flex justify-between text-xs font-medium text-gray-400 mb-6 -mt-4">
                <span :class="step === 1 && 'text-indigo-600'">1. Vos infos</span>
                <span :class="step === 2 && 'text-indigo-600'">2. Profil</span>
                <span :class="step === 3 && 'text-indigo-600'">3. Sécurité</span>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    <ul class="list-disc ml-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                {{-- Étape 1 : informations personnelles --}}
                <div x-show="step === 1" x-ref="step1" x-cloak class="space-y-4">
                    <div>
                        <label class="label">Nom complet *</label>
                        <input type="text" name="name" class="input" value="{{ old('name') }}" required autofocus>
                    </div>
                    <div>
                        <label class="label">Email *</label>
                        <input type="email" name="email" class="input" value="{{ old('email') }}" required>
                    </div>
                    <div>
                        <label class="label">Téléphone</label>
                        <input type="tel" name="phone" class="input" value="{{ old('phone') }}" placeholder="+212 6XX XX XX XX">
                    </div>
                </div>

                {{-- Étape 2 : profil médical --}}
                <div x-show="step === 2" x-ref="step2" x-cloak class="space-y-4">
                    <div>
                        <label class="label">Date de naissance</label>
                        <input type="date" name="date_of_birth" class="input" value="{{ old('date_of_birth') }}">
                    </div>
                    <div>
                        <label class="label">Ville</label>
                        <input type="text" name="city" class="input" value="{{ old('city') }}" placeholder="Marrakech, Casablanca...">
                    </div>
                    <div>
                        <label class="label">Vous êtes *</label>
                        {{-- Champ hidden : on force role=patient (les médecins sont onboardés par invitation admin) --}}
                        <input type="hidden" name="role" value="patient">
                        <div class="input bg-gray-50 cursor-not-allowed flex items-center justify-between" aria-disabled="true">
                            <span>👤 Patient</span>
                            <span class="text-xs text-gray-500">Inscription</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <strong>Vous êtes médecin ?</strong> L'accès médecin se fait sur invitation admin uniquement, pour des raisons de sécurité et de conformité. Contactez-nous à <a href="mailto:pro@sante-portable.ma" class="text-indigo-600 underline">pro@sante-portable.ma</a>.
                        </p>
                    </div>
                </div>

                {{-- Étape 3 : sécurité --}}
                <div x-show="step === 3" x-ref="step3" x-cloak class="space-y-4">
                    <div>
                        <label class="label">Mot de passe *</label>
                        <input type="password" name="password" class="input" required>
                    </div>
                    <div>
                        <label class="label">Confirmer le mot de passe *</label>
                        <input type="password" name="password_confirmation" class="input" required>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="flex gap-3 mt-8">
                    <button type="button" x-show="step > 1" x-cloak @click="prev()" class="btn-secondary flex-1">← Précédent</button>
                    <button type="button" x-show="step < totalSteps" @click="next()" class="btn-primary flex-1">Suivant →</button>
                    <button type="submit" x-show="step === totalSteps" x-cloak class="btn-primary flex-1">Créer mon compte</button>
                </div>
            </form>
        </div>

        <p class="text-center mt-6 text-gray-500 text-sm">
            Déjà un compte ? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Se connecter</a>
        </p>
    </div>
</div>
@endsection
