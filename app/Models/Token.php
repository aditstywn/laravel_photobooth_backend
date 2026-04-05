<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $guarded = ['id'];

        protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function devices()
    {
        return $this->hasMany(TokenDevice::class);
    }
}
