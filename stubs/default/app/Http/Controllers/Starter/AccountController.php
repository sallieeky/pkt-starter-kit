<?php

namespace App\Http\Controllers\Starter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function accountPicture($npk) 
    {
        try {
            $request = Http::get("https://leader.pupukkaltim.com/public/foto-thumbnail/$npk.jpg");
            $picture = $request->body();
            $status = $request->status();
            if ($status !== 200) {
                $picture = file_get_contents(public_path('/images/avatar-default.png'));
            }
        } catch (\Exception $e) {
            $picture = file_get_contents(public_path('/images/avatar-default.png'));
        }
        return response($picture, 200, ['Content-Type' => 'image/jpeg']);
    }
    public function accountPage(Request $request)
    {
        return Inertia::render('Account');
    }
}
