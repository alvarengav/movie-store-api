<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'url'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function getUrlAttribute($path)
    {
        return url("storage/" . $path);
    }
    public static function createImage($attributes)
    {
        $image = self::create($attributes);
        return $image;
    }
}
