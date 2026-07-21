# Santé Portable — Durcissement sécurité

Patch appliqué le 2026-07-17 pour corriger les points faibles identifiés lors
de l'audit de sécurité. Cible : données médicales sensibles (RGPD +性命).

## Résumé des changements

### Authentification (`app/Http/Controllers/Web/AuthController.php`)
- ❌ **AVANT** : `role` acceptait `doctor` en self-service (validation `in:patient,doctor`)
- ✅ **APRÈS** : `role` forcé à `patient` uniquement via validation `in:patient`
- ❌ **AVANT** : mot de passe `min:6` sans exigence de complexité
- ✅ **APRÈS** : `Password::min(8)->mixedCase()->numbers()` (8+ chars, maj+min+chiffre)
- ❌ **AVANT** : `Str::random(8)` sans retry sur collision
- ✅ **APRÈS** : `do { ... } while (collision)` dans helper `createMedicalRecordWithUniqueCode()`
- ✅ Logs `medical` sur tentative de rôle non-patient
- ✅ Vue `register` mise à jour : plus de `<select>` doctor, remplacé par champ hidden + texte explicatif

### Routes (`routes/web.php`)
- ❌ **AVANT** : aucune protection rate limit
- ✅ **APRÈS** :
  - Login + Register : `throttle:5,1` (5 tentatives/min)
  - Chatbot : `throttle:20,1`
  - Partage `/s/{token}` : `throttle:10,1`
  - Follow/unfollow doctor : `throttle:10,1`
  - Ajout patient : `throttle:10,1`

### Partage de dossier médical (`app/Models/ShareLink.php`, `SharedRecordController`, `ShareLinkController`)
- ❌ **AVANT** : token `Str::random(48)` (192 bits) — limite basse pour du médical
- ✅ **APRÈS** : `Str::random(64)` (256 bits)
- ❌ **AVANT** : IP stockée **en clair** en DB
- ✅ **APRÈS** : IP hashée via `hash('sha256', $ip . config('app.key'))` — non réversible
- ❌ **AVANT** : `findOrFail` distingue 404 (token inconnu) de vue "revoked/expired" (oracle attack)
- ✅ **APRÈS** : tous les cas → même vue générique "Lien invalide ou expiré" (anti-énumération)
- ❌ **AVANT** : pas de headers de sécurité sur la page de partage
- ✅ **APRÈS** : `Cache-Control: no-store`, `X-Content-Type-Options: nosniff`,
  `Referrer-Policy: no-referrer`, `X-Frame-Options: DENY`, `Pragma: no-cache`
- ❌ **AVANT** : `doctor_id` validé seulement sur `exists:users,id` (un user non-médecin pouvait être choisi)
- ✅ **APRÈS** : validation custom `function` qui vérifie `hasRole('doctor')`
- ✅ Audit log via channel `medical` à chaque accès

### Chatbot IA (`app/Http/Controllers/Web/ChatbotController.php`, `app/Services/OllamaService.php`)
- ❌ **AVANT** : contexte patient complet (chronic_conditions, allergies, current_treatments)
  envoyé en clair à Ollama → fuite de données si serveur Ollama partagé
- ✅ **APRÈS** : seules les métadonnées non-sensibles (âge, genre) sont envoyées.
  Le system prompt est codé en dur, pas d'extraction dynamique de champs sensibles.
- ❌ **AVANT** : détection d'urgence sur ~11 mots-clés (trop basique, faux positifs)
- ✅ **APRÈS** : ~40 patterns multilingues (FR/EN), groupés par catégorie :
  cardiaque, respiratoire, neurologique, hémorragie, trauma, psy/overdose.
  Renvoie 15 (SAMU Maroc) + 112 (urgence internationale).
- ✅ Test unitaire : `"j'ai mal au coeur je suis stressé"` (anxiété) ne déclenche PAS urgent.
- ✅ System prompt inclut une défense contre le prompt injection :
  refuse de se laisser reprogrammer.
- ✅ OllamaService : timeout configurable via `OLLAMA_TIMEOUT` (60s par défaut).
- ✅ Log des erreurs Ollama via channel `medical` (sans contenu du message).
- ✅ Cache des réponses génériques pendant 1h (pas les réponses personnalisées).
- ✅ Message user-friendly si Ollama injoignable.

### Médecin → Patient (`app/Http/Controllers/Web/DoctorDirectoryController.php`, `DoctorController.php`)
- ❌ **AVANT** : pas de check self-follow (un patient pouvait se follow lui-même)
- ✅ **APRÈS** : `abort_if($doctor->id === $request->user()->id, 400, 'Action impossible.')`
- ❌ **AVANT** : pas de check self-add-patient côté médecin
- ✅ **APRÈS** : `abort_if($record->user_id === $doctor->id, 400)` dans `addPatient`
- ✅ Nouvelle `App\Policies\PatientAccessPolicy` pour centraliser les règles
  d'accès médecin→patient (relation `doctor_patient` OU consultation existante).
  Utilisée dans `DoctorController::showPatient`.

