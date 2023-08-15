<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Crypto extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'logo',
    ];

    protected $hidden = [
        'id',
        'created_at'
    ];

    public function offers(): HasMany {
        return $this->hasMany(Offer::class);
    }

    public function priceHistory(): HasMany {
        return $this->hasMany(CryptoPrice::class);
    }

    public function price() {
        return $this->priceHistory()->one()->ofMany('date', 'max')->first()->price;
    }
}
