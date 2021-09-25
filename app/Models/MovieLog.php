<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MovieLog extends Model
{
    use HasFactory;

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function saveLog($movie)
    {
        $movieLog = new MovieLog();
        $movieLog->user_id = Auth::user()->id;
        $movieLog->movie_id = $movie->id;
        $movieLog->movie_title = $movie->title;
        $movieLog->movie_rental_price = $movie->rental_price;
        $movieLog->movie_sale_price = $movie->sale_price;
        $movieLog->save();
    }
}
