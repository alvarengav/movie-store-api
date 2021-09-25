<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovieLike extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function scopeLikeCount($query)
    {
        return $query->addSelect(DB::raw('count(*) as nLikes'));
    }
}
