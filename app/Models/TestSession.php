<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_date',
        'session_name',
        'start_time',
        'end_time',
        'quota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'is_active' => 'boolean',
            'quota' => 'integer',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()
            ->whereIn('status', [
                'awaiting_payment',
                'awaiting_verification',
                'cleared',
                'checked_in',
                'in_progress',
                'completed'
            ])
            ->count();
    }

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->quota - $this->registered_count);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->remaining_quota <= 0;
    }
}
