<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PortalSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Sync User Roles based on is_supervisor
        \DB::table('users')->where('is_supervisor', 1)->update(['role' => 'superadmin']);
        \DB::table('users')->where('is_supervisor', 0)->update(['role' => 'staff']);

        // 2. Initialize Company Settings if not exists
        \DB::table('company_settings')->updateOrInsert(
            ['id' => 1],
            [
                'company_name' => 'Nita Jaya Catering',
                'about_us' => 'Layanan katering profesional untuk berbagai acara Anda.',
                'phone' => '085767113554',
                'address' => 'Jakarta, Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
