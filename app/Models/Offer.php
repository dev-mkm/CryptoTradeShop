<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'amount',
        'selling',
    ];

    public function crypto(): BelongsTo {
        return $this->belongsTo(Crypto::class, 'crypto');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user');
    }
}
