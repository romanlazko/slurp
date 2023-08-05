<?php

namespace Romanlazko\Slurp\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('name', 'LIKE', "%". request('search') ."%")
            ->with('permissions')
            ->orderBy('name')
            ->get();
            
        return view('admin.slurp.role.index', compact([
            'roles'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::orderBy('id')->get();
        
        return view('admin.slurp.role.create', compact(
            'permissions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'required|integer',
        ]);

        $new_role = Role::create([
            'name' => $request->name,
        ]);

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $new_role->syncPermissions($permissions);

        return redirect()->route('admin.role.index')->with([
            'ok' => true,
            'description' => "Role succesfuly created"
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('id')->get();
        return view('admin.slurp.role.edit', compact(
            'role',
            'permissions'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array|required',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ]);
        $permissions = Permission::whereIn('id', $request->permissions)->get();

        $role->update([
            'name' => $request->name
        ]);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.role.index')->with([
            'ok' => true,
            'description' => "Role succesfuly updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('admin.role.index')->with([
            'ok' => true,
            'description' => "Role succesfuly deleted"
        ]);
    }
}
