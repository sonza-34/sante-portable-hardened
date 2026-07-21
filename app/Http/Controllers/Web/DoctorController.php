<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Policies\PatientAccessPolicy;

class DoctorController extends Controller
{
    // Liste des patients suivis par le médecin (relation explicite)
    public function patients(Request $request)
    {
        $patients = $request->user()
            ->patients()
            ->with('medicalRecord')
            ->get();

        return view('doctor.patients', compact('patients'));
    }

    // Ajouter un patient via son code dossier (ex: SP-MA-XXXXXXXX)
    public function addPatient(Request $request)
    {
        $validated = $request->validate([
            'record_code' => 'required|string',
        ]);

        $record = MedicalRecord::where('record_code', strtoupper(trim($validated['record_code'])))->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'record_code' => "Aucun patient ne correspond à ce code dossier.",
            ]);
        }

        $doctor = $request->user();

        // Empêche de s'ajouter soi-même
        abort_if($record->user_id === $doctor->id, 400, 'Action impossible.');

        if ($doctor->patients()->where('patient_id', $record->user_id)->exists()) {
            return back()->with('error', 'Ce patient est déjà dans votre liste.');
        }

        $doctor->patients()->attach($record->user_id);

        return back()->with('success', '✅ Patient ajouté à votre liste.');
    }

    // Retirer un patient de sa liste (ne supprime aucune donnée médicale)
    public function removePatient(Request $request, User $patient)
    {
        $request->user()->patients()->detach($patient->id);

        return redirect()->route('doctor.patients')->with('success', 'Patient retiré de votre liste.');
    }

    // Dossier complet d'un patient
    public function showPatient(Request $request, User $patient)
    {
        // Vérification centralisée via Policy + relation explicite doctor_patient
        $policy = app(PatientAccessPolicy::class);
        abort_unless(
            $policy->access($request->user(), $patient),
            403, 'Vous n\'avez pas accès à ce patient.'
        );

        $record = $patient->medicalRecord;
        $consultations = Consultation::where('user_id', $patient->id)
            ->with('doctor', 'prescriptions')
            ->latest('consultation_date')
            ->get();
        $prescriptions = $patient->prescriptions()->latest()->get();
        $vaccinations = $patient->vaccinations()->latest('administration_date')->get();

        return view('doctor.show-patient', compact(
            'patient', 'record', 'consultations', 'prescriptions', 'vaccinations'
        ));
    }

    // Modifier le dossier médical d'un patient suivi
    public function updateMedicalRecord(Request $request, User $patient)
    {
        abort_unless($request->user()->patients()->where('patient_id', $patient->id)->exists(), 403);

        $validated = $request->validate([
            'height_cm' => 'nullable|numeric',
            'weight_kg' => 'nullable|numeric',
            'chronic_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_treatments' => 'nullable|string',
            'family_history' => 'nullable|string',
            'surgical_history' => 'nullable|string',
        ]);

        $record = $patient->medicalRecord ?? $patient->medicalRecord()->create([
            'record_code' => 'SP-MA-' . strtoupper(Str::random(8)),
        ]);
        $record->update($validated);

        return back()->with('success', '✅ Dossier médical mis à jour');
    }

    // Créer une ordonnance directement (hors consultation)
    public function storePrescription(Request $request, User $patient)
    {
        abort_unless($request->user()->patients()->where('patient_id', $patient->id)->exists(), 403);

        $validated = $request->validate([
            'medication_name' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'instructions' => 'nullable|string',
        ]);

        Prescription::create([
            'user_id' => $patient->id,
            'doctor_id' => $request->user()->id,
            'medication_name' => $validated['medication_name'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'duration' => $validated['duration'],
            'instructions' => $validated['instructions'] ?? null,
            'start_date' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', '💊 Ordonnance créée pour ' . $patient->name);
    }

    // Créer un rappel pour un patient (ex: prise de médicament, prochain contrôle)
    public function storeReminder(Request $request, User $patient)
    {
        abort_unless($request->user()->patients()->where('patient_id', $patient->id)->exists(), 403);

        $validated = $request->validate([
            'type' => 'required|in:medication,appointment,vaccination,checkup',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_at' => 'required|date',
        ]);

        $patient->reminders()->create($validated);

        return back()->with('success', '⏰ Rappel créé pour ' . $patient->name);
    }

    // Formulaire nouvelle consultation
    public function createConsultation(Request $request, User $patient)
    {
        $record = $patient->medicalRecord;
        return view('consultations.create', compact('patient', 'record'));
    }

    // Enregistrer consultation + ordonnances
    public function storeConsultation(Request $request, User $patient)
    {
        $validated = $request->validate([
            'facility_name' => 'required|string',
            'facility_city' => 'required|string',
            'consultation_date' => 'required|date',
            'reason' => 'required|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'prescriptions' => 'nullable|array',
            'prescriptions.*.medication_name' => 'required_with:prescriptions.*|string',
            'prescriptions.*.dosage' => 'required_with:prescriptions.*|string',
            'prescriptions.*.frequency' => 'required_with:prescriptions.*|string',
            'prescriptions.*.duration' => 'required_with:prescriptions.*|string',
        ]);

        $doctor = $request->user();

        // Un médecin qui fait une consultation suit désormais ce patient
        if (!$doctor->patients()->where('patient_id', $patient->id)->exists()) {
            $doctor->patients()->attach($patient->id);
        }

        $record = $patient->medicalRecord;

        $consultation = Consultation::create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'medical_record_id' => $record->id,
            'facility_name' => $validated['facility_name'],
            'facility_city' => $validated['facility_city'],
            'consultation_date' => $validated['consultation_date'],
            'reason' => $validated['reason'],
            'diagnosis' => $validated['diagnosis'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'vital_signs' => $validated['vital_signs'] ?? null,
        ]);

        if (!empty($validated['prescriptions'])) {
            foreach ($validated['prescriptions'] as $rx) {
                if (!empty($rx['medication_name'])) {
                    Prescription::create([
                        'consultation_id' => $consultation->id,
                        'user_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'medication_name' => $rx['medication_name'],
                        'dosage' => $rx['dosage'],
                        'frequency' => $rx['frequency'],
                        'duration' => $rx['duration'],
                        'start_date' => $validated['consultation_date'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()
            ->route('doctor.show-patient', $patient)
            ->with('success', '✅ Consultation enregistrée');
    }
}
