<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'station_name',
        'status',
        'notes',
        'examiner_name',
        'examined_at',
    ];

    protected function casts(): array
    {
        return [
            'examined_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
