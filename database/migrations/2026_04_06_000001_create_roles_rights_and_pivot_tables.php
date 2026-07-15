<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('right_role', function (Blueprint $table) {
            $table->foreignUuid('right_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['right_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        $now = now();

        $rights = [
            ['name' => 'Utilisateur', 'slug' => 'user', 'description' => 'Droit de base pour un compte authentifié.'],
            ['name' => 'Utilisateurs (admin)', 'slug' => 'admin.users', 'description' => 'Gérer les utilisateurs et invitations.'],
            ['name' => 'Rôles (admin)', 'slug' => 'admin.roles', 'description' => 'Gérer les rôles et leurs droits.'],
            ['name' => 'Droits (admin)', 'slug' => 'admin.rights', 'description' => 'Gérer les droits.'],
        ];

        $rightIds = [];
        foreach ($rights as $row) {
            $uuid = (string) Str::uuid();
            DB::table('rights')->insert([
                'id' => $uuid,
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $rightIds[$row['slug']] = $uuid;
        }

        $adminRoleId = (string) Str::uuid();
        DB::table('roles')->insert([
            'id' => $adminRoleId,
            'name' => 'Administrateur',
            'slug' => 'admin',
            'description' => 'Accès complet ; reçoit automatiquement tout nouveau droit.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $userRoleId = (string) Str::uuid();
        DB::table('roles')->insert([
            'id' => $userRoleId,
            'name' => 'Utilisateur',
            'slug' => 'user',
            'description' => 'Rôle par défaut pour les comptes standards.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($rightIds as $rid) {
            DB::table('right_role')->insert([
                'right_id' => $rid,
                'role_id' => $adminRoleId,
            ]);
        }

        DB::table('right_role')->insert([
            'right_id' => $rightIds['user'],
            'role_id' => $userRoleId,
        ]);

        $adminUserIds = DB::table('users')->where('is_admin', true)->pluck('id');
        foreach ($adminUserIds as $userId) {
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => $adminRoleId,
            ]);
        }

        $allUserIds = DB::table('users')->pluck('id');
        foreach ($allUserIds as $userId) {
            if ($adminUserIds->contains($userId)) {
                continue;
            }
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => $userRoleId,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        if ($adminRoleId !== null) {
            $adminUserIds = DB::table('role_user')
                ->where('role_id', $adminRoleId)
                ->pluck('user_id');
            DB::table('users')->whereIn('id', $adminUserIds)->update(['is_admin' => true]);
        }

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('right_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('rights');
    }
};
