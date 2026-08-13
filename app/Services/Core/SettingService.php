<?php

namespace App\Services\Core;

use App\DAO\Core\SettingDAO;

class SettingService
{
    public function __construct(
        private SettingDAO $settingDAO
    ) {}

    public function getCompensationLimits(): array
    {
        return [
            'employee_compensation_limit' => (float) $this->settingDAO->getByKey('employee_compensation_limit', 50.00),
            'manager_compensation_limit'  => (float) $this->settingDAO->getByKey('manager_compensation_limit', 200.00),
        ];
    }

    public function updateCompensationLimits(array $data): void
    {
        if (isset($data['employee_compensation_limit'])) {
            $this->settingDAO->updateOrCreate('employee_compensation_limit', $data['employee_compensation_limit']);
        }

        if (isset($data['manager_compensation_limit'])) {
            $this->settingDAO->updateOrCreate('manager_compensation_limit', $data['manager_compensation_limit']);
        }
    }
}
