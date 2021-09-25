<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieLog;
use App\Services\ModelNotFoundResponse;

class MovieLogController extends Controller
{
    use ModelNotFoundResponse;
    /**
     * Handle the incoming request.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MovieLog::with(['user:id,name', 'movie:id,title'])->paginate(request('perPage'));
    }

    public function show($id)
    {
        $movieLog = MovieLog::with(['user:id,name', 'movie:id,title'])->find($id);
        $this->errorModelJsonResponse($movieLog);
        return $movieLog;
    }
}
