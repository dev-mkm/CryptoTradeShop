<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'amount',
        'cryptovalue',
    ];

    public function crypto(): BelongsTo {
        return $this->belongsTo(Crypto::class, 'crypto');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'userTrades')->withPivot('role');
    }
}
