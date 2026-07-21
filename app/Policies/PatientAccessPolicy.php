<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy centralisée pour l'accès médecin → patient.
 *
 * Règle : un médecin peut accéder au dossier d'un patient ssi :
 *   - le médecin a effectivement ce patient dans sa liste doctor_patient
 *   (relation belongsToMany), OU
 *   - il existe au moins une consultation entre ce médecin et ce patient.
 *
 * Cette policy évite la duplication du check "abort_unless" partout dans les
 * controllers. À utiliser via `Gate::define` + `$this->authorize(...)` ou
 * directement via la facade.
 */
class PatientAccessPolicy
{
    use HandlesAuthorization;

    /**
     * Un médecin peut-il accéder au dossier complet de ce patient ?
     */
    public function access(User $doctor, User $patient): bool
    {
        if (!$doctor->hasRole('doctor')) {
            return false;
        }

        // 1) Le patient est-il dans la liste explicite du médecin ?
        if ($doctor->patients()->where('patient_id', $patient->id)->exists()) {
            return true;
        }

        // 2) Le médecin a-t-il déjà une consultation avec ce patient ?
        $hasConsultation = \App\Models\Consultation::where('doctor_id', $doctor->id)
            ->where('user_id', $patient->id)
            ->exists();

        return $hasConsultation;
    }

    /**
     * Le médecin peut-il modifier le dossier médical de ce patient ?
     * Mêmes règles que access() pour ce patch — futur: différentier read/write.
     */
    public function update(User $doctor, User $patient): bool
    {
        return $this->access($doctor, $patient);
    }

    /**
     * Le médecin peut-il ajouter une prescription à ce patient ?
     */
    public function prescribe(User $doctor, User $patient): bool
    {
        return $this->access($doctor, $patient);
    }

    /**
     * Le médecin peut-il ajouter un rappel pour ce patient ?
     */
    public function remind(User $doctor, User $patient): bool
    {
        return $this->access($doctor, $patient);
    }
}
