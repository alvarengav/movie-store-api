<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieBuyRequest;
use App\Models\Movie;
use App\Models\Sale;
use App\Services\ModelNotFoundResponse;
use Illuminate\Support\Facades\Auth;

class MovieBuyController extends Controller
{
    use ModelNotFoundResponse;
    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\MovieBuyRequest  $request
     * @param int $movie_id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(MovieBuyRequest $request, $movie_id)
    {
        $attributes = $request->validated();
        $movie = Movie::find($movie_id);
        $this->errorModelJsonResponse($movie);

        if ($movie->stock < 1 || $movie->outOfStock($attributes['quantity'])) {
            $this->errorModelJsonResponse(null, [
                'error' => 'No stock available'
            ], 422);
        }

        $attributes['movie_id'] = $movie->id;
        $attributes['unit_price'] = $movie->sale_price;
        $attributes['user_id'] = Auth::user()->id;

        return Sale::newSale($attributes);
    }
}
