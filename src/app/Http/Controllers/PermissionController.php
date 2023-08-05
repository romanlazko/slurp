<?php
namespace Romanlazko\Slurp\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PermissionComment;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permissions = Permission::when(request()->has('search'), function($query) {
            return $query->where(function ($query) {
                $query->where('name', 'LIKE', "%". request('search') ."%")
                    ->orWhere('comment', 'LIKE', "%". request('search') ."%");
            });
        })
        ->orderBy('id')
        ->paginate(50);

        return view('admin.slurp.permission.index', compact([
            'permissions'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.slurp.permission.create');
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
            'name' => 'required|max:255|unique:permissions',
            'comment' => 'required|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
            'comment' => $request->comment,
        ]);

        return redirect()->route('admin.permission.index')->with([
            'ok' => true,
            'description' => "Permission succesfuly created"
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return view('admin.slurp.permission.edit', compact([
            'permission'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|max:255',
            'comment' => 'required|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'comment' => $request->comment,
        ]);

        return redirect()->route('admin.permission.index')->with([
            'ok' => true,
            'description' => "Permission succesfuly updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permission.index')->with([
            'ok' => true,
            'description' => "Permission succesfuly deleted"
        ]);
    }
}
