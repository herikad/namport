<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstCustomersSeeder extends Seeder
{
    public function run()
    {
        DB::table('mst_customers')->insert([
            'company_name'   => 'Namport',
            'display_name'   => 'Namport',
            'first_name'     => 'Namport',
            'last_name'      => 'Admin',
            'mobile_no'      => '0810000000',
            'email'          => 'info@namport.com',
            'website'        => 'https://www.namport.com',
            'country'        => 'Namibia',
            'state'          => 'Erongo',
            'city'           => 'Walvis Bay',
            'address1'       => '1 Dock Road',
            'zip_code'       => '9000',
            'created_by'     => 1,
            'created_at'     => now(),
            'status_term'    => 'active',
            'registered_on'  => now(),
            'is_archive'     => 0,
            'time_zone_term' => 'Africa/Windhoek',
            'currency_term'  => 'NAD',
            // 'total_client'   => 1,
            // 'total_projects' => 1,
            // 'is_active'      => 1,
        ]);
    }
}
