<?php

namespace App\Models;

use App\Services\MovieScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Movie extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'title',
        'description',
        'stock',
        'rental_price',
        'sale_price',
        'availability',
    ];

    protected $casts = [
        'stock' => 'integer',
        'rental_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'availability' => 'boolean'
    ];

    protected $append = [
        'likes_count'
    ];

    /**
     * all images movie
     * @return Array images
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * with movie_likes table relation
     */
    public function likes()
    {
        return $this->hasMany(MovieLike::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    public function outOfStock($quantity)
    {
        return ($this->stock - $quantity) < 0;
    }

    public static function withImage($movie_id, $image_id)
    {
        return self::with(['images' => function ($query) use ($image_id) {
            $query->where(['id' => $image_id]);
        }])->find($movie_id);
    }

    public function scopeAvailableMovies($query)
    {
        return $query->where('availability', true);
    }

    public static function createMovie($attributes)
    {
        $new_movie = self::create($attributes);
        return $new_movie->fresh();
    }

    public static function updateMovie($attributes, $id)
    {
        $movie = self::find($id);

        if (!$movie) return null;
        $movie->update($attributes);
        $movie->fresh();
        return $movie;
    }

    public static function deleteMovie($id)
    {
        $movie = self::find($id);
        if (!$movie) {
        }

        return $movie->delete();
    }

    protected static function booted()
    {
        static::addGlobalScope(new MovieScope);
        static::created(function ($movie) {
            MovieLog::saveLog($movie);
        });
        static::updated(function ($movie) {
            MovieLog::saveLog($movie);
        });
    }

    public static function withLikes()
    {
        return DB::select('movies_with_likes');
    }
}
