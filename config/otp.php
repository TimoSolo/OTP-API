<?php

return [

    
    'otp_per_hour' => env('OTP_PER_HOUR', 3),
    'otp_expires_seconds' => env('OTP_EXPIRE', 30), // in seconds
    'otp_resend_valid_minutes' => env('OTP_RESEND_VALID', 5), // in minutes
    'otp_resend_times' => env('OTP_RESEND_TIMES', 3), 


];
