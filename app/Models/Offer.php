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
        'user_id',
    ];

    protected $hidden = [
        'user_id',
        'crypto_id'
    ];

    public function crypto(): BelongsTo {
        return $this->belongsTo(Crypto::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
