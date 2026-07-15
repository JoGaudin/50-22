<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $userId = (string) Str::uuid();

        DB::table('users')->insert([
            'id' => $userId,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        $userRoleId = DB::table('roles')->where('slug', 'user')->value('id');

        if ($adminRoleId) {
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => $adminRoleId,
            ]);
        }

        if ($userRoleId) {
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => $userRoleId,
            ]);
        }
    }

    public function down(): void
    {
        $user = DB::table('users')->where('email', 'admin@example.com')->first();

        if ($user) {
            DB::table('role_user')->where('user_id', $user->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();
        }
    }
};
