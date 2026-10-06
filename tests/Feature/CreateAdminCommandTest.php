<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_admin_with_hidden_password_prompts(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Administrator',
            '--email' => 'ADMIN@example.com',
        ])
            ->expectsQuestion('Password admin (minimal 12 karakter)', 'Rahasia!1234')
            ->expectsQuestion('Ulangi password admin', 'Rahasia!1234')
            ->expectsOutput('Akun admin berhasil dibuat.')
            ->assertSuccessful();

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Administrator', $user->name);
        $this->assertSame('admin', $user->role);
        $this->assertSame(['admin'], $user->roles);
        $this->assertTrue($user->is_active);
        $this->assertNull($user->station_id);
        $this->assertTrue(Hash::check('Rahasia!1234', $user->password));
    }

    public function test_it_refuses_to_replace_an_existing_account(): void
    {
        User::create([
            'name' => 'Admin Lama',
            'email' => 'admin@example.com',
            'password' => 'Rahasia!5678',
            'role' => 'admin',
            'roles' => ['admin'],
            'is_active' => true,
        ]);

        $this->artisan('app:create-admin', [
            '--name' => 'Administrator',
            '--email' => 'admin@example.com',
        ])
            ->expectsOutput('Email tersebut sudah digunakan oleh akun lain.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 1);
    }
}
