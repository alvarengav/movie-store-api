<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterUserController extends Controller
{
    public function __invoke(RegisterUserRequest $request)
    {
        $attributes = $request->validated();
        $attributes['role'] = 'customer';
        $user = User::createUser($attributes);
        event(new Registered($user));
        return ['access_token' => Auth::login($user)];
    }
}
