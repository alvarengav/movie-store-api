<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ModelNotFoundResponse;
use Illuminate\Http\Request;

class UserMovieRentController extends Controller
{
    use ModelNotFoundResponse;

    public function moviesRent($user_id)
    {
        $user = User::with(['rents' => function ($query) {
            $query->rented();
        }])->find($user_id);
        $this->errorModelJsonResponse($user);
        return $user;
    }
}
