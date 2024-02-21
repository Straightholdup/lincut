<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'original_url',
        'short_token',
        'is_private',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getShortUrlAttribute()
    {
        $baseUrl = config('app.url') . ':' . config('app.port');
        return rtrim($baseUrl, '/') . '/' . $this->short_token;
    }
}
