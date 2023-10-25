<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use App\RabbitMQPublisher;
use App\Http\Controllers\NotificationSender;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'loginWithGoogle', 'handleGoogleCallback']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function loginWithGoogle() {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback() {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::where('provider_id', $googleUser->id)->where('provider', 'google')->first();

        // If user already exists, just log them in
        if ($user) {
            $token = auth()->login($user);
            return $this->respondWithToken($token);
        } 
        
        $user = User::updateOrCreate([
            'provider_id' => $googleUser->id,
            'provider' => 'google',
        ], [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'email_verified_at' => now(),
        ]);

        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.user', 'topic');
        $publisher->publish(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'emailVerifiedAt' => $user->email_verified_at,
            'avatar' => null, // There is no way user will have an avatar at this point, so we set it to null
        ]), 'events.user', 'user.created');

        // Sent email       
        $notificationSender = new NotificationSender();
        $notificationSender->sendEmailWelcomeUser($user->email);
        // Send in-app notification
        $notificationSender->sendInAppWelcomeNewUser($user->id);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}