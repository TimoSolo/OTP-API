<?php

namespace App\Http\Controllers;

use App\Models\OneTimeCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\OTP;
use PhpParser\Node\Stmt\TryCatch;

class OTPController extends Controller
{

    public function showVerify(Request $request)
    {
        $email = $request->email;
        $code = $request->code;
        return view('otp-verify', compact('email', 'code'));
    }
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
        $userExists = true; // User::where('email', $email)->first();
        if ($userExists) { // if no user found pretend its ok to prevent probing for valid email addresses
            try {
                $otp = new OneTimeCode();
                $otp->email = $email;
                $otp->save();
                // email OTP
                Notification::route('mail', $email)->notify(new OTP($otp));
            } catch (\Throwable $th) {
                return redirect()->route('otp.verify', compact('email'))->withErrors($th->getMessage());
            }
        }
        return redirect()->route('otp.verify', compact('email'))->with('status', 'code-sent');
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

        try {
            $otp = OneTimeCode::getValidOTP($email, $code);
            if ($otp) {
                return redirect()->route('otp.done');
            } else {
                return back()->withErrors(['Incorrect code or email.']);
            }
        } catch (\Throwable $th) {
            return redirect()->route('otp.verify', compact('email', 'code'))->withErrors($th->getMessage());
        }
    }
}
