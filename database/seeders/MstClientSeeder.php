<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MstClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('mst_client')->insert([
            [
                'customer_id' => 1,
                'company_name' => 'Namport',
                'display_name' => 'Namport',
                'industry_type_term' => 'Shipping',
                'logo' => null,
                'banner_image' => null,
                'description' => 'Shipping service provider in Namibia.',
                'short_description' => 'Shipping services and logistics.',
                'vat_no' => 'VAT123456',
                'phone_no' => '+264 64 208 2111',
                'website' => 'https://namport.com',
                'email' => 'namport_sqt@yopmail.com',
                'address1' => '2GX2+MP6',
                'address2' => 'Walvis Bay, Namibia',
                'city' => 'Walvis Bay',
                'zip_code' => '', // Namibia doesn't use postal codes widely; leave blank or use '0000' if required
                'state' => 'Erongo', // Walvis Bay is in the Erongo region
                'country' => 'Namibia',
                'country_code' => '+264',
                'timezone_id' => 271,
                'currency_term' => 'NAD', // Namibian Dollar
                'status_term' => 'active',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'last_updated_on' => Carbon::now(),
                'total_projects' => 0,
                'total_completed' => 0,
                'total_business' => 0.00,
                'total_received' => 0.00,
            ],
            [
                'customer_id' => 1,
                'company_name' => 'Softqube',
                'display_name' => 'Softqube Technologies',
                'industry_type_term' => 'IT',
                'logo' => null,
                'banner_image' => null,
                'description' => 'Leading IT solutions provider.',
                'short_description' => 'IT Solutions',
                'vat_no' => 'VAT123456',
                'phone_no' => '1234567890',
                'website' => 'https://softqube.com',
                'email' => 'info@softqube.com',
                'address1' => 'Softqube HQ',
                'address2' => 'Tech Park, Ahmedabad',
                'city' => 'Ahmedabad',
                'zip_code' => '380015',
                'state' => 'GJ',
                'country' => 'India',
                'country_code' => '+91',
                'timezone_id' => 195,
                'currency_term' => 'INR',
                'status_term' => 'active',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'last_updated_on' => Carbon::now(),
                'total_projects' => 0,
                'total_completed' => 0,
                'total_business' => 0.00,
                'total_received' => 0.00,
            ],
        ]);
    }
}
