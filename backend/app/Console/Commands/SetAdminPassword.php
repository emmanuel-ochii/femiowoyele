<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use RuntimeException;

class SetAdminPassword extends Command
{
    protected $signature = 'admin:set-password
                            {--email=admin@femiowoyele.com : Admin user email address}
                            {--password= : New password. Prefer ADMIN_PASSWORD or the secure prompt.}
                            {--create : Create the admin user if it does not exist}
                            {--keep-tokens : Do not revoke existing admin API tokens}';

    protected $description = 'Set or rotate an admin CMS password';

    public function handle(): int
    {
        $email = (string) $this->option('email');
        try {
            $password = $this->resolvePassword();
        } catch (RuntimeException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! $this->isStrongEnough($password)) {
            $this->components->error('Password must be at least 12 characters and include letters, numbers, and symbols.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            if (! $this->option('create')) {
                $this->components->error("No admin user exists for {$email}. Re-run with --create if this is the first admin.");

                return self::FAILURE;
            }

            $user = new User([
                'name' => 'FemiOwoyele Admin',
                'email' => $email,
                'role' => 'admin',
            ]);
        }

        $user->forceFill([
            'password' => $password,
            'role' => $user->role ?: 'admin',
        ])->save();

        if (! $this->option('keep-tokens')) {
            $user->tokens()->delete();
        }

        $this->components->info("Admin password updated for {$email}.");

        if (! $this->option('keep-tokens')) {
            $this->components->info('Existing admin API tokens were revoked.');
        }

        return self::SUCCESS;
    }

    private function resolvePassword(): string
    {
        $password = (string) ($this->option('password') ?: env('ADMIN_PASSWORD'));

        if ($password !== '') {
            return $password;
        }

        $password = (string) $this->secret('New admin password');
        $confirmation = (string) $this->secret('Confirm admin password');

        if ($password !== $confirmation) {
            throw new RuntimeException('Password confirmation does not match.');
        }

        return $password;
    }

    private function isStrongEnough(string $password): bool
    {
        return strlen($password) >= 12
            && preg_match('/[A-Za-z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }
}
