<?php

namespace App\Listeners;

use App\Events\OTPEvent;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class SendOTP
{
    public function handle(OTPEvent $event): void
    {
        Mail::to($event->email)->send(new OtpMail($event->otp));
    }
}
