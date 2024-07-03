<?php

namespace Pkt\StarterKit\Macros;

class RouterMacro
{
    /**
     * Authenticated routes that require a user to be logged in.
     * 
     * @return callable
     */
    public function authenticated(): callable
    {
        return function () {
            $this->middleware(config('sso-session.ENABLE_SSO') ? ['SsoPortal'] : ['auth']);

            return $this;
        };
    }

    /**
     * Authorized routes that require a user to have a specific role.
     * 
     * @return callable
     */
    public function roles(): callable
    {
        return function ($roles) {
            if (is_array($roles)) {
                $roles = implode('|', $roles);
            } elseif ($roles instanceof \Illuminate\Support\Collection) {
                $roles = $roles->implode('|');
            }
            $this->middleware('role:' . $roles);

            return $this;
        };
    }
}