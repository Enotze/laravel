<?php


namespace App\Services\Auth;


use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterService
{
    public function register(RegisterRequest $request)
    {
        $user = User::register(
            $request['name'],
            $request['email'],
            $request['password']
        );
        event(new Registered($user));
    }
}
