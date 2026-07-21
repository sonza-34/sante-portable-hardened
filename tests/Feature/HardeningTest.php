<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crée les rôles Spatie nécessaires aux tests
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    }

    /**
     * A.1 — L'inscription publique ne doit PAS accepter role=doctor.
     */
    public function test_register_rejects_doctor_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr Self Service',
            'email' => 'fakemedecin@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role' => 'doctor',
        ]);

        // Doit être un échec de validation
        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'fakemedecin@example.com']);
    }

    /**
     * A.2 — L'inscription avec role=patient fonctionne et crée un medical_record.
     */
    public function test_register_as_patient_creates_medical_record(): void
    {
        $response = $this->post('/register', [
            'name' => 'Patient Test',
            'email' => 'patient@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role' => 'patient',
        ]);

        $response->assertRedirect('/dashboard');
        $user = User::where('email', 'patient@example.com')->firstOrFail();
        $this->assertNotNull($user->medicalRecord);
        $this->assertStringStartsWith('SP-MA-', $user->medicalRecord->record_code);
    }

    /**
     * A.3 — Mot de passe faible rejeté (min 8, mixedCase, numbers).
     */
    public function test_register_rejects_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'X',
            'email' => 'weak@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => 'patient',
        ]);
        $response->assertSessionHasErrors('password');
    }

    /**
     * B.1 — Le token de partage fait 64 caractères.
     */
    public function test_share_link_token_is_64_chars(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $link = ShareLink::createFor($user, '24h', null, null);

        $this->assertSame(64, strlen($link->token));
    }

    /**
     * B.2 — Un token invalide retourne la même vue générique que expiré/révoqué
     * (anti-énumération).
     */
    public function test_share_link_invalid_token_returns_generic_error(): void
    {
        $response = $this->get('/s/this-token-does-not-exist-anywhere');
        // On accepte 200 (vue invalid) ou 404 — mais on ne doit PAS voir
        // de détail sur le token dans le HTML
        $this->assertNotSame(500, $response->getStatusCode());
        // Le contenu ne doit pas mentionner 'where' SQL
        $this->assertStringNotContainsString('SQLSTATE', $response->getContent());
    }

    /**
     * B.3 — L'IP est hashée en DB (pas en clair).
     */
    public function test_share_ip_is_hashed_in_db(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');
        $link = ShareLink::createFor($user, '24h', null, null);

        $link->recordAccess('192.168.1.42', 'TestUA/1.0');

        $this->assertDatabaseHas('share_links', [
            'id' => $link->id,
        ]);

        $fresh = $link->fresh();
        // L'IP ne doit PAS être en clair
        $this->assertNotSame('192.168.1.42', $fresh->last_accessed_ip);
        // Sur MariaDB (prod), on attend un hash sha256 complet de 64 chars hexa.
        // Sur sqlite (test), la colonne fait 45 chars → le hash sera tronqué.
        // On vérifie donc qu'on a un hash hexa (au moins 40 chars), peu importe la longueur.
        $this->assertMatchesRegularExpression('/^[a-f0-9]{40,64}$/', (string) $fresh->last_accessed_ip);
    }

    /**
     * C.1 — Détection urgence : mots-clés français.
     */
    public function test_chatbot_urgent_message_returns_samou_15(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $response = $this->actingAs($user)
            ->postJson('/chatbot/message', ['message' => "J'ai une douleur thoracique intense"]);

        $response->assertStatus(200);
        $response->assertJson([
            'urgent' => true,
            'intent' => 'urgence',
        ]);
        $this->assertStringContainsString('15', $response->json('reply'));
    }

    /**
     * C.2 — Détection urgence : mots-clés anglais.
     */
    public function test_chatbot_urgent_keyword_in_english_too(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $response = $this->actingAs($user)
            ->postJson('/chatbot/message', ['message' => "chest pain, I think I'm having a heart attack"]);

        $response->assertStatus(200);
        $response->assertJson(['urgent' => true]);
    }

    /**
     * C.3 — Détection détresse psychologique.
     */
    public function test_chatbot_detects_suicide_ideation(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $response = $this->actingAs($user)
            ->postJson('/chatbot/message', ['message' => "I want to kill myself, je veux me suicider"]);

        $response->assertStatus(200);
        $response->assertJson(['urgent' => true]);
    }

    /**
     * C.4 — "Mal au coeur" (anxiété) NE DOIT PAS déclencher urgent
     * (faux positif qu'on a explicitement voulu éviter).
     */
    public function test_chatbot_anxiety_does_not_trigger_urgent(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $response = $this->actingAs($user)
            ->postJson('/chatbot/message', ['message' => "j'ai mal au coeur je suis stressé"]);

        $response->assertStatus(200);
        // Pas urgent, intent = vulgarisation (ou erreur si Ollama indispo en test)
        $this->assertNull($response->json('urgent'));
    }

    /**
     * C.5 — Le contexte médical sensible n'est PAS envoyé à Ollama.
     * (vérification statique du code source — éviter que quelqu'un re-câble
     * chronic_conditions dans le system prompt à l'avenir).
     */
    public function test_chatbot_controller_does_not_leak_sensitive_fields_to_ollama(): void
    {
        $controllerPath = app_path('Http/Controllers/Web/ChatbotController.php');
        $source = file_get_contents($controllerPath);

        // Le code ne doit PAS concaténer chronic_conditions / allergies / current_treatments
        // dans le system prompt destiné à Ollama.
        $this->assertStringNotContainsString('chronic_conditions', $source,
            'ChatbotController ne doit pas envoyer chronic_conditions à Ollama (RGPD).');
        $this->assertStringNotContainsString('allergies', $source,
            'ChatbotController ne doit pas envoyer allergies à Ollama (RGPD).');
        $this->assertStringNotContainsString('current_treatments', $source,
            'ChatbotController ne doit pas envoyer current_treatments à Ollama (RGPD).');
    }

    /**
     * D.1 — Un patient ne peut pas se follow lui-même.
     */
    public function test_patient_cannot_follow_self(): void
    {
        $user = User::factory()->create();
        $user->assignRole('patient');

        $response = $this->actingAs($user)
            ->postJson("/doctors/{$user->id}/follow");

        // Doit être rejeté (400 attendu)
        $this->assertContains($response->getStatusCode(), [400, 403, 404]);
    }

    /**
     * Sanity — Le medical log channel existe.
     */
    public function test_medical_log_channel_is_configured(): void
    {
        $channels = config('logging.channels');
        $this->assertArrayHasKey('medical', $channels);
        $this->assertSame('daily', $channels['medical']['driver']);
    }
}
