<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('username')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyIncrements('gender')->nullable();
            $table->string('profile_pix')->nullable();
            $table->text('about')->nullable();
            $table->tinyInteger('user_type')->comment('1: user, 2:admin');
            $table->tinyInteger('status')->default(0)->nullable();
            $table->tinyInteger('reg_status')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            'first_name' => 'Admin',
            'email' => 'admin@admin.com',
            'last_name' => 'Admin Last',
            'password' => Hash::make('123456789'),
            'user_type' => 2,
            'status' => 1,
            'reg_status' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
