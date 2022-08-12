<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class OneTimeCode extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'resends',
    ];

    protected static function boot()
    {
        parent::boot();

        OneTimeCode::creating(function ($model) {
            $model->code = OneTimeCode::generateCode();
        });
    }

    public static function createOrReuseCode($email)
    {
        // check if can resend old one
        $otp =  OneTimeCode::where("email", $email)->latest()->first();
        if ($otp) {
            $resend_minutes = config('otp.otp_resend_valid_minutes');
            $valid_date = Carbon::now()->subMinutes($resend_minutes);
            // if valid time and resends:
            if ($otp->created_at >= $valid_date) {
                $resend_times = config('otp.otp_resend_times');
                if ($otp->resends < $resend_times) {
                    $otp->resends = $otp->resends + 1;
                    $otp->valid_from = Carbon::now();
                    $otp->save();
                    return $otp;
                } else {
                    // cant resend so many
                    throw new Exception("Resend limit of $resend_times in the last $resend_minutes minutes exceeded for " . $email . ".");
                }
            }
        }

        // check if allowed to request a new code?
        $otp_per_hour = config('otp.otp_per_hour');
        $valid_date = Carbon::now()->subHour();
        $otp_count =  OneTimeCode::where("email", $email)->where("valid_from", ">=", $valid_date)->count(); // get the latest code for this email address

        if ($otp_count >= $otp_per_hour) {
            throw new Exception("Code request limit of $otp_per_hour per hour exceeded for " . $email . ".");
        }

        // passed tests, create new code
        $otp = new OneTimeCode();
        $otp->email = $email;
        $otp->save();
        return $otp;
    }

    public static function generateCode()
    {
        // TODO: get all codes in one query...
        // TODO: prevent endless loop if all nums generated! :P
        $one_day_ago = Carbon::now()->subDays(1)->toDateTimeString();

        do {
            $code = substr("000000" . random_int(0, 999999), -6); // get the last 6 chars of zero padded number
        } while (
            OneTimeCode::where("code", $code)
            ->where("valid_from", ">=", $one_day_ago)
            ->exists()
        );

        return $code;
    }

    public static function getValidOTP($email, $code)
    {
        // 30 sec is very short, making 30 min for now.
        // TODO: get from config!
        $valid_for_x_sec = 30 * 60;
        $valid_date = Carbon::now()->subSeconds($valid_for_x_sec); // ->toDateTimeString();
        $otp =  OneTimeCode::where("email", $email)->latest()->first(); // get the latest code for this email address

        // check if otp exists
        if (!$otp) {
            throw new Exception("No OTP found");
        }

        // check if time valid
        if ($otp->valid_from < $valid_date) {
            throw new Exception("Code has expired.");
        }

        // check if code valid
        if ($otp->code != $code) {
            throw new Exception("Invalid Code.");
        }

        // check if used
        if ($otp->used_at) {
            throw new Exception("Code has already been used.");
        }

        // mark code as used to prevent reusing code.
        $otp->used_at = Carbon::now();
        $otp->save();

        return $otp;
    }
}
