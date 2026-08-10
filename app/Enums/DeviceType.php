<?php

namespace App\Enums;

enum DeviceType: string
{
    case ANDROID = 'android';
    case IOS     = 'ios';
    case WEB     = 'web';

    // case SMART_TV = 'smart_tv';
}
