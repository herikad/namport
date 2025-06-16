<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MstEmployeeSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('mst_employee')->insert([
            [
                'customer_id' => 1,
                'department_id' => 2,
                'user_id' => 3,
                'role_id' => 4,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'display_name' => 'John D.',
                'profilepicture' => null,
                'email' => 'john.doe@example.com',
                'mobile_no' => '9876543210',
                'shortdescription' => 'Senior developer with 5+ years of experience.',
                'status_term' => 'active',
                'voice_profile' => null,
                'is_voiceprofile_done' => 0,
                'lastlogindate' => $now,
                'is_admin' => 0,
                'is_non_staffmember' => 0,
                'hourly_rate' => 500.00,
                'is_active' => 1,
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ],
            [
                'customer_id' => 1,
                'department_id' => 2,
                'user_id' => 4,
                'role_id' => 5,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'display_name' => 'Jane S.',
                'profilepicture' => null,
                'email' => 'jane.smith@example.com',
                'mobile_no' => '9876543211',
                'shortdescription' => 'HR manager with experience in team building.',
                'status_term' => 'active',
                'voice_profile' => null,
                'is_voiceprofile_done' => 0,
                'lastlogindate' => $now,
                'is_admin' => 0,
                'is_non_staffmember' => 0,
                'hourly_rate' => 450.00,
                'is_active' => 1,
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ],
            [
                'customer_id' => 2,
                'department_id' => 3,
                'user_id' => 5,
                'role_id' => 6,
                'first_name' => 'Raj',
                'last_name' => 'Patel',
                'display_name' => 'Raj P.',
                'profilepicture' => null,
                'email' => 'raj.patel@example.com',
                'mobile_no' => '9876543212',
                'shortdescription' => 'Marketing expert and client relationship manager.',
                'status_term' => 'active',
                'voice_profile' => null,
                'is_voiceprofile_done' => 0,
                'lastlogindate' => $now,
                'is_admin' => 0,
                'is_non_staffmember' => 0,
                'hourly_rate' => 400.00,
                'is_active' => 1,
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ]
        ]);
    }
}
