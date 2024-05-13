<?php

namespace App\Http\Requests;

use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        RateLimiter::clear($this->throttleKey());

        $credential = $this->only('username','password');

        $ldap = $this->ldapZimbraAuth($credential['username'], $credential['password']);
        if ($ldap) {
            foreach ($ldap['mail'] as $mail) {
                $user_name = explode('@', $mail)[0];
                $user = User::where('username', $user_name)->first();
                if ($user && $user->is_active) {
                    Auth::login($user);
                } else if ($user && !$user->is_active) {
                    throw new AuthenticationException('User does not have access');
                }
            }
        }else{
            if (! Auth::attempt(['username' => $credential['username'], 'password' => $credential['password'], 'is_active' => 1])) {
                RateLimiter::hit($this->throttleKey());

                throw new AuthenticationException('Incorect Username or Password');
            }
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw new AuthenticationException(trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]));
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('username')).'|'.$this->ip());
    }

    private function ldapZimbraAuth($username, $pass){
        $ldap['host'] = config('ldap.LDAP_HOST');
        $ldap['port'] = config('ldap.LDAP_PORT');
        $ldap['dn'] = config('ldap.LDAP_DN');
        $ldap['pass'] = config('ldap.LDAP_PASS');
        $ldap['tree'] = config('ldap.LDAP_TREE');
        $ldap['enable'] = config('ldap.LDAP_ENABLE');

        if (!$ldap['enable']) {
            return false;
        }

        $ldap['conn'] = ldap_connect($ldap['host'], $ldap['port']);
        ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap['conn'], LDAP_OPT_REFERRALS, 0);

        $ldap['bind'] = @ldap_bind($ldap['conn'], $ldap['dn'], $ldap['pass']);
        if ($ldap['bind']) {
            $username = strpos($username, '@pupukkaltim.com') ? $username : $username.'@pupukkaltim.com';

            $search_filter = "(mail=" . $username . ")";
            $result = @ldap_search($ldap['conn'],  $ldap['tree'], $search_filter, array('displayname', 'mail', 'uid', 'ou', 'sn', 'givenname'));
            $first_entry = @ldap_first_entry($ldap['conn'], $result);

            if ($first_entry) {
                $user_dn = @ldap_get_dn($ldap['conn'], $first_entry);
                $user_attributes = @ldap_get_attributes($ldap['conn'], $first_entry);
                if ($user_dn) {
                    $bind_login_user = @ldap_bind($ldap['conn'], $user_dn, $pass);
                    if ($bind_login_user) {
                        return $user_attributes;
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }
}
