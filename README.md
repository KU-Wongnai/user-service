# KU Wongnai - User Service

Handle authentication, authorization, and manage user profile

## Libraries used

-   [laravel/fortify](https://laravel.com/docs/10.x/fortify) - For setup authentication service
-   [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth) - For provide authentication using JWT

## Features

1. Register
2. Login
3. Logout
4. Get user profile
5. Delete my account
6. Delete specific user by admin
7. Add role to user
8. Remove role from user
9. Update user profile
10. Update rider profile

## Setup

Install laravel dependencies

```sh
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

Copy `.env.example` to `.env`

```sh
cp .env.example .env
```

Make sure you have the network called `ku-wongnai_ku-wongnai` this use for connect to RabbitMQ

Start the service

```sh
sail up -d
```

Generate JWT secret

```sh
sail artisan jwt:secret
```

Migrate and seed database

```sh
sail artisan migrate:fresh --seed
```

Generate application key

```sh
sail artisan key:generate
```

If some service can't be run, for example, MySQl run the following command

```sh
sail down --volumes
```

## API

Service run at http://localhost:8090

### Register User

> POST -> http://localhost:8090/register

```json
{
    "name": "Non",
    "email": "qu1etboy@dev.io",
    "password": "12345678",
    "password_confirmation": "12345678"
}
```

### Login

> POST -> http://localhost:8090/api/auth/login

```json
{
    "email": "qu1etboy@dev.io",
    "password": "12345678"
}
```

After succesfully logged in, you will get a JWT Token

### Get user profile

> POST -> http://localhost:8090/api/users/me

If you access this route without JWT token, It should return `401 Unauthorized`. Now try with JWT token

```
Authorization: Bearer <JWT_Token>
```

This should return the user profile

### Delete my account

> DELETE -> http://localhost:8090/api/users/me

### Delete specific user by admin

> DELETE -> http://localhost:8090/api/users/{user}

### Logout

> POST -> http://localhost:8090/api/auth/logout

With `Authorization: Bearer <JWT_Token>` to invalidate token

> There are more routes that created by laravel/fortify but not listed in here. Please refer to the docs for more information

### Role

User can has many roles, for example user can be a normal user or a rider that delivery food to customers. Only admin has an access to this functionality.

Currently fixed to only 3 roles

-   User
-   Rider
-   Admin

```json
// Example response
{
    "id": 4,
    "name": "John Doe",
    "email": "johndoe@dev.io",
    "email_verified_at": null,
    "two_factor_secret": null,
    "two_factor_recovery_codes": null,
    "two_factor_confirmed_at": null,
    "created_at": "2023-09-02T19:49:59.000000Z",
    "updated_at": "2023-09-02T19:49:59.000000Z",
    "roles": [
        {
            "id": 1,
            "name": "user",
            "created_at": "2023-09-02T17:14:04.000000Z",
            "updated_at": "2023-09-02T17:14:04.000000Z",
            "pivot": {
                "user_id": 4,
                "role_id": 1
            }
        }
    ],
    "user_profile": null,
    "rider_profile": null
}
```

Add role to user

> POST -> http://localhost:8090/api/users/role

```json
{
    "user_id": 1,
    "role_name": "rider"
}
```

Remove role from user

> DELETE -> http://localhost:8090/api/users/role

```json
{
    "user_id": 1,
    "role_name": "rider"
}
```

### Profile

User can has 2 profile. One for normal user and second for rider. This action will update or create if no profile found.

User profile

> PUT -> http://localhost:8090/api/users/profile/user

```json
{
    "user_id": 2,
    "phone_number": "0123456789",
    "birth_date": "2003-3-5"
}
```

Rider profile

> PUT -> http://localhost:8090/api/users/profile/rider

```json
{
    "user_id": 2,
    "phone_number": "0890708155",
    "birth_date": "2003-3-5",
    "bank_account_number": "1234567890",
    "id_card": "1234567890123"
}
```
