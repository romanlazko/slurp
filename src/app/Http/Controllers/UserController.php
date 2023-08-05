<?php

namespace Romanlazko\Slurp\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    protected $attributes;

    public function __construct()
    {
        $this->attributes = $this->getUserModelAttributes();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(50);

        return view('admin.slurp.user.index', [
            'users' => $users,
            'attributes' => $this->attributes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.slurp.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.user.index')->with([
            'ok' => true,
            'description' => "User succesfuly created"
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('admin.slurp.user.edit', [
            'user' => $user,
            'roles' => $roles,
            'attributes' => $this->attributes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        foreach ($this->attributes as $attribute) {
            $attributes[$attribute] = $request->$attribute;
        }

        $user->update($attributes);

        $user->assignRole($request->roles);

        return redirect()->route('admin.user.index')->with([
            'ok' => true,
            'description' => "User succesfuly updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.user.index')->with([
            'ok' => true,
            'description' => "User succesfuly deleted"
        ]);
    }

    private function getUserModelAttributes()
    {
        $userModel = new User();

        $fillableAttributes = $userModel->getFillable();

        $hiddenAttributes = $userModel->getHidden();

        return array_diff($fillableAttributes, $hiddenAttributes);
    }
}
