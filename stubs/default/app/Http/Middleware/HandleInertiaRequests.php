<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Support\Facades\Auth;
use Pkt\StarterKit\Helpers\GlobalSearch;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'user_permissions' => $request->user()?->getAllPermissions()->pluck('name'),
                'user_roles' => $request->user()?->getRoleNames(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'ziggy' => fn () => [
                ...(new Ziggy())->toArray(),
                'location' => $request->url(),
            ],
            'notifications' => Auth::user() ? Auth::user()->unreadNotifications : null,
            'isEnableGlobalSearch' => (new GlobalSearch())->isEnable(),
        ];
    }
}
