<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin seeder provisions admin user from configuration.
     */
    public function test_admin_seeder_provisions_admin_user_from_environment(): void
    {
        $targetEmail = 'superadmin@artfolio.test';
        $targetName = 'Super Administrador';
        $targetPassword = 'StrongPassword123!';

        // Configurar variables en tiempo de ejecución
        config([
            'app.admin_email' => $targetEmail,
            'app.admin_name' => $targetName,
            'app.admin_password' => $targetPassword,
        ]);

        // Ejecutar Seeder
        $this->seed(AdminSeeder::class);

        // Verificar que existe en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => $targetEmail,
            'name' => $targetName,
            'is_admin' => true,
            'status' => 'active',
        ]);

        $admin = User::where('email', $targetEmail)->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check($targetPassword, $admin->password));
    }

    /**
     * Multiple executions of admin seeder are idempotent.
     */
    public function test_admin_seeder_is_idempotent(): void
    {
        // Ejecutar primera vez
        $this->seed(AdminSeeder::class);
        $countFirstRun = User::where('is_admin', true)->count();

        // Ejecutar segunda vez
        $this->seed(AdminSeeder::class);
        $countSecondRun = User::where('is_admin', true)->count();

        $this->assertEquals(1, $countFirstRun);
        $this->assertEquals($countFirstRun, $countSecondRun);
    }
}
