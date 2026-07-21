<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VaccinationController extends Controller
{
    public function index(Request $request)
    {
        $vaccinations = $request->user()
            ->vaccinations()
            ->orderBy('administration_date', 'desc')
            ->get();

        return view('vaccinations.index', compact('vaccinations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaccine_name' => 'required|string',
            'administration_date' => 'required|date',
            'next_dose_date' => 'nullable|date',
            'batch_number' => 'nullable|string',
            'administered_by' => 'nullable|string',
            'facility_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $request->user()->vaccinations()->create($validated);
        return back()->with('success', '💉 Vaccin ajouté');
    }
}