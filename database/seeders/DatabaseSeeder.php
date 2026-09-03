<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Provisión idempotente del Administrador desde .env (RF-12)
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@artfolio.test')],
            [
                'name'     => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin1234!')),
                'is_admin' => true,
                'status'   => 'active',
            ]
        );
    }
}
