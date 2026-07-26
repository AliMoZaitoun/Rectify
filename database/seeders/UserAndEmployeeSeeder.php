<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Core\Branch;
use App\Models\Core\Employee;
use App\Models\Core\EmployeeBranch;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserAndEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocationId = Location::first()?->id ?? 1;

        // 1. إنشاء الموظفين
        $employeesData = [
            [
                'first_name' => 'أحمد',
                'last_name'  => 'العلي',
                'email'      => 'manager1@company.com',
                'phone'      => '0911111111',
                'position'   => 'manager',
            ],
            [
                'first_name' => 'سامر',
                'last_name'  => 'المصري',
                'email'      => 'staff1@company.com',
                'phone'      => '0922222222',
                'position'   => 'staff',
            ],
            [
                'first_name' => 'رنا',
                'last_name'  => 'الحسن',
                'email'      => 'staff2@company.com',
                'phone'      => '0933333333',
                'position'   => 'staff',
            ],
            [
                'first_name' => 'خالد',
                'last_name'  => 'عمر',
                'email'      => 'manager2@company.com',
                'phone'      => '0944444444',
                'position'   => 'manager',
            ],
        ];

        $branches = Branch::all();

        foreach ($employeesData as $index => $data) {
            $user = User::create([
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'location_id'       => $defaultLocationId,
                'gender'            => 'male',
                'type'              => 'employee',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
            ]);

            // توزيع الموظفين على الأفرع
            if ($branches->isNotEmpty()) {
                $assignedBranch = $branches[$index % $branches->count()];

                EmployeeBranch::create([
                    'employee_id' => $employee->id,
                    'branch_id'   => $assignedBranch->id,
                    'from_date'   => now()->subMonths(6)->toDateString(),
                    'to_date'     => null,
                    'position'    => $data['position'],
                ]);
            }
        }

        // 2. إنشاء زبائن لتجربة النظام
        $clientsData = [
            ['first_name' => 'عمر', 'last_name' => 'الشامي', 'email' => 'client1@gmail.com', 'points' => 150],
            ['first_name' => 'سارة', 'last_name' => 'الخالد', 'email' => 'client2@gmail.com', 'points' => 320],
        ];

        foreach ($clientsData as $clientInfo) {
            $clientUser = User::create([
                'first_name'        => $clientInfo['first_name'],
                'last_name'         => $clientInfo['last_name'],
                'email'             => $clientInfo['email'],
                'phone'             => '09555' . rand(10000, 99999),
                'location_id'       => $defaultLocationId,
                'gender'            => 'female',
                'type'              => 'client',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            Client::create([
                'user_id' => $clientUser->id,
                'points'  => $clientInfo['points'],
            ]);
        }
    }
}
