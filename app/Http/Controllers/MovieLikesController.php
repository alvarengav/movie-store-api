<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieLikeRequest;
use App\Models\Movie;
use App\Models\User;
use App\Services\ModelNotFoundResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MovieLikesController extends Controller
{
    use ModelNotFoundResponse;

    public function __construct()
    {
        $this->middleware('auth:api')->only('store');
    }
    /**
     * Display a listing of the resource.
     *@param int $movie_id
     * @return \Illuminate\Http\Response
     */
    public function index($movie_id)
    {
        $movie = Movie::with('likes')->find($movie_id);
        $this->errorModelJsonResponse($movie);

        return ['likes' => $movie->likes->count()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $movie_id)
    {
        $user = $request->user();
        $movie = $user->movieLikes->find($movie_id);
        $this->errorModelJsonResponse(!$movie, [
            'error' => 'User already liked.'
        ], 422);
        $movieToLike = Movie::find($movie_id);
        $this->errorModelJsonResponse($movieToLike);
        try {
            return $movieToLike->likes()->create(['user_id' => $user->id]);
        } catch (ModelNotFoundException $th) {
            $this->errorModelJsonResponse(null, ['error' => 'Resources not found']);
        }
    }
}
