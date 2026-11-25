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
        'academic_year',
        'status',
        'queue_code',
        'checked_in_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'academic_year' => 'integer',
            'checked_in_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($registration) {
            if (!$registration->academic_year) {
                $registration->academic_year = (int) date('Y');
            }
        });
    }

    /**
     * Get list of academic years for filter dropdown.
     * Always includes: current year + next year (auto from calendar)
     * + all years that already have data in DB (for historical data).
     * Admin never needs to manually add years — it's fully automatic.
     */
    public static function getAvailableYears(): array
    {
        $currentYear = (int) date('Y');

        // Pull years that already have real data
        $dbYears    = static::distinct()->pluck('academic_year')->filter()->map(fn($y) => (int)$y)->toArray();
        $sessYears  = TestSession::distinct()->pluck('academic_year')->filter()->map(fn($y) => (int)$y)->toArray();

        // Always include current year + next year so dropdown is ready before data exists
        $autoYears = [$currentYear, $currentYear + 1];

        $allYears = array_unique(array_merge($dbYears, $sessYears, $autoYears));
        rsort($allYears);
        return array_values($allYears);
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
     * Canonical 1D ticket barcode payload (matches printed & web ticket).
     */
    public function getTicketBarcodeCodeAttribute(): string
    {
        $year = $this->academic_year ?? date('Y');
        $queueOrId = $this->queue_code ?: ('REG-' . $this->id);
        return "SPK-{$year}-PPKMB-{$queueOrId}";
    }

    /**
     * Find a registration from any ticket barcode, QR payload, queue code, or NIM/NISN.
     */
    public static function findByScanCode(string $input): ?self
    {
        $code = trim($input);
        if ($code === '') {
            return null;
        }

        $relations = ['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults'];

        // 1. Standardized Ticket Barcode: SPK-{YEAR}-PPKMB-{QUEUE_OR_REG}
        if (preg_match('/^SPK-(\d{4})-PPKMB-(.+)$/i', $code, $matches)) {
            $year = (int) $matches[1];
            $tail = strtoupper(trim($matches[2]));

            if (preg_match('/^REG-(\d+)$/i', $tail, $regMatch)) {
                return static::with($relations)->find((int) $regMatch[1]);
            }

            $matchedWithYear = static::with($relations)
                ->where('queue_code', $tail)
                ->where('academic_year', $year)
                ->first();

            if ($matchedWithYear) {
                return $matchedWithYear;
            }

            return static::with($relations)->where('queue_code', $tail)->first();
        }

        // 2. Legacy QR Payload: SIPKESMA:{id}:{queue}:{nim}
        if (str_starts_with(strtoupper($code), 'SIPKESMA:')) {
            $parts = explode(':', $code);
            $regId = $parts[1] ?? null;
            if ($regId && is_numeric($regId)) {
                return static::with($relations)->find((int) $regId);
            }
        }

        // 3. Direct Queue Code (e.g., A-001 or 2025-A-001)
        $byQueue = static::with($relations)
            ->where('queue_code', strtoupper($code))
            ->first();
        if ($byQueue) {
            return $byQueue;
        }

        // 4. Lookup by NIM / NISN
        $profile = Profile::where('nim', $code)->first();
        if ($profile) {
            return static::with($relations)
                ->where('user_id', $profile->user_id)
                ->first();
        }

        return null;
    }

    /**
     * Generate sequential queue code safely with database locking.
     * Guarantees strict FIFO order and prevents collision even when multiple users register simultaneously.
     */
    public static function generateNextQueueCode(?int $sessionId = null): string
    {
        $existingCodes = self::whereNotNull('queue_code')
            ->lockForUpdate()
            ->pluck('queue_code')
            ->toArray();

        $maxNumber = 0;
        foreach ($existingCodes as $code) {
            if (preg_match('/^[A-Z]-(\d+)$/', $code, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;
        return 'A-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
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
