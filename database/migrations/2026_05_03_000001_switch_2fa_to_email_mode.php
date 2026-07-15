<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remplacer le paramètre 2fa_enabled (bool) par 2fa_mode (none|email)
        $enabled = DB::table('settings')->where('key', '2fa_enabled')->value('value');

        DB::table('settings')->where('key', '2fa_enabled')->delete();

        DB::table('settings')->insert([
            'key' => '2fa_mode',
            'value' => $enabled === '1' ? 'email' : 'none',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Supprimer les colonnes TOTP (plus utilisées avec la 2FA par email)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }

    public function down(): void
    {
        $mode = DB::table('settings')->where('key', '2fa_mode')->value('value');

        DB::table('settings')->where('key', '2fa_mode')->delete();

        DB::table('settings')->insert([
            'key' => '2fa_enabled',
            'value' => $mode === 'email' ? '1' : '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')->nullable()->after('password');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
        });
    }
};
