<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminDapurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buat role admin-dapur jika belum ada
        $roleDapur = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin-dapur']);

        // Buat user admin dapur jika belum ada
        $userDapur = \App\Models\User::updateOrCreate(
            ['username' => 'admindapur'],
            [
                'email' => 'dapur@nitajaya.com',
                'firstname' => 'Admin',
                'lastname' => 'Dapur',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            ]
        );

        // Assign role ke user
        if (!$userDapur->hasRole('admin-dapur')) {
            $userDapur->assignRole($roleDapur);
        }
    }
}
