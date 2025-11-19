<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
        ], [
            'mobile.regex' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد.',
        ]);

        // Find user by mobile
        $user = \App\Models\User::where('mobile', $request->mobile)->first();
        
        if (!$user) {
            return back()->withInput($request->only('mobile'))
                ->withErrors(['mobile' => 'کاربری با این شماره موبایل یافت نشد.']);
        }

        // Generate 6-digit verification code
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete old codes for this mobile
        \DB::table('password_reset_codes')
            ->where('mobile', $request->mobile)
            ->delete();
        
        // Store new code (expires in 5 minutes)
        \DB::table('password_reset_codes')->insert([
            'mobile' => $request->mobile,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send SMS via IPPanel
        $smsService = new \App\Services\IPPanelService();
        $sent = $smsService->sendVerificationCode($request->mobile, $code);

        if ($sent) {
            // Redirect to reset password page with mobile number
            return redirect()->route('password.reset', ['mobile' => $request->mobile])
                ->with('status', 'کد بازیابی به شماره موبایل شما ارسال شد. کد تا 5 دقیقه معتبر است.');
        }

        return back()->withInput($request->only('mobile'))
            ->withErrors(['mobile' => 'خطا در ارسال پیامک. لطفاً دوباره تلاش کنید.']);
    }
}
