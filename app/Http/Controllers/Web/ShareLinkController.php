<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Http\Request;

class ShareLinkController extends Controller
{
    public function index(Request $request)
    {
        $links = $request->user()->shareLinks()
            ->with('doctor')
            ->latest()
            ->get();

        $doctors = User::role('doctor')->orderBy('name')->get(['id', 'name', 'city']);

        return view('share.index', compact('links', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'duration' => 'required|in:1h,24h,7d',
            'doctor_id' => [
                'nullable',
                'exists:users,id',
                function (string $attribute, $value, \Closure $fail) {
                    if ($value !== null) {
                        $user = User::find($value);
                        if (!$user || !$user->hasRole('doctor')) {
                            $fail("L'utilisateur sélectionné n'a pas le rôle médecin.");
                        }
                    }
                },
            ],
            'notes' => 'nullable|string|max:255',
        ]);

        $link = ShareLink::createFor(
            $request->user(),
            $validated['duration'],
            $validated['doctor_id'] ?? null,
            $validated['notes'] ?? null
        );

        return redirect()
            ->route('share.index')
            ->with('success', '🔗 Lien généré !');
    }

    public function revoke(Request $request, ShareLink $shareLink)
    {
        abort_unless($shareLink->user_id === $request->user()->id, 403);
        $shareLink->update(['is_revoked' => true]);
        return back()->with('success', 'Lien révoqué');
    }
}
