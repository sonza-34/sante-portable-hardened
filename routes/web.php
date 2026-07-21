<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MedicalRecordController;
use App\Http\Controllers\Web\PrescriptionController;
use App\Http\Controllers\Web\VaccinationController;
use App\Http\Controllers\Web\ReminderController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\DoctorController;
use App\Http\Controllers\Web\ShareLinkController;
use App\Http\Controllers\Web\SharedRecordController;
use App\Http\Controllers\Web\DoctorDirectoryController;

Route::get('/', function () { return view('welcome'); });

// Public : médecin invité via lien — throttle 10/min par IP (anti-énumération + brute force)
Route::get('/s/{token}', [SharedRecordController::class, 'show'])
    ->name('share.show')
    ->middleware('throttle:10,1');

Route::middleware('guest')->group(function () {
    // Throttle durci contre force brute : 5 tentatives/min par IP
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post')
        ->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post')
        ->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/medical-record', [MedicalRecordController::class, 'show'])->name('medical-record');
    Route::put('/medical-record', [MedicalRecordController::class, 'update'])->name('medical-record.update');

    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions');

    Route::get('/vaccinations', [VaccinationController::class, 'index'])->name('vaccinations');
    Route::post('/vaccinations', [VaccinationController::class, 'store'])->name('vaccinations.store');

    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::patch('/reminders/{reminder}/complete', [ReminderController::class, 'complete'])->name('reminders.complete');

    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    // Chatbot : 20 messages/min max (anti-abus + cost Ollama)
    Route::post('/chatbot/message', [ChatbotController::class, 'chat'])
        ->name('chatbot.message')
        ->middleware('throttle:20,1');
    Route::get('/chatbot/status', [ChatbotController::class, 'status'])->name('chatbot.status');

    Route::get('/share', [ShareLinkController::class, 'index'])->name('share.index');
    Route::post('/share', [ShareLinkController::class, 'store'])->name('share.store');
    Route::delete('/share/{shareLink}', [ShareLinkController::class, 'revoke'])->name('share.revoke');

    // Côté patient : choisir un médecin dans l'annuaire
    Route::get('/doctors', [DoctorDirectoryController::class, 'index'])->name('doctors.index');
    Route::post('/doctors/{doctor}/follow', [DoctorDirectoryController::class, 'follow'])
        ->name('doctors.follow')
        ->middleware('throttle:10,1');
    Route::delete('/doctors/{doctor}/unfollow', [DoctorDirectoryController::class, 'unfollow'])
        ->name('doctors.unfollow')
        ->middleware('throttle:10,1');
});

Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/patients', [DoctorController::class, 'patients'])->name('patients');
    Route::post('/patients', [DoctorController::class, 'addPatient'])
        ->name('add-patient')
        ->middleware('throttle:10,1');
    Route::delete('/patients/{patient}', [DoctorController::class, 'removePatient'])->name('remove-patient');
    Route::get('/patients/{patient}', [DoctorController::class, 'showPatient'])->name('show-patient');
    Route::put('/patients/{patient}/medical-record', [DoctorController::class, 'updateMedicalRecord'])->name('update-medical-record');
    Route::post('/patients/{patient}/prescription', [DoctorController::class, 'storePrescription'])->name('store-prescription');
    Route::post('/patients/{patient}/reminder', [DoctorController::class, 'storeReminder'])->name('store-reminder');
    Route::get('/patients/{patient}/consultation/new', [DoctorController::class, 'createConsultation'])->name('create-consultation');
    Route::post('/patients/{patient}/consultation', [DoctorController::class, 'storeConsultation'])->name('store-consultation');
});
