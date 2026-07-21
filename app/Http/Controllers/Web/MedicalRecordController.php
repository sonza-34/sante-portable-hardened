<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $record = $user->medicalRecord()->firstOrCreate(
            ['user_id' => $user->id],
            ['record_code' => 'SP-MA-' . strtoupper(\Illuminate\Support\Str::random(8))]
        );
        return view('medical-record.show', compact('user', 'record'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'height_cm' => 'nullable|numeric',
            'weight_kg' => 'nullable|numeric',
            'chronic_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_treatments' => 'nullable|string',
            'family_history' => 'nullable|string',
            'surgical_history' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'insurance_provider' => 'nullable|string',
            'insurance_number' => 'nullable|string',
        ]);

        $record = $request->user()->medicalRecord;
        $record->update($validated);

        return back()->with('success', '✅ Dossier médical mis à jour');
    }
}