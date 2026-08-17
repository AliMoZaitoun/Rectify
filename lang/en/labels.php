<?php

return [
    'compensation_type' => [
        'points' => 'Reward Points',
        'coupon' => 'Discount Coupon',
        'other'  => 'Other Compensation',
    ],
    'compensation_status' => [
        'pending'  => 'Pending',
        'granted'  => 'Granted',
        'rejected' => 'Rejected',
        'pending_approval'  => 'Pending approval'
    ],
    'complaint_priority' => [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ],
    'complaint_status' => [
        'pending'           => 'Pending',
        'in_progress'       => 'In Progress',
        'waiting_documents' => 'Waiting for Documents',
        'resolved'          => 'Resolved',
        'closed'            => 'Closed',
        'rejected'          => 'Rejected',
        'escalated'         => 'Escalated'
    ],

    'action_types' => [
        'complaint_submitted' => 'Complaint Submitted',
        'request_documents'  => 'Request new documents',
        'document_submitted' => 'Document submitted',
        'comment'            => 'Message',
        'message'            => 'Message',
    ],

    'ai_tone' => [
        'professional' => 'Professional & Formal',
        'empathetic'   => 'Empathetic & Understanding',
        'strict'       => 'Strict & Direct',
        'friendly'     => 'Friendly & Casual',
    ],
];
