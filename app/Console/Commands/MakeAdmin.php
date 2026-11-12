<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'journal:admin {email}';
    protected $description = 'Give an existing account access to the admin pages';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (!$user) {
            $this->error('Register this email on the site first.');
            return self::FAILURE;
        }
        $user->admin = true;
        $user->save();
        $this->info('Admin access is ready. Log in and open /admin.');
        return self::SUCCESS;
    }
}
