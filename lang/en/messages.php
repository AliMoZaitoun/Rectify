<?php

return [
    'common' => [
        'success' => 'Operation completed successfully.',
        'failed' => 'The operation failed.',
        'error' => 'An error occurred. Please try again later.',
        'resource' => 'Resource',
        'not_found_item' => 'Sorry, :item not found.',
        'deleted' => 'Deleted successfully.',
        'stored' => 'Stored successfully.',
        'updated' => 'Updated successfully.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],
    'auth' => [
        'login' => 'Login successful! Welcome aboard.',
        'logout' => 'Logout successful! See you later.',
        'invalid' => 'Invalid email or password.',
        'password_invalid' => 'Invalid password',

        'password_changed' => 'Password changed successfully.',
        'email_verified' => 'Email verified successfully.',
        'already_verified' => 'Email already verified!.',


        'inactive' => 'Your account is not activated yet. Please check your email or enter the OTP.',
        'otp_sent' => 'OTP has been sent to your email.',
        'otp_failed' => 'Failed to send OTP. Please try again.',
        'otp_verified' => 'OTP verified successfully.',
        'otp_invalid' => 'Invalid or expired OTP.',

        'invalid_refresh_token' => 'Session expired. Please log in again.',
    ],

    'system' => [
        'validation' => 'The given data was invalid.',
        'db_error' => 'A system error occurred. Please contact support with code: :trace_id',
        'no_results' => 'No results found.',
    ],

    'branches' => [
        'already_has_manager' => 'Sorry, this branch already has a manager. Please reassign the current manager before assigning a new one.',
    ],

    'complaint' => [
        'complaint_not_found' => 'The requested complaint was not found.',
        'login_to_claim_reward'            => 'You have a pending compensation reward! Log in or create an account to claim your points.',
        'reveal_identity_to_claim_reward' => 'You have a pending compensation reward! Please unlink the anonymous status from your complaint to claim points to your account.',
        'cannot_compensate_unresolved_complaint' => 'Cannot add compensation to an unresolved complaint.',
        'already_compensated'              => 'This complaint has already been compensated.',
        'cannot_delete_granted_compensation' => 'Cannot delete a compensation that has already been granted.',
        'device_id_required'               => 'Device ID header (X-Device-ID) is required for synchronization.',
        'synced_successfully'              => 'Complaints synced successfully.',
        'action_added'                     => 'Action added successfully.',

        'cannot_rate_unresolved' => 'You cannot rate a complaint that is not resolved yet.',
        'already_rated'          => 'You have already rated the resolution of this complaint.',
        'rated_successfully'     => 'Thank you! Your rating has been submitted successfully.',

        'cannot_reopen'         => 'You can only reopen complaints that have been resolved.',
        'reopened_successfully' => 'Complaint has been reopened successfully and will be reviewed.',

        'report_generated_successfully' => 'Complaints and performance report generated successfully.',

        'cannot_modify_merged_complaint' => 'Cannot modify a merged complaint. Please manage it via the parent complaint or unmerge it first.',
        'cannot_merge_parent_complaint' => 'Cannot merge a complaint that already has child complaints attached to it.',

        'merged_successfully'   => 'Complaints merged successfully.',
        'unmerged_successfully' => 'Complaint unmerged successfully.',

        'status_updated'        => 'Complaint status has been changed successfully',

        'history' => [
            'merged_internal'   => 'This complaint was internally merged to link with a similar issue.',
            'unmerged_internal' => 'This complaint was unmerged and reopened as an independent request.',
            'auto_resolved'     => 'The issue has been resolved and its resolution documented by the specialized team.',

            'sla_escalated_internal' => 'Complaint automatically escalated to branch manager due to SLA breach.',

            'auto_status_change_request_documents'  => 'Auto status change: Documents requested from client.',
            'auto_status_change_document_submitted' => 'Auto status change: Documents submitted by client.',
            'auto_status_change_comment'            => 'Auto status change: New message.',

            'auto_updated_parent' => 'Auto-updated via parent complaint :tracking_code',

            'reopened' => 'Complaint Reopened by customer.',
            'escalated' => 'The complaint has been escalated to the branch manager',
        ],
        'max_reopens_reached' => 'Sorry, you have reached the maximum allowed limit for reopening this complaint. Please contact customer service for further assistance.',
    ],

    'core' => [
        'employee_required' => 'Invalid permission, you must be an employee to perform this action.',
    ],

    'client' => [
        'client_not_found'             => 'Client not found.',
        'insufficient_points'          => 'Insufficient points balance to complete the redemption.',
        'points_redeemed_successfully' => 'Points redeemed successfully.',
    ],

    'compensations' => [
        'invalid_coupon'          => 'The coupon is invalid or does not exist.',
        'coupon_not_granted'      => 'This coupon has not been granted for redemption.',
        'coupon_already_redeemed' => 'Sorry, this coupon has already been redeemed.',
        'redeemed_successfully'   => 'Coupon redeemed successfully.',
        'client_required' => 'A client must be specified when the compensation is not linked to a complaint.',
    ],

    'ai' => [
        'disabled' => 'The AI assistant is disabled or not configured by management.',
        'generation_failed' => 'An error occurred while generating the AI response. Please try again later.',
        'generated_successfully' => 'Response generated successfully.',
        'connection_failed' => 'Failed to connect to the AI service. Please check your network connection or try again later.',
        'parsing_failed' => 'An unexpected error occurred while parsing the AI output. Please try again.',

        'no_complaints_for_report' => 'No complaints found matching the criteria for analysis.',
        'report_generated'         => 'Institutional analysis report generated successfully.',
        'reports_retrieved' => 'Reports retrieved successfully.',
        'report_retrieved'  => 'Report details retrieved successfully.',
        'report_not_found'  => 'The requested report was not found.',
    ],
];
