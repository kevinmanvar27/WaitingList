<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Mail\OtpMail;
use App\Models\User;

class OtpController extends Controller
{
    // Send OTP to email
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));
        $now = Carbon::now();

        // Find existing active (not used, not expired) OTP within 30 minutes
        $existing = DB::table('otps')
            ->where('email', $email)
            ->whereNull('used_at')
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->orderByDesc('created_at')
            ->first();

        $otp = null;
        $otpId = null;

        if ($existing) {
            // Resend same OTP within validity window
            $otp = $existing->otp;
            $otpId = $existing->id;
        } else {
            // Generate cryptographically secure 6-digit OTP
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpId = DB::table('otps')->insertGetId([
                'email' => $email,
                'otp' => $otp,
                'status' => 'generated',
                'created_at' => $now,
                'expires_at' => $now->copy()->addMinutes(30),
            ]);
        }

        try {
            Mail::to($email)->send(new OtpMail($otp, $email));
            // Mark as sent and update last_sent_at
            DB::table('otps')->where('id', $otpId)->update([
                'status' => 'sent',
                'last_sent_at' => $now,
            ]);
        } catch (\Exception $e) {
            \Log::error('Mail send failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP email. Please try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'data' => [
                'email' => $email,
                'valid_for_minutes' => 30,
            ],
        ]);
    }

    // Verify OTP and log user in
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ]);

        $email = strtolower(trim($request->email));
        $otp = $request->otp;
        $now = Carbon::now();

        // Find a matching, unexpired, unused OTP
        $record = DB::table('otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->whereNull('used_at')
            ->where('expires_at', '>=', $now)
            ->orderByDesc('created_at')
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'OTP is invalid or has expired.',
            ], 422);
        }

        // Invalidate OTP immediately upon successful verification
        DB::table('otps')->where('id', $record->id)->update([
            'used_at' => $now,
            'status' => 'used',
        ]);

        // Create or find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            $name = ucfirst(explode('@', $email)[0]);
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => $now,
            ]);
        }

        // Issue Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_picture' => $user->profile_picture,
                    'has_pin' => !is_null($user->pin ?? null),
                    'is_admin' => (bool) ($user->is_admin ?? false),
                ],
                'token' => $token,
            ],
        ]);
    }
}
