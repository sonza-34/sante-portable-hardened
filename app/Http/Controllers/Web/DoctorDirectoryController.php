<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorDirectoryController extends Controller
{
    // Liste des médecins disponibles (côté patient)
    public function index(Request $request)
    {
        abort_if($request->user()->hasRole('doctor'), 403);

        $patient = $request->user();
        $myDoctorIds = $patient->doctors()->pluck('users.id')->toArray();

        $doctors = User::role('doctor')->get();

        return view('doctors.index', compact('doctors', 'myDoctorIds'));
    }

    // Le patient choisit un médecin
    public function follow(Request $request, User $doctor)
    {
        abort_if($request->user()->hasRole('doctor'), 403);
        abort_unless($doctor->hasRole('doctor'), 404, 'Médecin introuvable.');

        // Empêche un utilisateur de se suivre lui-même
        abort_if($doctor->id === $request->user()->id, 400, 'Action impossible.');

        $request->user()->doctors()->syncWithoutDetaching([$doctor->id]);

        return back()->with('success', '✅ Dr. ' . $doctor->name . ' a été ajouté à vos médecins.');
    }

    // Le patient retire un médecin de sa liste
    public function unfollow(Request $request, User $doctor)
    {
        abort_if($request->user()->hasRole('doctor'), 403);
        abort_if($doctor->id === $request->user()->id, 400, 'Action impossible.');

        $request->user()->doctors()->detach($doctor->id);

        return back()->with('success', 'Médecin retiré de votre liste.');
    }
}
