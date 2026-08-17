<?php

return [
    'compensation_type' => [
        'points' => 'نقاط مكافأة',
        'coupon' => 'كوبون خصم',
        'other'  => 'تعويض آخر',
    ],
    'compensation_status' => [
        'pending'  => 'قيد الانتظار',
        'granted'  => 'تم الصرف',
        'rejected' => 'مرفوض',
        'pending_approval'  => 'بانتظار الموافقة'

    ],
    'complaint_priority' => [
        'low'    => 'منخفضة',
        'medium' => 'متوسطة',
        'high'   => 'عالية',
        'urgent' => 'طارئة',
    ],
    'complaint_status' => [
        'pending'           => 'قيد الانتظار',
        'in_progress'       => 'قيد المعالجة',
        'waiting_documents' => 'بانتظار الوثائق',
        'resolved'          => 'محلولة',
        'closed'            => 'مغلقة',
        'rejected'          => 'مرفوضة',
        'escalated'         => 'مُصَعَّدة',
    ],

    'action_types' => [
        'complaint_submitted' => 'تم تقديم الشكوى.',
        'request_documents'  => 'طلب وثائق إضافية',
        'document_submitted' => 'تم تقديم الوثائق',
        'message'            => 'رسالة',
    ],

    'ai_tone' => [
        'professional' => 'رسمية واحترافية',
        'empathetic'   => 'متعاطفة ومتفهمة',
        'strict'       => 'حازمة ومباشرة',
        'friendly'     => 'ودودة ومريحة',
    ],
];
