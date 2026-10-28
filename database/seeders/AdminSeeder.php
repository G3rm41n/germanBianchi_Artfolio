<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Provisión idempotente del Administrador desde .env (RF-12).
     */
    public function run(): void
    {
        $email = config('app.admin_email') ?: env('ADMIN_EMAIL', 'admin@artfolio.test');
        $name = config('app.admin_name') ?: env('ADMIN_NAME', 'Administrador');
        $password = config('app.admin_password') ?: env('ADMIN_PASSWORD', 'password');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'status'   => 'active',
            ]
        );
    }
}
