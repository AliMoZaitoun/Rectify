<?php

namespace App\DAO\Core;

use App\Models\Core\Setting;

class SettingDAO
{
    public function getByKey(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function updateOrCreate(string $key, mixed $value): Setting
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function getAllAsArray(): array
    {
        return Setting::all()->pluck('value', 'key')->toArray();
    }
}
