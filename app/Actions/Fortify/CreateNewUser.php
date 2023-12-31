<?php

namespace App\Actions\Fortify;

use App\Http\Controllers\NotificationSender;
use App\Jobs\UserCreated;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\RabbitMQPublisher;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = new User();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);

        $user->save();

        // Add default role (USER) to the user
        $default_role = Role::where('name', '=', 'user')->first();
        $user->roles()->attach($default_role->id);

        // Sent email       
        $notificationSender = new NotificationSender();
        $notificationSender->sendEmailWelcomeUser($user->email);
        // Send in-app notification
        $notificationSender->sendInAppWelcomeNewUser($user->id);

        $user->refresh();
        
        // Publish an event to the message queue
        // UserCreated::dispatch($user->toArray())->onQueue('users');

        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.user', 'topic');
        // $publisher->publish('Hello from user service, user has been created!', 'events.user.created', 'user.created');
        $publisher->publish(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'emailVerifiedAt' => $user->email_verified_at,
            'avatar' => null, // There is no way user will have an avatar at this point, so we set it to null
        ]), 'events.user', 'user.created');

        return $user;
    }
}