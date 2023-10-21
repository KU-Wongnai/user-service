<?php

namespace App\Http\Controllers;

use App\Models\RiderProfile;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\RabbitMQPublisher;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    /**
     * Get all users
     */
    public function findAll() {
        // Gate::authorize('isAdmin', User::class);
        
        return User::with('roles')
                    ->with('userProfile')
                    ->with('riderProfile')
                    ->get();
    }

    /**
     * Get user by id
     */
    public function findById(User $user) {
        // Gate::authorize('isAdmin', User::class);
        
        return User::with('roles')
                    ->with('userProfile')
                    ->with('riderProfile')
                    ->find($user->id);
    }

    public function findByEmail(Request $request) {
        $request->merge(['email' => $request->route('email')]);
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->get('email');
        $user = User::with('roles')
                    ->with('userProfile')
                    ->with('riderProfile')
                    ->where('email', '=', $email)
                    ->first();
            
        if ($user == null) {
            throw ValidationException::withMessages([
                'email' => ['No user was found with the given email'],
            ]);
        }

        return response()->json($user);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = User::with('roles')
                    ->with('userProfile')
                    ->with('riderProfile')
                    ->find(auth()->user()->id);
                    
        return response()->json($user);
    }

    /**
     * Sending email verification
     */
    public function sendEmailVerificationNotification() {
        $user = User::find(auth()->user()->id);
        $user->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Email verification sent successfully',
            'success' => true,
        ]);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request) {
        // $user = User::find(auth()->user()->id);
        $request->validate([
            'token' => ['required'],
        ]);

        // Verify jwt
        $token = $request->get('token');

        Log::info("Token: {$token}");

        $user = auth()->setToken($token)->user();

        if ($user == null) {
            abort(400, "Invalid token");
        }

        if ($user->hasVerifiedEmail()) {
            abort(400, "Email already verified");
        }

        $user->markEmailAsVerified();
        $user->refresh();

        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.user', 'topic');
        $publisher->publish(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'emailVerifiedAt' => $user->email_verified_at,
            'avatar' => $user->userProfile ? $user->userProfile->avatar : null,
        ]), 'events.user', 'user.updated');

        // Invalidate token
        auth()->invalidate();

        return redirect(config('app.frontend_url') . '/settings');
    }
    
    /**
     * Delete my account
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMyAccount() {
        $user = User::find(auth()->user()->id);
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully',
            'success' => true,
        ]);
    }

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
     * Added role to the user by admin
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
            'phone_number' => ['nullable', 'regex:/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|
            2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|
            4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/'], // regex for validate a phone number
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'min:1', 'max:255'],
            'avatar' => ['nullable', 'string'],
            'student_id' => ['nullable', 'size:10'],
            'faculty' => ['nullable', 'min:1', 'max:255'],
            'major' => ['nullable', 'min:1', 'max:255'],
            'favorite_food' => ['nullable', 'min:1', 'max:255'],
            'allergy_food' => ['nullable', 'min:1', 'max:255'],
            'point' => ['nullable', 'numeric', 'min:0'],

        ]);

        $user = User::find(auth()->user()->id);

        $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id], 
            $validateInput
        );

        // Ppublish to message queue
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.user', 'topic');
        $publisher->publish(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'emailVerifiedAt' => $user->email_verified_at,
            'avatar' => $user->userProfile ? $user->userProfile->avatar : null,
        ]), 'events.user', 'user.updated');

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
            'phone_number' => ['required', 'regex:/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|
            2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|
            4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/'],
            'birth_date' => ['nullable', 'date'],
            'id_card' => ['required', 'size:13'],
            'id_card_photo' => ['required', 'string'],
            'bank_account_number' => ['required', 'size:10'],
            'bank_account_name' => ['required', 'min:1', 'max:255'],
            'bank_account_code' => ['required', 'min:1', 'max:255'],
            'book_bank_photo' => ['required', 'string'],
            'avatar' => ['nullable', 'string'],
            'student_id' => ['nullable', 'size:10'],
            'faculty' => ['nullable', 'min:1', 'max:255'],
            'major' => ['nullable', 'min:1', 'max:255'],
            'desire_location' => ['nullable', 'min:1', 'max:255'],
        ]);

        $user = User::find(auth()->user()->id);
        
        $user->riderProfile()->updateOrCreate(
            ['user_id' => $user->id], 
            $validateInput
        );

        // TODO: publish to message queue

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
     * Update rider status to pending, rejected, or verified by admin
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
     * Delete user from database by admin
     */
    public function destroy(User $user) {
        
        Gate::authorize('isAdmin', User::class);
        $user->delete();

        return [
            'message' => 'User deleted successfully',
            'success' => true,
        ];
    }


}