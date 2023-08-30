# KU Wongnai - User Service

Handle authentication, authorization, and manage user profile

## Libraries used

-   [laravel/fortify](https://laravel.com/docs/10.x/fortify) - For setup authentication service
-   [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth) - For provide authentication using JWT

## API

Service run at http://localhost:8090

### Register User

> POST -> http://localhost:8090/register

```
{
    "name": "Non",
    "email": "qu1etboy@dev.io",
    "password": "12345678",
    "password_confirmation": "12345678"
}
```

### Login

> POST -> http://localhost:8090/api/auth/login

```
{
    "email": "qu1etboy@dev.io",
    "password": "12345678"
}
```

After succesfully logged in, you will get a JWT Token

### Get user profile

> POST -> http://localhost:8090/api/auth/me

If you access this route without JWT token, It should return `401 Unauthorized`. Now try with JWT token

```
Authorization: Bearer <JWT_Token>
```

This should return the user profile

> There are more routes that created by laravel/fortify but not listed in here. Please refer to the docs for more information
