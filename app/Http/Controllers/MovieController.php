<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use App\Services\AuthorizationGate;
use App\Services\FilterService;
use App\Services\ModelNotFoundResponse;

class MovieController extends Controller
{
    use ModelNotFoundResponse;
    use AuthorizationGate;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show', 'search']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $builder = Movie::withCount('likes')->with('images');

        $filter = FilterService::createFilter($builder);

        return $filter->defaultSort("sort", "title")
            ->sort("sort")
            ->filter('actives', '=', true, $this->authorizedAdmin())
            ->getBuilder()->paginate(request('perPage'));
    }

    public function search()
    {
        $builder = Movie::withCount('likes')->with('images');

        $filter = FilterService::createFilter($builder);

        return $filter->defaultSort("sort", "title")
            ->sort("sort")
            ->filter('actives', '=', true, $this->authorizedAdmin())
            ->search('title')
            ->getBuilder()->paginate(request('perPage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\MovieRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovieRequest $request)
    {
        $attributes = $request->validated();
        return Movie::createMovie($attributes);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Movie::find($id);
        $this->errorModelJsonResponse($movie);
        return $movie;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMovieRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMovieRequest $request, $id)
    {
        $attributes = $request->all();
        $updateMovie = Movie::updateMovie($attributes, $id);
        $this->errorModelJsonResponse($updateMovie);
        return $updateMovie;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movie = Movie::find($id);
        $this->errorModelJsonResponse($movie);
        return $movie->delete();
    }
}
