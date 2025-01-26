<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('users', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('name');
    //         $table->string('email')->unique();
    //         $table->timestamp('email_verified_at')->nullable();
    //         $table->string('password');
    //         $table->rememberToken();
    //         $table->timestamps();
    //     });

    //     Schema::create('password_reset_tokens', function (Blueprint $table) {
    //         $table->string('email')->primary();
    //         $table->string('token');
    //         $table->timestamp('created_at')->nullable();
    //     });

    //     Schema::create('sessions', function (Blueprint $table) {
    //         $table->string('id')->primary();
    //         $table->foreignId('user_id')->nullable()->index();
    //         $table->string('ip_address', 45)->nullable();
    //         $table->text('user_agent')->nullable();
    //         $table->longText('payload');
    //         $table->integer('last_activity')->index();
    //     });
    // }
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->string('name'); // User's name
        $table->string('username')->unique()->nullable(); // Unique username for the user
        $table->string('email')->unique(); // Unique email
        $table->timestamp('email_verified_at')->nullable(); // For email verification
        $table->string('password'); // Hashed password
        $table->string('profile_picture')->default('users/default-image.png'); // URL to profile picture
        $table->text('bio')->nullable(); // Optional biography
        $table->enum('role', ['admin', 'user'])->default('user'); 
        $table->boolean('is_active')->default(true); // Account active status
        $table->rememberToken(); // Token for "Remember Me" functionality
        $table->timestamps(); // Created at and Updated at timestamps
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Schema::dropIfExists('password_reset_tokens');
        // Schema::dropIfExists('sessions');
    }
};
