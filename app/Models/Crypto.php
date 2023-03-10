<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crypto extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'logo',
        'price',
    ];

    protected $hidden = [
        'id',
        'created_at'
    ];

    public function offers(): HasMany {
        return $this->hasMany(Offer::class);
    }
}
