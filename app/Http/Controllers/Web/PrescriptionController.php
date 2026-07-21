<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $prescriptions = $request->user()
            ->prescriptions()
            ->with('doctor')
            ->latest()
            ->get();

        return view('prescriptions.index', compact('prescriptions'));
    }
}