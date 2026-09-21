<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'amount',
        'proof_file_path',
        'status',
        'bank_name',
        'sender_name',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'verified_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
