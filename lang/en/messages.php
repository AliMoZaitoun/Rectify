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

    'sentences' => [
        'wrong_start_date' => 'The start date must be after or equal to the project start date: :date',

        'device_mismatch' => 'Access denied. This account is linked to another device.',
        'out_of_geofence' => 'You are outside the project geographical boundary. Distance: :distance meters.',
    ],

    'favorites' => [
        'already_exists' => 'This unit is already in your favorites.',
    ],

    'orders' => [
        'already_submitted' => 'You have already submitted an order for this unit or service.',
    ],

    'project' => [
        'project_has_no_buildings' => 'The selected project does not contain any buildings. Engineer allocation cannot be completed.',
    ],

    'unit' => [
        'invalid_price' => 'Price couldn\'t be negative'
    ],

    'attendance' => [
        'outside_geofence'          => 'You are outside the permitted geographical boundaries. You are currently :distance meters away from the site.',
        'already_checked_in'        => 'You have already recorded your attendance for today at :time.',
        'different_device'          => 'This device is not registered to your account. Please contact the administration to authorize this new device.',
        'building_required'         => 'The building (building_id) must be specified to determine the exact location of attendance.',
        'not_checked_in_yet'        => 'No active check-in record found for this session to complete the check-out.',
        'low_gps_accuracy'          => 'GPS signal is weak (Accuracy: :current meters). The system requires an accuracy better than :required meters. Please move to an open area.',
        'mock_location_detected'    => 'Attendance rejected! The use of fake location applications (Mock Location) is strictly prohibited.',
        'shift_timeout'             => 'You forgot to check out from your previous shift on :date. Please contact management to adjust your hours.',
        'building_project_mismatch' => 'The selected building does not belong to the specified project. Please verify your selection.',
        'before_project_start'      => 'Cannot record attendance because the project has not officially started yet (Project start date: :date).',
        'future_time_detected'      => 'Attendance rejected! Please set your phone time and date to automatic (Future time detected).',
        'offline_sync_expired'      => 'Sorry, this attendance cannot be synced because it is too old and exceeded the maximum allowed offline period of :days days.',
        'invalid_checkout_time'     => 'Invalid operation. Check-out time cannot be before check-in time.',
    ],

    'appointment' => [
        'done'      => 'The appointment has been finished sucessfully.',
        'booked'    => 'The appointment has been booked sucessfully.',
        'cancelled' => 'The appointment has been cancelled sucessfully.',

        'cannot_complete_future_appointment' => 'You cannot compoete a future appointment!',
    ],

    'chat' => [
        'room_not_found' => 'The requested chat room was not found in the system.',
        'unauthorized_access' => 'You are not authorized to access this chat room.',
    ],
];
