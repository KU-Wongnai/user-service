<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // $table->id();
            $table->string('phone_number');
            $table->date('birth_date');
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            
            // For student but not required because user may not be a student
            $table->string('student_id')->unique()->nullable();
            $table->string('faculty')->nullable();
            $table->string('major')->nullable();

            // Use for recommendation system
            $table->string('favorite_food')->nullable();
            $table->string('allergy_food')->nullable();

            // Point from doing transaction in the app, e.g. 100 Baht = 1 point. 
            // Can be used to redeem rewards or discount
            $table->float('point')->default(0);

            $table->timestamps(); // created_at, updated_at

            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};