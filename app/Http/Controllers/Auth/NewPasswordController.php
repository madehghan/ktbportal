<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'mobile.regex' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد.',
            'code.size' => 'کد تأیید باید 6 رقم باشد.',
        ]);

        // Verify the code
        $resetCode = \DB::table('password_reset_codes')
            ->where('mobile', $request->mobile)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetCode) {
            return back()->withInput($request->only('mobile'))
                ->withErrors(['code' => 'کد تأیید نامعتبر یا منقضی شده است.']);
        }

        // Find user by mobile
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return back()->withInput($request->only('mobile'))
                ->withErrors(['mobile' => 'کاربری با این شماره موبایل یافت نشد.']);
        }

        // Update password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Delete used code
        \DB::table('password_reset_codes')
            ->where('mobile', $request->mobile)
            ->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'رمز عبور شما با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.');
    }
}
