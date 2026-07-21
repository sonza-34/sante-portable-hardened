<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Vaccination;
use App\Models\Reminder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ============ ROLES ============
        $roles = ['patient', 'doctor', 'admin'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);
        }

        // ============ MÉDECINS ============
        $doctor1 = User::updateOrCreate(
            ['email' => 'fatima@sante.ma'],
            [
                'name' => 'Dr. Fatima Zahra Bennani',
                'password' => Hash::make('password'),
                'phone' => '+212 661 11 22 33',
                'city' => 'Casablanca',
                'gender' => 'female',
            ]
        );
        $doctor1->syncRoles(['doctor']);

        $doctor2 = User::updateOrCreate(
            ['email' => 'karim@sante.ma'],
            [
                'name' => 'Dr. Karim El Idrissi',
                'password' => Hash::make('password'),
                'phone' => '+212 662 44 55 66',
                'city' => 'Marrakech',
                'gender' => 'male',
            ]
        );
        $doctor2->syncRoles(['doctor']);

        // ============ PATIENT 1 : Mohammed (diabétique chronique) ============
        $patient1 = User::updateOrCreate(
            ['email' => 'patient@sante.ma'],
            [
                'name' => 'Mohammed El Amrani',
                'password' => Hash::make('password'),
                'phone' => '+212 663 77 88 99',
                'cin' => 'BK123456',
                'date_of_birth' => '1965-03-15',
                'gender' => 'male',
                'address' => 'Hay Riad, Rue 5, N°23',
                'city' => 'Marrakech',
                'blood_type' => 'O+',
            ]
        );
        $patient1->syncRoles(['patient']);

        $record1 = MedicalRecord::updateOrCreate(
            ['user_id' => $patient1->id],
            [
                'record_code' => 'SP-MA-DEMO001',
                'height_cm' => 175,
                'weight_kg' => 82,
                'chronic_conditions' => 'Diabète type 2 (diagnostiqué en 2018). Hypertension artérielle.',
                'allergies' => 'Pénicilline',
                'current_treatments' => 'Metformine 1000mg 2x/jour. Amlodipine 5mg 1x/jour.',
                'family_history' => 'Père diabétique type 2. Mère hypertendue.',
                'surgical_history' => 'Appendicectomie en 1990.',
                'emergency_contact_name' => 'Aicha El Amrani (épouse)',
                'emergency_contact_phone' => '+212 664 11 22 33',
                'insurance_provider' => 'CNOPS',
                'insurance_number' => 'CNOPS-789456',
            ]
        );

        // Consultations
        $consult1 = Consultation::create([
            'user_id' => $patient1->id,
            'doctor_id' => $doctor2->id,
            'medical_record_id' => $record1->id,
            'facility_name' => 'Clinique Atlas',
            'facility_city' => 'Marrakech',
            'consultation_date' => '2026-01-15',
            'reason' => 'Contrôle trimestriel diabète',
            'diagnosis' => 'Diabète type 2 équilibré. HTA stable.',
            'notes' => 'Glycémie à jeun: 1.30 g/L. HbA1c: 7.2%. TA: 135/80.',
            'vital_signs' => 'TA: 135/80, Glycémie: 1.30 g/L, Poids: 82kg',
        ]);

        $consult2 = Consultation::create([
            'user_id' => $patient1->id,
            'doctor_id' => $doctor1->id,
            'medical_record_id' => $record1->id,
            'facility_name' => 'Hôpital Ibn Rochd',
            'facility_city' => 'Casablanca',
            'consultation_date' => '2026-04-22',
            'reason' => 'Hospitalisation pour déséquilibre glycémique',
            'diagnosis' => 'Décompensation diabétique hyperglycémique.',
            'notes' => 'Patient transféré de Marrakech. Glycémie à 3.5 g/L à l\'admission.',
            'vital_signs' => 'TA: 150/90, Glycémie: 3.5 g/L',
        ]);

        // Ordonnances
        Prescription::create([
            'consultation_id' => $consult1->id,
            'user_id' => $patient1->id,
            'doctor_id' => $doctor2->id,
            'medication_name' => 'Metformine',
            'dosage' => '1000mg',
            'frequency' => '2x par jour (matin et soir)',
            'duration' => '3 mois',
            'instructions' => 'À prendre pendant les repas pour limiter les effets digestifs.',
            'start_date' => '2026-01-15',
            'is_active' => false,
        ]);

        Prescription::create([
            'consultation_id' => $consult1->id,
            'user_id' => $patient1->id,
            'doctor_id' => $doctor2->id,
            'medication_name' => 'Amlodipine',
            'dosage' => '5mg',
            'frequency' => '1x par jour (le matin)',
            'duration' => '6 mois',
            'instructions' => 'Surveillance tensionnelle régulière.',
            'start_date' => '2026-01-15',
            'is_active' => true,
        ]);

        Prescription::create([
            'consultation_id' => $consult2->id,
            'user_id' => $patient1->id,
            'doctor_id' => $doctor1->id,
            'medication_name' => 'Metformine',
            'dosage' => '1000mg',
            'frequency' => '2x par jour',
            'duration' => '6 mois',
            'instructions' => 'Reprise Metformine après sortie d\'hospitalisation.',
            'start_date' => '2026-04-25',
            'is_active' => true,
        ]);

        // Vaccinations
        Vaccination::create([
            'user_id' => $patient1->id,
            'vaccine_name' => 'Grippe saisonnière',
            'administration_date' => '2025-11-10',
            'batch_number' => 'GR-2025-7842',
            'administered_by' => 'Infirmier Youssef',
            'facility_name' => 'Centre de santé Hay Riad',
        ]);

        Vaccination::create([
            'user_id' => $patient1->id,
            'vaccine_name' => 'Tétanos-Diphtérie (rappel)',
            'administration_date' => '2024-06-15',
            'next_dose_date' => '2034-06-15',
            'batch_number' => 'TD-2024-9912',
            'administered_by' => 'Dr. Bennani',
            'facility_name' => 'Clinique Atlas',
        ]);

        // Rappels
        Reminder::create([
            'user_id' => $patient1->id,
            'type' => 'medication',
            'title' => 'Prendre Metformine 1000mg',
            'description' => 'Matin et soir pendant le repas',
            'due_at' => now()->addHours(2),
        ]);

        Reminder::create([
            'user_id' => $patient1->id,
            'type' => 'medication',
            'title' => 'Prendre Amlodipine 5mg',
            'description' => 'Le matin avec un verre d\'eau',
            'due_at' => now()->addHours(10),
        ]);

        Reminder::create([
            'user_id' => $patient1->id,
            'type' => 'checkup',
            'title' => 'Bilan glycémique trimestriel',
            'description' => 'Prise de sang à jeun + consultation Dr. Bennani',
            'due_at' => now()->addDays(14),
        ]);

        Reminder::create([
            'user_id' => $patient1->id,
            'type' => 'appointment',
            'title' => 'RDV Cardiologue — Dr. Bennani',
            'description' => 'Hôpital Ibn Rochd, étage 4, bureau 412',
            'due_at' => now()->addDays(30),
        ]);

        // ============ PATIENT 2 : Aicha ============
        $patient2 = User::updateOrCreate(
            ['email' => 'aicha@sante.ma'],
            [
                'name' => 'Aicha Benali',
                'password' => Hash::make('password'),
                'phone' => '+212 665 12 34 56',
                'date_of_birth' => '1990-08-22',
                'gender' => 'female',
                'city' => 'Casablanca',
                'blood_type' => 'A+',
            ]
        );
        $patient2->syncRoles(['patient']);

        MedicalRecord::updateOrCreate(
            ['user_id' => $patient2->id],
            [
                'record_code' => 'SP-MA-DEMO002',
                'height_cm' => 165,
                'weight_kg' => 62,
                'chronic_conditions' => 'Aucune',
                'allergies' => 'Pollen',
                'current_treatments' => 'Vitamines grossesse',
                'emergency_contact_name' => 'Hassan Benali (mari)',
                'emergency_contact_phone' => '+212 666 98 76 54',
            ]
        );

        // ============ ADMIN ============
        $admin = User::updateOrCreate(
            ['email' => 'admin@sante.ma'],
            [
                'name' => 'Admin Système',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        $this->command->info('✅ Comptes de démo créés avec succès !');
        $this->command->info('');
        $this->command->info('🔐 Identifiants :');
        $this->command->info('   Patient  : patient@sante.ma / password');
        $this->command->info('   Médecin  : fatima@sante.ma  / password');
        $this->command->info('   Admin    : admin@sante.ma   / password');
    }
}