<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('blood_type', 5);
            $table->string('rhesus', 5)->default('+');
            $table->decimal('height_cm', 5, 2);
            $table->decimal('weight_kg', 5, 2);
            $table->text('allergies')->nullable();
            $table->text('chronic_diseases')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('past_surgeries')->nullable();
            $table->boolean('is_smoker')->default(false);
            $table->string('vaccine_status')->default('Lengkap (Booster)');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_histories');
    }
};
