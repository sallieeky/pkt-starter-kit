<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function accountPage(Request $request)
    {
        return Inertia::render('Account');
    }
}
