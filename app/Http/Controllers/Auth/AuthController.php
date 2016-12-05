<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Socialite;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect(); 
    }

    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect('auth/'.$provider);
        }

        $authUser = $this->findOrCreateUser($user, $provider);

        Auth::login($authUser, true);

        return redirect('home');
    }

    private function findOrCreateUser($user, $provider)
    {
        if ($authUser = User::where($provider.'_id', $user->id)->first()) {
            return $authUser;
        }

        return User::create([
            'avatar' => $user->avatar,
            'name' => $user->name,
            'email' => $user->email,
            $provider.'_id' => $user->id,            
        ]);
    }
}
