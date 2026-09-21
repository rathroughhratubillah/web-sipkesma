<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('test_session_id')->nullable()->constrained('test_sessions')->nullOnDelete();
            $table->enum('status', [
                'draft',
                'awaiting_payment',
                'awaiting_verification',
                'cleared',
                'checked_in',
                'in_progress',
                'completed'
            ])->default('draft');
            $table->string('queue_code')->nullable()->index();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
