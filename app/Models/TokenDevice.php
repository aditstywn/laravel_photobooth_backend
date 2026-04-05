<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenDevice extends Model
{
    protected $guarded = ['id'];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }
}
