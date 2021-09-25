<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'user_id',
        'expiration_date',
        'return_date',
        'unit_rental_price',
        'late_fee',
        'quantity',
        'default_rate'
    ];

    protected $casts = [
        'expiration_date' =>  'datetime:m-d-Y',
        'return_date' => 'datetime:m-d-Y',
        'unit_rental_price' => 'decimal:2',
        'quantity' => 'integer',
        'late_fee' => 'decimal:2',
        'late_days' => 'integer',
        'default_rate' => 'float'
    ];

    protected $appends = ['late_days'];

    public function getLateDaysAttribute()
    {
        if ($this->expiration_date->lessThan($this->returned_date)) {
            return $this->expiration_date->diffInDays($this->return_date);
        }

        return 0;
    }

    public function calculateTotalFee()
    {
        if ($this->late_days !== 0) {
            $this->late_fee = $this->quantity * $this->unit_rental_price * $this->default_rate * $this->late_days;
        }
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRented($query)
    {
        return $query->where('rented', true);
    }

    public function scopeReturned($query)
    {
        return $query->where('rented', false);
    }

    public static function newMovieRent($attributes)
    {
        $rent = self::create($attributes);
        return $rent->fresh();
    }
}
