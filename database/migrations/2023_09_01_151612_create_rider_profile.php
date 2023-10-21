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
            
            $table->string('phone_number');
            $table->date('birth_date')->nullable();
            
            $table->string('id_card');
            $table->string('id_card_photo');

            $table->string('bank_account_number');
            $table->string('bank_account_name'); // Bank account holder name.
            $table->string('bank_account_code'); // https://docs.opn.ooo/supported-banks
            $table->string('book_bank_photo'); 
            
            $table->string('avatar')->nullable();
            
            // For student but not required because other rider may not be a student
            $table->string('student_id')->unique()->nullable();
            $table->string('faculty')->nullable();
            $table->string('major')->nullable();

            // Location that rider can easily go to
            $table->string('desire_location')->nullable();

            // Score that user gave to rider
            $table->integer('score')->default(0);

            $table->enum('status', ['pending', 'rejected', 'verified'])->default('pending');
            $table->timestamp('rider_verified_at')->nullable();


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