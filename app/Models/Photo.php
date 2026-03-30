<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    protected $appends = ['photo_template_url', 'gif_vidio_url'];

    protected function photoTemplateUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo_template
                ? asset('storage/' . $this->photo_template)
                : null,
        );
    }

    protected function gifVidioUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->gif_vidio
                ? asset('storage/' . $this->gif_vidio)
                : null,
        );
    }

    public function photoResults()
    {
        return $this->hasMany(PhotoResult::class);
    }
}
