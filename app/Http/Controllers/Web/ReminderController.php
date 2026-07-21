<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $reminders = $request->user()
            ->reminders()
            ->orderBy('due_at')
            ->get();

        return view('reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:medication,appointment,vaccination,checkup',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_at' => 'required|date',
        ]);

        $request->user()->reminders()->create($validated);
        return back()->with('success', '⏰ Rappel créé');
    }

    public function complete(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->findOrFail($id);
        $reminder->update(['is_completed' => true]);
        return back()->with('success', '✅ Rappel marqué comme fait');
    }
}