<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Providers\RouteServiceProvider; // DEPRECATED IN LARAVEL 11
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\SSOSession;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller
{
    public function loginPage(Request $request)
    {
        if (config('sso-session.ENABLE_SSO') && config('sso-session.DISABLE_INTERNAL_LOGIN')) {
            return $this->checkAccessSso();
        }
        return Inertia::render('Login');
    }

    public function login(LoginRequest $request)
    {
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (config('sso-session.ENABLE_SSO')) {
            $sessionId = Session::get('SESSION_ID');
            $ssoSession = SsoSession::where('SESSION_ID', $sessionId)->first();
            if ($ssoSession) {
                return redirect(config('sso-session.PORTAL_URL_LOGOUT'));
            }
            return redirect()->route('home');
        } else {
            return redirect()->route('home');
        }

        return redirect('/login');
    }

    private function checkAccessSso()
    {
        $applicationName = config('sso-session.APPLICATION_NAME');
        $loginPageUrl = config('sso-session.PORTAL_URL_LOGIN');
        $session_id = Session::get('SESSION_ID');
        if (!$session_id) {
            $session_id = Session::getId();
            Session::put('SESSION_ID', $session_id);
        }
        $sso_session = SSOSession::where('SESSION_ID', $session_id)->first();
        if (config('sso-session.SSO_FULL_FEATURE')) {
            if (!$sso_session) {
                $queryParam = '?application=' . $applicationName;
                $queryParam .= '&session=' . $session_id;
                return redirect($loginPageUrl . $queryParam);
            } else {
                return redirect()->route('home');
            }
        }
    }
}
