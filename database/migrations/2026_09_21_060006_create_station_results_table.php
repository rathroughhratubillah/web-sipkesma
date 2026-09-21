<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->enum('station_name', ['urin', 'napza', 'pemeriksaan']);
            $table->enum('status', ['pass', 'followup', 'fail']);
            $table->text('notes')->nullable();
            $table->string('examiner_name')->nullable();
            $table->timestamp('examined_at');
            $table->timestamps();

            $table->unique(['registration_id', 'station_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_results');
    }
};
