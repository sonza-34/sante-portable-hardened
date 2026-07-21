<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShareLink;
use Illuminate\Http\Request;

class SharedRecordController extends Controller
{
    /**
     * Affiche un dossier partagé via token.
     *
     * NOTE SÉCURITÉ (anti-énumération) : on ne distingue PAS entre "token inconnu",
     * "révoqué" et "expiré". Tous les cas → même vue générique. Sinon un attaquant
     * pourrait distinguer les tokens valides (200) des invalides (404) et brute-forcer.
     *
     * NOTE RGPD (granularité) : pour ce patch, on expose l'ensemble de l'historique
     * du patient pendant la durée de validité du lien. Une évolution future pourra
     * filtrer par date (ex: seulement depuis création du lien).
     */
    public function show(Request $request, string $token)
    {
        $link = ShareLink::where('token', $token)->first();

        // Cas invalide : token inconnu OU révoqué OU expiré → même réponse
        if (!$link || !$link->isValid()) {
            return $this->invalidResponse('invalide');
        }

        // Log de l'accès AVANT de servir le contenu (audit)
        $link->recordAccess($request->ip(), $request->userAgent());

        $patient = $link->user;
        $record = $patient->medicalRecord;
        $consultations = \App\Models\Consultation::where('user_id', $patient->id)
            ->with('doctor', 'prescriptions')
            ->latest('consultation_date')
            ->get();
        $prescriptions = $patient->prescriptions()->where('is_active', true)->get();
        $vaccinations = $patient->vaccinations()->latest('administration_date')->get();

        return response()
            ->view('share.show', compact(
                'link', 'patient', 'record', 'consultations', 'prescriptions', 'vaccinations'
            ))
            // Headers de sécurité pour données médicales sensibles
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
                'X-Content-Type-Options' => 'nosniff',
                'Referrer-Policy' => 'no-referrer',
                'X-Frame-Options' => 'DENY',
                'Pragma' => 'no-cache',
            ]);
    }

    /**
     * Réponse générique pour token invalide/révoqué/expiré.
     * Utilise la vue existante 'share.invalid' avec une raison générique.
     */
    private function invalidResponse(string $reason)
    {
        return response()
            ->view('share.invalid', ['reason' => 'invalide'])
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
                'X-Content-Type-Options' => 'nosniff',
                'Referrer-Policy' => 'no-referrer',
            ]);
    }
}
