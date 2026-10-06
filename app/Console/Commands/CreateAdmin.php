<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {--name= : Nama lengkap admin}
                            {--email= : Alamat email admin}';

    protected $description = 'Buat akun administrator baru secara aman.';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Nama admin')));
        $email = mb_strtolower(trim((string) ($this->option('email') ?: $this->ask('Email admin'))));

        $identityValidator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            ],
            [
                'name.required' => 'Nama admin wajib diisi.',
                'email.required' => 'Email admin wajib diisi.',
                'email.email' => 'Format email admin tidak valid.',
                'email.unique' => 'Email tersebut sudah digunakan oleh akun lain.',
            ],
        );

        if ($identityValidator->fails()) {
            foreach ($identityValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password admin (minimal 12 karakter)');
        $passwordConfirmation = (string) $this->secret('Ulangi password admin');

        $passwordValidator = Validator::make(
            [
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(12)->mixedCase()->numbers()->symbols(),
                ],
            ],
            [
                'password.required' => 'Password admin wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
            ],
        );

        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'roles' => ['admin'],
            'station_id' => null,
            'is_active' => true,
        ]);

        $this->newLine();
        $this->info('Akun admin berhasil dibuat.');
        $this->table(
            ['ID', 'Nama', 'Email', 'Peran', 'Status'],
            [[$user->id, $user->name, $user->email, 'Admin', 'Aktif']],
        );

        return self::SUCCESS;
    }
}
