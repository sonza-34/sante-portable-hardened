<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Inscription publique.
     *
     * NOTE SÉCURITÉ : Le rôle 'doctor' n'est PAS accepté en self-service. Tout médecin
     * doit être onboardé via invitation admin (procédure à mettre en place — voir TODO).
     * Cette restriction évite qu'un inconnu s'auto-proclame médecin et accède à des
     * dossiers patients.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'city' => 'nullable|string',
            // Rôle 'patient' uniquement — voir docblock ci-dessus.
            'role' => 'required|in:patient',
        ], [
            'role.in' => 'Le rôle "doctor" nécessite une invitation. Inscrivez-vous en tant que patient.',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères avec majuscule, minuscule et chiffre.',
        ]);

        // Log si une tentative de bypass du rôle a quand même atteint la validation
        // (ne devrait pas arriver avec la validation ci-dessus, mais ceinture & bretelles).
        if ($request->input('role') && $request->input('role') !== 'patient') {
            Log::channel('medical')->warning('Tentative inscription rôle non-patient', [
                'attempted_role' => $request->input('role'),
                'email' => $validated['email'],
                'ip' => $request->ip(),
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'city' => $validated['city'] ?? null,
        ]);

        $user->assignRole('patient');

        // Création automatique du dossier médical avec record_code garanti unique.
        $this->createMedicalRecordWithUniqueCode($user);

        Auth::login($user);

        return redirect()->route('dashboard')
                         ->with('success', 'Bienvenue sur Santé Portable !');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                             ->with('success', 'Connexion réussie');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects'])
                     ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Génère un record_code unique avec retry loop.
     *
     * Str::random(8) → 36^8 = ~2.8 trillion de combinaisons, donc collision quasi
     * impossible, mais on boucle par sécurité pour garantir l'unicité en DB.
     */
    private function createMedicalRecordWithUniqueCode(User $user): MedicalRecord
    {
        do {
            $code = 'SP-MA-' . strtoupper(Str::random(8));
        } while (MedicalRecord::where('record_code', $code)->exists());

        return MedicalRecord::create([
            'user_id' => $user->id,
            'record_code' => $code,
        ]);
    }
}
