<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@fakemail.com',
                'role' => 'admin',
                'phone_number' => '5551234567',
                'hire_date' => '2022-05-15',
                'supervisor_id' => 5, // E005
                'password' => bcrypt('1234'),
                'status' => 1
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Ali',
                'email' => 'ahmed.ali@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5552345678',
                'hire_date' => '2023-02-20',
                'supervisor_id' => 5, // E005
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5553456789',
                'hire_date' => '2021-09-01',
                'supervisor_id' => 6, // E006
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Miller',
                'email' => 'james.miller@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5554567890',
                'hire_date' => '2020-11-12',
                'supervisor_id' => 5, // E005
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'Rebecca',
                'last_name' => 'Brown',
                'email' => 'rebecca.brown@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5555678901',
                'hire_date' => '2018-08-10',
                'supervisor_id' => 10, // E010
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Smith',
                'email' => 'michael.smith@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5556789012',
                'hire_date' => '2019-04-25',
                'supervisor_id' => 10, // E010
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Hussein',
                'email' => 'fatima.hussein@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5557890123',
                'hire_date' => '2022-06-18',
                'supervisor_id' => 6, // E006
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Williams',
                'email' => 'david.williams@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5558901234',
                'hire_date' => '2021-03-30',
                'supervisor_id' => 5, // E005
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'Clara',
                'last_name' => 'Wilson',
                'email' => 'clara.wilson@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5559012345',
                'hire_date' => '2020-10-15',
                'supervisor_id' => 6, // E006
                'password' => bcrypt('password123'),
                'status' => 1
            ],
            [
                'first_name' => 'Thomas',
                'last_name' => 'Taylor',
                'email' => 'thomas.taylor@fakemail.com',
                'role' => 'employee',
                'phone_number' => '5550123456',
                'hire_date' => '2017-01-05',
                'supervisor_id' => null, // No supervisor
                'password' => bcrypt('password123'),
                'status' => 1
            ],
        ];
        foreach ($users as $key => $value) {
            $user = User::create($value);
            $user->assignRole($value['role']);
        }
    }
}

