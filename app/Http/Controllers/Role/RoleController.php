<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleCreateRequest;
use App\Http\Requests\Role\RoleUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $roles = Role::when($request->search, function ($query) use ($request) {
            $query
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('label', 'like', '%' . $request->search . '%');
        })->paginate(10)->withQueryString();
        return Inertia::render('Role/Index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Role/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        Role::create($request->all());
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): \Inertia\Response
    {
        return Inertia::render('Role/Show', ['role' => $role]);
    }

    /**
     * Display the specified resource.
     */
    public function edit(Role $role): \Inertia\Response
    {
        return Inertia::render('Role/Edit', ['role' => $role]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $role->update($request->all());
        return redirect()->route('roles.index');
    }

    public function getPermissionsByRoleId($id): \Inertia\Response
    {
        $role = Role::find($id);
        $permissions = $role->permissions;
        $all_permissions = Permission::all();
        return Inertia::render('Role/AssignPermissionToRole',
            ['permissions' => $permissions, 'all_permissions' => $all_permissions, 'role' => $role]);
    }

    public function editRole(Request $request): \Illuminate\Http\RedirectResponse
    {
        $role = Role::find($request->role['id']);
        $role->permissions()->sync($request->permissions);
        return redirect()->route('roles.index');
    }

}
