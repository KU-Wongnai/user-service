<?php

namespace App\Http\Controllers;

use App\Models\RiderProfile;
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

    /**
     * Create user profile
     */
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

    /**
     * Create rider profile
     */
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

    /**
     * Give score to rider from 0 to 5
     */
    public function giveScoreToRider(Request $request, User $user) {

        $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:5'],
        ]);

        if ($user->id === auth()->user()->id) {
            abort(403, "You are not allowed to give score to yourself");
        }

        // Check if user has rider profile
        $riderProfile = RiderProfile::where('user_id', '=', $user->id)->first();

        if ($riderProfile == null) {
            abort(400, "This user do not have a rider profile");
        }

        $riderProfile->score = $request->get('score');
        $riderProfile->save();

        return [
            'message' => 'Score given successfully',
            'success' => true,
        ];
    }

    /**
     * Update rider status to pending, rejected, or verified
     */
    public function updateRiderStatus(Request $request, User $user) {
        
        Gate::authorize('isAdmin', User::class);

        $request->validate([
            'status' => ['required', 'in:pending,rejected,verified'],
        ]);

        if ($user->id === auth()->user()->id) {
            abort(403, "You are not allowed to set status to yourself");
        }

        // Check if user has rider profile
        $riderProfile = RiderProfile::where('user_id', '=', $user->id)->first();

        if ($riderProfile == null) {
            abort(400, "This user do not have a rider profile");
        }

        $riderProfile->status = $request->get('status');
        $riderProfile->rider_verified_at = $request->get('status') === 'verified' ? now() : null;
        $riderProfile->save();

        return [
            'message' => 'Status set successfully',
            'success' => true,
        ];
    }

    /**
     * Delete user from database
     */
    public function destory(User $user) {
        
        // Don't allow user to delete other user
        if ($user->id !== auth()->user()->id) {
            abort(403, "You are not allowed to delete other user");
        }

        $user->delete();

        return [
            'message' => 'User deleted successfully',
            'success' => true,
        ];
    }


}