<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_session_id',
        'status',
        'queue_code',
        'checked_in_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function testSession(): BelongsTo
    {
        return $this->belongsTo(TestSession::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function stationResults(): HasMany
    {
        return $this->hasMany(StationResult::class);
    }

    public function getStationResult(string $stationName): ?StationResult
    {
        return $this->stationResults->firstWhere('station_name', $stationName);
    }

    public function isAllStationsPassed(): bool
    {
        $stations = ['urin', 'napza', 'pemeriksaan'];
        $results = $this->stationResults->whereIn('station_name', $stations);

        if ($results->count() < 3) {
            return false;
        }

        foreach ($results as $result) {
            if ($result->status !== 'pass') {
                return false;
            }
        }

        return true;
    }

    public function getQrCodePayloadAttribute(): string
    {
        return 'SIPKESMA:' . $this->id . ':' . ($this->queue_code ?? 'TKT') . ':' . ($this->user->profile->nim ?? 'NIM');
    }

    /**
     * Determine active stage in 11-step workflow
     * 1: Registrasi & Login
     * 2: Lengkapi Data Diri
     * 3: Riwayat Kesehatan
     * 4: Pilih Jadwal Tes
     * 5: Konfirmasi Daftar
     * 6: Bayar
     * 7: Verifikasi Bayar
     * 8: Nomor Urut Tes
     * 9: Check-in Kampus
     * 10: Tes & Pemeriksaan
     * 11: Hasil & Sertifikat
     */
    public function getCurrentStepNumberAttribute(): int
    {
        $hasProfile = $this->user && $this->user->profile;
        if (!$hasProfile) {
            return 2;
        }

        $hasHealth = $this->user && $this->user->healthHistory;
        if (!$hasHealth) {
            return 3;
        }

        if (!$this->test_session_id) {
            return 4;
        }

        if ($this->status === 'draft') {
            return 5;
        }

        if ($this->status === 'awaiting_payment') {
            return 6;
        }

        if ($this->status === 'awaiting_verification') {
            return 7;
        }

        if ($this->status === 'cleared') {
            return 8;
        }

        if ($this->status === 'checked_in') {
            return 10;
        }

        if ($this->status === 'in_progress') {
            return 10;
        }

        if ($this->status === 'completed') {
            return 11;
        }

        return 2;
    }
}
