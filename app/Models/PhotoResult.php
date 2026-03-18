<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoResult extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoResultFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['photo_ori_url'];

    protected function photoOriUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo_ori
                ? asset('storage/' . $this->photo_ori)
                : null,
        );
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
