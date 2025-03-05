<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;
class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('accounts')->insert([
            'name' => "Default",
            'balance' => "50000",

        ]);
      DB::table('locations')->insert([
            'name' => "Default",

        ]);

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'locations' => '[{"id":"1","text":"Default"}]',
            'cashAccounts' => '{"1":"1"}',
            'cardAccounts' => '{"1":"1"}',
            'onlineAccounts' => '{"1":"1"}',
            'gpayAccounts' => '{"1":"1"}',
            'phonepeAccounts' => '{"1":"1"}',
            'amazonpayAccounts' => '{"1":"1"}',

            'password' => Hash::make('ajs2020!@#$',),
        ]);

        DB::table('roles')->insert([
            'name' => "Super Admin",
            'guard_name' => 'api',


        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\Models\User',
            'model_id' => 1,

        ]);
        DB::table('customers')->insert([
            'id' => 1,
            'name' => 'WALK-IN CUSTOMER',
        ]);

        DB::table('settings')->insert([
            'id' => 1,
            'business_name' => 'ABC',
            'currency' => 'Rs. ',
            'gst_number' => '33BFUPR7716A1Z9',
            'phone' => '9876543210',
            'print_price_decimal_value' => 2,
            'print_quantity_decimal_value' => 0,
            'account_id' => 1,
            'allow_product_code_unique' => 1,
            'print_total_savings' => 1,
            'location_id' => 1,
            'customer_id' => 1,
            'license' => 'eyJpdiI6InZFbkpLUm5sSHE1UzZnOG9RczNuQ2c9PSIsInZhbHVlIjoib1hNZUNsblpwQ2lQQkFVRnJNVVIvKzJjYytYcS9icmJPOGtZSU56RTBxYz0iLCJtYWMiOiIzMjZmNDBiYzA0MDFhNmRlY2Y1YWUxM2Y5MWFhNjQyYjlkNzYyMjcwZTRmYzQzZTY1Nzc4MzY0OWM2OGUwNTAyIiwidGFnIjoiIn0=',
            'address' => 'NO. 23, 7TH CROSS,\n
ARULANANDHA NAGAR,\n
THANJAVUR',

        ]);

        DB::table('model_has_permissions')->insert([
            [ 'permission_id' => 1, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 2, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 3, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 4, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 5, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 6, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 7, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 8, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 9, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 10, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 11, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 12, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 13, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 14, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 15, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 16, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 17, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 18, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 19, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 20, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 21, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 22, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 23, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 24, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 25, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 26, 'model_type' => 'App\Models\User', 'model_id' => 1],
            [ 'permission_id' => 27, 'model_type' => 'App\Models\User', 'model_id' => 1],
        ]
    );
    }
}
