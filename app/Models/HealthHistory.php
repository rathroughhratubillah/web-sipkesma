<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_type',
        'rhesus',
        'height_cm',
        'weight_kg',
        'allergies',
        'chronic_diseases',
        'current_medications',
        'past_surgeries',
        'is_smoker',
        'vaccine_status',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'height_cm' => 'float',
            'weight_kg' => 'float',
            'is_smoker' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBmiAttribute(): ?float
    {
        if ($this->height_cm > 0 && $this->weight_kg > 0) {
            $heightM = $this->height_cm / 100;
            return round($this->weight_kg / ($heightM * $heightM), 1);
        }
        return null;
    }

    public function getBmiStatusAttribute(): string
    {
        $bmi = $this->bmi;
        if ($bmi === null) {
            return '-';
        }
        if ($bmi < 18.5) {
            return 'Kurus (Underweight)';
        } elseif ($bmi <= 22.9) {
            return 'Normal (Ideal)';
        } elseif ($bmi <= 24.9) {
            return 'Kelebihan Berat (Overweight)';
        } else {
            return 'Obesitas';
        }
    }
}
