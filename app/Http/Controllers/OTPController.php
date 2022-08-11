<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\OTP;

class OTPController extends Controller
{
    /**
     * Send a new OTP notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendOTP(Request $request)
    {
        $email = $request->email;

        // OPTIONAL: check if user exists before 
        $userExists = User::where('email', $email)->first();
        if ($userExists) { // if no user found pretend its ok to prevent probing for valid email addresses

            // TODO: generate OTP
            $otp = "123456";

            // email OTP
            Notification::route('mail', $email)->notify(new OTP($otp, $email));
        }
        return redirect()->route('otp.verify');
    }

    /**
     * Send a new OTP notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOTP(Request $request)
    {
        $email = $request->email;
        $code = $request->code;

        return redirect()->route('otp.send');
    }
}
