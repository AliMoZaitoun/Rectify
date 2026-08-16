<?php

namespace App\Notifications\V1\Client;

use App\Notifications\BaseNotification;

class PointsRedeemedNotification extends BaseNotification
{
    public function __construct(int $points, string $employeeName)
    {
        $title = __('messages.clients.points_redeemed_title', ['default' => 'تم خصم نقاط من رصيدك']);
        $body = __('messages.clients.points_redeemed_body', [
            'points' => $points,
            'employee' => $employeeName,
            'default' => "تم خصم {$points} نقطة من رصيدك في الفرع بواسطة الموظف {$employeeName}."
        ]);

        parent::__construct(
            title: $title,
            body: $body,
            data: ['type' => 'points_redeemed', 'deducted_points' => $points]
        );
    }
}
