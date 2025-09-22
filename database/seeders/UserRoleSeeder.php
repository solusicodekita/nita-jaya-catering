<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update existing users with roles
        DB::table('users')->where('username', 'superadmin')->update([
            'role' => 'superadmin'
        ]);

        DB::table('users')->where('username', 'admin')->update([
            'role' => 'admin'
        ]);

        DB::table('users')->where('username', 'supervisor')->update([
            'role' => 'supervisor'
        ]);

        DB::table('users')->where('username', 'owner')->update([
            'role' => 'owner'
        ]);

        DB::table('users')->where('username', 'petugas')->update([
            'role' => 'petugas_gudang'
        ]);

        // Insert new users if they don't exist
        $users = [
            [
                'firstname' => 'Super',
                'lastname' => 'Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@nitajaya.com',
                'password' => bcrypt('superadmin123'),
                'role' => 'superadmin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'username' => 'admin',
                'email' => 'admin@nitajaya.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'firstname' => 'Supervisor',
                'lastname' => 'User',
                'username' => 'supervisor',
                'email' => 'supervisor@nitajaya.com',
                'password' => bcrypt('supervisor123'),
                'role' => 'supervisor',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'firstname' => 'Owner',
                'lastname' => 'User',
                'username' => 'owner',
                'email' => 'owner@nitajaya.com',
                'password' => bcrypt('owner123'),
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'firstname' => 'Petugas',
                'lastname' => 'Gudang',
                'username' => 'petugas',
                'email' => 'petugas@nitajaya.com',
                'password' => bcrypt('petugas123'),
                'role' => 'petugas_gudang',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
