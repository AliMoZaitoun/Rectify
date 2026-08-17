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
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            return;
        }

        // قائمة أسماء راكزة ومجهزة للمدراء والموظفين
        $managersList = [
            ['first_name' => 'أحمد', 'last_name' => 'العلي'],
            ['first_name' => 'خالد', 'last_name' => 'العمر'],
            ['first_name' => 'طارق', 'last_name' => 'السيد'],
            ['first_name' => 'محمود', 'last_name' => 'الراشد'],
            ['first_name' => 'ماهر', 'last_name' => 'الحكيم'],
        ];

        $staffList = [
            // مجموعة للفرع الأول
            [
                ['first_name' => 'سامر', 'last_name' => 'المصري', 'gender' => 'male'],
                ['first_name' => 'رنا', 'last_name' => 'الحسن', 'gender' => 'female'],
                ['first_name' => 'عمر', 'last_name' => 'النابلسي', 'gender' => 'male'],
            ],
            // مجموعة للفرع الثاني
            [
                ['first_name' => 'زياد', 'last_name' => 'الشامي', 'gender' => 'male'],
                ['first_name' => 'نور', 'last_name' => 'الخطيب', 'gender' => 'female'],
                ['first_name' => 'باسم', 'last_name' => 'الحداد', 'gender' => 'male'],
            ],
            // مجموعة للفرع الثالث
            [
                ['first_name' => 'كريم', 'last_name' => 'الفارس', 'gender' => 'male'],
                ['first_name' => 'سارة', 'last_name' => 'النجار', 'gender' => 'female'],
                ['first_name' => 'حسام', 'last_name' => 'الصالح', 'gender' => 'male'],
            ],
        ];

        foreach ($branches as $bIndex => $branch) {

            $managerName = $managersList[$bIndex % count($managersList)];

            $managerUser = User::create([
                'first_name'        => $managerName['first_name'],
                'last_name'         => $managerName['last_name'],
                'email'             => "manager.branch{$branch->id}@company.com",
                'phone'             => '091' . str_pad((string)$branch->id, 7, '0', STR_PAD_LEFT),
                'location_id'       => $defaultLocationId,
                'gender'            => 'male',
                'type'              => 'employee',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            $managerEmployee = Employee::create([
                'user_id' => $managerUser->id,
            ]);

            EmployeeBranch::create([
                'employee_id' => $managerEmployee->id,
                'branch_id'   => $branch->id,
                'from_date'   => now()->subMonths(6)->toDateString(),
                'to_date'     => null,
                'position'    => 'manager',
            ]);

            $managerUser->assignRole('manager');

            $currentBranchStaff = $staffList[$bIndex % count($staffList)];

            foreach ($currentBranchStaff as $sIndex => $staffData) {
                $staffNumber = $sIndex + 1;

                $staffUser = User::create([
                    'first_name'        => $staffData['first_name'],
                    'last_name'         => $staffData['last_name'],
                    'email'             => "staff{$staffNumber}.branch{$branch->id}@company.com",
                    'phone'             => "092{$branch->id}0000{$staffNumber}",
                    'location_id'       => $defaultLocationId,
                    'gender'            => $staffData['gender'],
                    'type'              => 'employee',
                    'password'          => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);

                $staffEmployee = Employee::create([
                    'user_id' => $staffUser->id,
                ]);

                EmployeeBranch::create([
                    'employee_id' => $staffEmployee->id,
                    'branch_id'   => $branch->id,
                    'from_date'   => now()->subMonths(3)->toDateString(),
                    'to_date'     => null,
                    'position'    => 'staff',
                ]);

                $staffUser->assignRole('staff');
            }
        }

        $clientsData = [
            ['first_name' => 'عبد الله', 'last_name' => 'الرفاعي', 'email' => 'client1@gmail.com', 'points' => 150, 'gender' => 'male'],
            ['first_name' => 'مريم', 'last_name' => 'المنصوري', 'email' => 'client2@gmail.com', 'points' => 320, 'gender' => 'female'],
        ];

        foreach ($clientsData as $clientInfo) {
            $clientUser = User::create([
                'first_name'        => $clientInfo['first_name'],
                'last_name'         => $clientInfo['last_name'],
                'email'             => $clientInfo['email'],
                'phone'             => '09555' . rand(10000, 99999),
                'location_id'       => $defaultLocationId,
                'gender'            => $clientInfo['gender'],
                'type'              => 'client',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            Client::create([
                'user_id' => $clientUser->id,
                'points'  => $clientInfo['points'],
            ]);
            $clientUser->assignRole(['client']);
        }
    }
}
