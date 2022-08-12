<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class OneTimeCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
    ];

    protected static function boot()
    {
        parent::boot();

        OneTimeCode::creating(function ($model) {
            // check if allowed to request a new code?
            // TODO: config
            $valid_for_x_per_hour = 3;
            $valid_date = Carbon::now()->subHour();
            $otp_count =  OneTimeCode::where("email", $model->email)->where("created_at", ">=", $valid_date)->count(); // get the latest code for this email address

            if ($otp_count >= $valid_for_x_per_hour) {
                throw new Exception("Code request limit of $valid_for_x_per_hour per hour exceeded for " . $model->email . ".");
            }
            $model->code = OneTimeCode::generateCode();
        });
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
            ->where("created_at", ">=", $one_day_ago)
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
        if ($otp->created_at < $valid_date) {
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
