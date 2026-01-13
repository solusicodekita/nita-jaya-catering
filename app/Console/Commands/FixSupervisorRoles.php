<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class FixSupervisorRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:supervisor-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memberikan role admin kepada user yang memiliki is_supervisor = 1 tapi belum memiliki role admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memeriksa user dengan is_supervisor = 1...');

        // Ambil role admin
        $roleAdmin = Role::where('name', 'admin')->first();
        
        if (!$roleAdmin) {
            $this->error('Role "admin" tidak ditemukan. Membuat role admin...');
            $roleAdmin = Role::create(['name' => 'admin']);
            $this->info('Role "admin" berhasil dibuat.');
        }

        // Ambil semua user dengan is_supervisor = 1
        $supervisors = User::where('is_supervisor', 1)->get();

        $this->info("Ditemukan {$supervisors->count()} user dengan is_supervisor = 1");

        $fixed = 0;
        $alreadyHasRole = 0;

        foreach ($supervisors as $user) {
            // Cek apakah user sudah memiliki role admin
            if (!$user->hasRole('admin')) {
                $user->assignRole($roleAdmin);
                $this->info("✓ Role admin diberikan kepada: {$user->username}");
                $fixed++;
            } else {
                $this->line("  {$user->username} sudah memiliki role admin");
                $alreadyHasRole++;
            }
        }

        $this->newLine();
        $this->info("Selesai!");
        $this->info("User yang diperbaiki: {$fixed}");
        $this->info("User yang sudah memiliki role: {$alreadyHasRole}");

        return Command::SUCCESS;
    }
}

