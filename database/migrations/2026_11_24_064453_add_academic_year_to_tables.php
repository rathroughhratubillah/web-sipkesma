<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('registrations', 'academic_year')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->unsignedSmallInteger('academic_year')->default(2026)->after('test_session_id')->index();
            });

            foreach (DB::table('registrations')->get() as $reg) {
                $year = $reg->created_at ? (int) substr($reg->created_at, 0, 4) : 2026;
                DB::table('registrations')->where('id', $reg->id)->update(['academic_year' => $year ?: 2026]);
            }
        }

        if (!Schema::hasColumn('test_sessions', 'academic_year')) {
            Schema::table('test_sessions', function (Blueprint $table) {
                $table->unsignedSmallInteger('academic_year')->default(2026)->after('session_name')->index();
            });

            foreach (DB::table('test_sessions')->get() as $sess) {
                $year = $sess->session_date ? (int) substr($sess->session_date, 0, 4) : 2026;
                DB::table('test_sessions')->where('id', $sess->id)->update(['academic_year' => $year ?: 2026]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('registrations', 'academic_year')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropColumn('academic_year');
            });
        }

        if (Schema::hasColumn('test_sessions', 'academic_year')) {
            Schema::table('test_sessions', function (Blueprint $table) {
                $table->dropColumn('academic_year');
            });
        }
    }
};
