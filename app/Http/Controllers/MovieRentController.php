<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRentRequest;
use App\Models\Movie;
use App\Models\Rent;
use App\Services\ModelNotFoundResponse;
use Carbon\Carbon;

class MovieRentController extends Controller
{
    use ModelNotFoundResponse;
    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\MovieRentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function newRentMovie(MovieRentRequest $request, $movie_id)
    {
        $attributes = $request->validated();
        $expiration_date = Carbon::parse($attributes['expiration_date']);
        $attributes['expiration_date'] = $expiration_date;

        $movie = Movie::find($movie_id);
        $this->errorModelJsonResponse($movie);

        if ($movie->stock < 1 || $movie->outOfStock($attributes['quantity'])) {
            $this->errorModelJsonResponse(null, [
                'error' => 'No stock available'
            ], 422);
        }

        $attributes['movie_id'] = $movie_id;
        $attributes['unit_rental_price'] = $movie->rental_price;
        $attributes['default_rate'] = $movie->default_rate;
        return Rent::newMovieRent($attributes);
    }

    /**
     * @param int $rent_id
     */
    public function returnMovie($rent_id)
    {
        $rent = Rent::with('movie')->find($rent_id);

        $rent->return_date = Carbon::now();
        $rent->calculateTotalFee();
        $rent->rented = false;
        $rent->save();
        return $rent->fresh();
    }
}
