<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $users = User::with('role')->when($request->search, function ($query) use ($request) {
            $query
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        })->paginate(10)->withQueryString();
        return Inertia::render('User/UserComponent', [
            'users' => $users,
            'searchTerm' => $request->search,
        ]);
    }

    public function edit(User $user): \Inertia\Response
    {
        $roles = Role::all();
        return Inertia::render('User/Edit', ['user' => $user, 'roles' => $roles]);
    }

    public function update(UserUpdateRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id
        ]);
        return redirect()->route('users.index');
    }

}
