<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCreateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AuthController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::create($request->validated());
//        Auth::login($user);
        return redirect::route('home')->with('message', 'Welcome to your website');
    }

    public function login(LoginRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return Inertia::location(route('welcome'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }


    public function update(Request $request, string $id)
    {
        //
    }

    public function create(): \Inertia\Response
    {
        $roles = Role::all();
        return Inertia::render('User/Create', ['roles' => $roles]);
    }

    public function editPassword(): \Inertia\Response
    {
        return Inertia::render('User/ChangePassword');
    }

    public function updatePassword(ChangePasswordRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->user()->update([
                'password' => Hash::make($request->password)
            ]);
            Auth::logoutOtherDevices($request->password);
            return back();

        } catch (\Exception $e) {
            return back();
        }
    }
}
