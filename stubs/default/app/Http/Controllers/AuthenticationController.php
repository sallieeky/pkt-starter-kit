<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Providers\RouteServiceProvider; // DEPRECATED IN LARAVEL 11
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticationController extends Controller
{
    public function loginPage(Request $request){
        return Inertia::render('Login');
    }

    public function login(LoginRequest $request){
        try {
            $request->authenticate();
            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::HOME); // DEPRECATED IN LARAVEL 11
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
