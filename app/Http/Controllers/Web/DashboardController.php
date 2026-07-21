<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isDoctor = $user->hasRole('doctor');
        $isPatient = $user->hasRole('patient');

        // Stats patient
        $activePrescriptions = $isPatient
            ? $user->prescriptions()->where('is_active', true)->count()
            : 0;

        $totalConsultations = $isPatient
            ? \App\Models\Consultation::where('user_id', $user->id)->count()
            : 0;

        $vaccinationsCount = $isPatient
            ? $user->vaccinations()->count()
            : 0;

        $upcomingRemindersCount = $user->reminders()->upcoming()->count();
        $completedReminders = $user->reminders()->where('is_completed', true)->count();
        $totalReminders = $user->reminders()->count();

        // Stats médecin — utilise la source unique doctor_patient pour la cohérence
        // (auparavant on comptait via Consultation::distinct(user_id), incohérent avec
        // la relation belongsToMany utilisée partout ailleurs).
        $patientsCount = $isDoctor
            ? $user->patients()->count()
            : 0;

        $myConsultations = $isDoctor
            ? \App\Models\Consultation::where('doctor_id', $user->id)->count()
            : 0;

        // Prochains rappels (max 5)
        $upcomingReminders = $user->reminders()->upcoming()->take(5)->get();

        return view('dashboard', compact(
            'user', 'isPatient', 'isDoctor',
            'activePrescriptions', 'totalConsultations', 'vaccinationsCount',
            'upcomingRemindersCount', 'completedReminders', 'totalReminders',
            'patientsCount', 'myConsultations', 'upcomingReminders'
        ));
    }
}
