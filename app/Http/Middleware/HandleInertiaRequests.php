<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return array_merge(parent::share($request), [
                'auth' => [
                    'user' => null,
                    'role' => null,
                    'permissions' => []
                ],
                'flash' => [
                    'message' => fn() => $request->session()->get('message'),
                ]
            ]);
        }
        $role = $user->role ? $user->role->name : null;
        $permissions = $user->role ? $user->role->permissions->pluck('name') : [];
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'role' => $role,
                'permissions' => $permissions
            ],
            'settings' => [
                'name' => Setting::first()->name,
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
            ]
        ]);
    }
}
