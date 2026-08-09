<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
    case EMPLOYEE = 'employee';
    case MANAGER = 'manager';
    case STUFF = 'staff';

    public static function values(): array

    {
        return array_column(self::cases(), 'value');
    }
}
