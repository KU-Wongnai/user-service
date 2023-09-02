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
        Schema::create('rider_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // $table->id();
            $table->string('phone_number');
            $table->string('id_card');
            $table->date('birth_date');
            $table->string('bank_account_number');
        
            $table->string('avatar')->nullable();
            
            // For student but not required because other rider may not be a student
            $table->string('student_id')->unique()->nullable();
            $table->string('faculty')->nullable();
            $table->string('major')->nullable();

            // Location that rider can easily go to
            $table->string('desire_location')->nullable();

            $table->timestamps(); // created_at, updated_at

            $table->primary('user_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_profile');
    }
};