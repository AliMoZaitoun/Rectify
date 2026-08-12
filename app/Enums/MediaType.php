<?php

namespace App\Enums;

enum MediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case PANORAMA_360 = '360_panorama';
    case DOCUMENT = 'document';
}
