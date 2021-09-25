<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Models\Image;
use App\Models\Movie;
use App\Services\ModelNotFoundResponse;
use App\Services\MovieService;
use Illuminate\Http\Request;

class MovieImageController extends Controller
{
    use ModelNotFoundResponse;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *@param int $id - Movie id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $movie = Movie::with('images')->find($id);
        $this->errorModelJsonResponse($movie);
        return $movie->images;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param   \App\Http\Requests\ImageRequest  $request
     * @param int $id - Movie id
     * @return \Illuminate\Http\Response
     */
    public function store(ImageRequest $request, $id)
    {
        $request->validated();
        $movie = Movie::find($id);
        $this->errorModelJsonResponse($movie);

        try {
            $path = MovieService::uploadImage($request->file('image'));
            return Image::createImage([
                'movie_id' => $movie->id,
                'url' => $path
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'the resource cannot be created.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $movie_id
     * @param  int  $image_id
     * @return \Illuminate\Http\Response
     */
    public function show($movie_id, $image_id)
    {
        $movie = Movie::withImage($movie_id, $image_id);

        if (!$movie || $movie->images->isEmpty()) {
            $this->errorModelJsonResponse(null);
        }

        return $movie->images[0];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $movie_id
     * @param int $image_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($movie_id, $image_id)
    {
        $movie = Movie::withImage($movie_id, $image_id);

        if (!$movie || $image = $movie->images->isEmpty()) {
            return response()->json([
                'error' => 'Resource not found.'
            ], 404);
        }

        try {
            $image = $movie->images[0];
            MovieService::deleteImage($image->url);
            $deleted = $image->delete();

            $message = $deleted
                ? ['message' => 'Image deleted successfully', 'deleted' => $deleted]
                : ['message' => 'Could not delete the image.', 'deleted' => $deleted];
            return response()->json($message);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'error trying to delete image.'
            ], 500);
        }
    }
}