### Dashboard (`app/Http/Controllers/Web/DashboardController.php`)
- ❌ **AVANT** : `patientsCount` comptait via `Consultation::distinct(user_id)` (incohérent
  avec la relation `belongsToMany patients()` utilisée partout ailleurs)
- ✅ **APRÈS** : `$user->patients()->count()` (source unique, cohérente)

### Configuration (`config/logging.php`, `.env.example`)
- ✅ Nouveau channel `medical` (driver `daily`, fichier `storage/logs/medical.log`,
  rétention 90 jours, niveau `info`).
- ✅ Variables env documentées : `OLLAMA_TIMEOUT`, `LOG_LEVEL_MEDICAL`, `LOG_MEDICAL_DAYS`.

### Vues (`resources/views/auth/register.blade.php`, `resources/views/share/invalid.blade.php`)
- ✅ register : champ hidden `role=patient` + texte explicatif pour les médecins
  (lien mailto pro@sante-portable.ma)
- ✅ share/invalid : message GÉNÉRIQUE quel que soit le cas (révoqué, expiré, inconnu)
  + meta tags anti-cache/anti-referrer

### Migration (`database/migrations/2026_07_17_120000_widen_share_link_ip_column.php`)
- ✅ Élargit `share_links.last_accessed_ip` de 45 à 64 chars (pour stocker le hash sha256).
  Cross-DB (MySQL/MariaDB/SQLite/PostgreSQL), SQL brut pour éviter doctrine/dbal.

### Tests (`tests/Feature/HardeningTest.php`)
Nouveau fichier de tests ciblés sur les durcissements :
- `test_register_rejects_doctor_role` — l'inscription refuse role=doctor
- `test_register_as_patient_creates_medical_record` — flux nominal
- `test_register_rejects_weak_password` — mot de passe < 8 / sans chiffre
- `test_share_link_token_is_64_chars` — entropie du token
- `test_share_link_invalid_token_returns_generic_error` — anti-énumération
- `test_share_ip_is_hashed_in_db` — l'IP n'est jamais en clair
- `test_chatbot_urgent_message_returns_samou_15` — urgence FR → SAMU 15
- `test_chatbot_urgent_keyword_in_english_too` — urgence EN détectée
- `test_chatbot_detects_suicide_ideation` — détresse psy
- `test_chatbot_anxiety_does_not_trigger_urgent` — anti faux-positif
- `test_chatbot_controller_does_not_leak_sensitive_fields_to_ollama` — RGPD
- `test_patient_cannot_follow_self` — anti self-follow
- `test_medical_log_channel_is_configured` — config logging

## Comment lancer les tests

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan test --filter=HardeningTest
```

## Points laissés en TODO (hors scope de ce patch)

1. **Onboarding médecin** : créer une route `register.doctor` accessible uniquement
   sur invitation admin (avec token d'invitation à usage unique).
2. **Soft delete + droit à l'oubli RGPD** : ajouter `SoftDeletes` aux modèles
   médicaux + endpoint `DELETE /account` qui anonymise.
3. **MFA / 2FA** : TOTP pour les comptes médecin (obligatoire pour conformité).
4. **Chiffrement at-rest des champs médicaux** : `encrypted` cast sur
   `chronic_conditions`, `allergies`, etc. (déjà préparé dans le code, à activer).
5. **Granularité des liens de partage** : exposer seulement les consultations
   créées pendant la période d'activité du lien (actuellement tout l'historique).
6. **CSP header** : ajouter `Content-Security-Policy` sur toutes les pages.

## Fichiers modifiés

```
app/Http/Controllers/Web/AuthController.php        [modifié]
app/Http/Controllers/Web/ChatbotController.php     [modifié]
app/Http/Controllers/Web/DashboardController.php   [modifié]
app/Http/Controllers/Web/DoctorController.php       [modifié]
app/Http/Controllers/Web/DoctorDirectoryController.php [modifié]
app/Http/Controllers/Web/ShareLinkController.php   [modifié]
app/Http/Controllers/Web/SharedRecordController.php [modifié]
app/Models/ShareLink.php                            [modifié]
app/Policies/PatientAccessPolicy.php                [nouveau]
app/Providers/AppServiceProvider.php                [modifié]
app/Services/OllamaService.php                      [modifié]
resources/views/auth/register.blade.php             [modifié]
resources/views/share/invalid.blade.php             [modifié]
routes/web.php                                      [modifié]
config/logging.php                                  [modifié]
.env.example                                        [modifié]
.env                                                [modifié]
database/migrations/2026_07_17_120000_widen_share_link_ip_column.php [nouveau]
tests/Feature/HardeningTest.php                     [nouveau]
SECURITY_HARDENING.md                               [nouveau — ce fichier]
```
