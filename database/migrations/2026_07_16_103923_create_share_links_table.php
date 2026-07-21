<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('token', 64)->unique();
            $table->enum('duration', ['1h', '24h', '7d'])->default('24h');
            $table->timestamp('expires_at');
            $table->integer('access_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->string('last_accessed_ip', 45)->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['token', 'is_revoked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_links');
    }
};