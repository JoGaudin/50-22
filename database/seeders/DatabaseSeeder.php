<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@example.com');
        $name = (string) env('ADMIN_NAME', 'Administrator');
        $plainPassword = env('ADMIN_PASSWORD');

        if ($plainPassword === null || $plainPassword === '') {
            $plainPassword = Str::password(24);
            if (app()->environment('local')) {
                fwrite(STDERR, "ADMIN_PASSWORD non défini : mot de passe généré pour {$email}\n{$plainPassword}\n");
            }
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::query()->where('slug', 'admin')->first();
        if ($adminRole !== null) {
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        $this->call(ParamDescriptionSeeder::class);
        $this->call(RugbyNationaleSeeder::class);
    }
}
