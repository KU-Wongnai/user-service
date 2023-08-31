<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Added role to the user
     * Note: User can has many roles
     */
    public function addRole(Request $request) 
    {    

        Gate::authorize('isAdmin', User::class);

        $request->validate([
            'user_id' => ['required'],
            'role_name' => ['required'],
        ]);

        $user_id = $request->get('user_id');
        $role = Role::where('name', '=', $request->get('role_name'))->first();
        
        $user = User::find($user_id);

        if ($user == null) {
            abort(400, "No user was found with the given id = {$user_id}");
        }

        $user->roles()->attach($role->id);

        return [
            'message' => 'Role added successfully',
            'success' => true,
        ];
    }

    /**
     * Added role to the user
     * Note: User can has many roles
     */
    public function removeRole(Request $request) 
    {    
        $request->validate([
            'user_id' => ['required'],
            'role_name' => ['required'],
        ]);

        $user_id = $request->get('user_id');
        $role = Role::where('name', '=', $request->get('role_name'))->first();

        
        $user = User::find($user_id);

        if ($user == null) {
            abort(400, "No user was found with the given id = {$user_id}");
        }

        $user->roles()->detach($role->id);

        return [
            'message' => 'Role removed successfully',
            'success' => true,
        ];
    }
}