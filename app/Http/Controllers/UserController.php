<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
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

        if ($user->hasRole($role->name)) {
            abort(400, "This user already had this role");
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

    public function createUserProfile(Request $request) {
        $validateInput = $request->validate([
            'user_id' => ['required'],
            'phone_number' => ['required', 'size:10'],
            'birth_date' => ['required', 'date'],
            'address' => ['nullable', 'min:1', 'max:255'],
            'avatar' => ['nullable'],
            'student_id' => ['nullable', 'size:10'],
            'faculty' => ['nullable', 'min:1', 'max:255'],
            'major' => ['nullable', 'min:1', 'max:255'],
            'favorite_food' => ['nullable', 'min:1', 'max:255'],
            'allergy_food' => ['nullable', 'min:1', 'max:255'],
            'point' => ['nullable', 'numeric', 'min:0'],

        ]);

        $user_id = $request->get('user_id');
        $user = User::find($user_id);

        if ($user == null) {
            abort(400, "No user was found with the given id = {$user_id}");
        }

        if ($user->id !== auth()->user()->id) {
            abort(403, "You are not allowed to create profile for other user");
        }

        // unset($validateInput['user_id']);

        $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id], 
            $validateInput
        );

        return [
            'message' => 'User profile created successfully',
            'success' => true,
        ];
    }

    public function createRiderProfile(Request $request) {
        $validateInput = $request->validate([
            'user_id' => ['required'],
            'phone_number' => ['required', 'size:10'],
            'birth_date' => ['required', 'date'],
            'id_card' => ['required', 'size:13'],
            'bank_account_number' => ['required', 'size:10'],
            'avatar' => ['nullable'],
            'student_id' => ['nullable', 'size:10'],
            'faculty' => ['nullable', 'min:1', 'max:255'],
            'major' => ['nullable', 'min:1', 'max:255'],
            'desire_location' => ['nullable', 'min:1', 'max:255'],
        ]);

        $user_id = $request->get('user_id');
        $user = User::find($user_id);

        if ($user == null) {
            abort(400, "No user was found with the given id = {$user_id}");
        }

        if ($user->id !== auth()->user()->id) {
            abort(403, "You are not allowed to create profile for other user");
        }

        $user->riderProfile()->updateOrCreate(
            ['user_id' => $user->id], 
            $validateInput
        );

        return [
            'message' => 'Rider profile created successfully',
            'success' => true,
        ];
    }
}