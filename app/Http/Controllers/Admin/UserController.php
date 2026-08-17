<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::with('roles')->latest('id')->get();
        return view('admin.users.index',compact('data'));
    }


    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('admin.users.create',compact('roles'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->only(['firstname', 'lastname', 'username', 'email', 'password']);
        $input['password'] = Hash::make($input['password']);
        
        // Create user
        $user = User::create($input);
        
        // Assign role chosen from request
        $user->assignRole($request->input('roles'));

        return redirect()->route('admin.users.index')
                        ->with('success','User created successfully');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('admin.users.show',compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('admin.users.edit',compact('user','roles','userRole'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required'
        ]);

        $input = $request->except(['roles', '_token', '_method', 'confirm-password']);
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input, array('password'));
        }

        $user = User::findOrFail($id);
        $user->update($input);

        $user->syncRoles($request->input('roles'));

        return redirect()->route('admin.users.index')
                        ->with('success','User updated successfully');
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('admin.users.index')
                        ->with('success','User deleted successfully');
    }
}
