<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin account
        $user = new User();
        $user->name = "admin";
        $user->email = "admin@example.com";
        $user->password = Hash::make("admin");

        $user->save();

        $user->roles()->sync([1, 3]); // Add user role and admin role


        $user = new User();
        $user->name = "User2";
        $user->email = "User2@example.com";
        $user->password = Hash::make("12345678");

        $user->save();

        $user->roles()->sync([1]); // Add user role and admin role

    }
}