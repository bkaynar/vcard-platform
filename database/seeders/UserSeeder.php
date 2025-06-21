<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Eski büyük harfli admin rolünü sil
        \Spatie\Permission\Models\Role::where('name', 'Admin')->delete();

        // Roller yoksa oluştur
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Admin kullanıcı
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Kullanıcı',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        // Tüm rollerini kaldırıp sadece admin rolünü ata
        $admin->syncRoles([$adminRole]);

        // Normal kullanıcı
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal Kullanıcı',
                'username' => 'user',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles([$userRole]);
    }
}
