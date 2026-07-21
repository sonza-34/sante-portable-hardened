<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->string('facility_name');
            $table->string('facility_city');
            $table->date('consultation_date');
            $table->text('reason');
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->text('vital_signs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};