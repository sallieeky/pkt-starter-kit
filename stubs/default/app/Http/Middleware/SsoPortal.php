<?php

namespace App\Http\Middleware;

use App\Models\SSOSession;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class SsoPortal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $applicationName = config('sso-session.APPLICATION_NAME');
        $loginPageUrl = config('sso-session.PORTAL_URL_LOGIN');

        if (config('sso-session.ENABLE_SSO')) {
            $session_id = Session::get('SESSION_ID');
            if (!$session_id) {
                $session_id = session()->getId();
                Session::put('SESSION_ID', $session_id);
            }
            $sso_session = SSOSession::where('SESSION_ID', $session_id)->first();            
            if (config('sso-session.DISABLE_INTERNAL_LOGIN')) {
                if (config('sso-session.SSO_FULL_FEATURE')) {
                    if (!$sso_session) {
                        $queryParam = '?application=' . $applicationName;
                        $queryParam .= '&session=' . $session_id;
                        return redirect($loginPageUrl . $queryParam);
                    } else {
                        if (auth()->check()) {
                            return $next($request);
                        } else {
                            $aliases = explode(';', $sso_session->USER_ALIASES);
                            $user = User::whereIn('username', $aliases)->where('is_active', true)->first();
                            if ($user != null) {
                                Auth::login($user);
                                return $next($request);
                            } else {
                                $queryParam = '?application=' . $applicationName;
                                $queryParam .= '&error=404';
                                return redirect($loginPageUrl.$queryParam);
                            }
                        }
                    }
                }
            }
        }
        return $next($request);
    }
}
