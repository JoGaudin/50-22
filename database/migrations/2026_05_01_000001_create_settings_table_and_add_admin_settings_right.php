<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('settings')->insert([
            'key' => '2fa_enabled',
            'value' => '0',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rightId = (string) Str::uuid();

        DB::table('rights')->insert([
            'id' => $rightId,
            'name' => 'Paramètres (admin)',
            'slug' => 'admin.settings',
            'description' => 'Gérer les paramètres globaux de l\'application.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');

        if ($adminRoleId) {
            DB::table('right_role')->insert([
                'right_id' => $rightId,
                'role_id' => $adminRoleId,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('right_role')
            ->whereIn('right_id', DB::table('rights')->where('slug', 'admin.settings')->pluck('id'))
            ->delete();

        DB::table('rights')->where('slug', 'admin.settings')->delete();

        Schema::dropIfExists('settings');
    }
};
