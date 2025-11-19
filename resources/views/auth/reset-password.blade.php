<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 mb-2">تنظیم رمز عبور جدید</h2>
        <p class="text-sm text-gray-600">
            کد 6 رقمی ارسال شده و رمز عبور جدید خود را وارد کنید.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf

        <input type="hidden" name="mobile" value="{{ request('mobile') }}">

        <!-- Verification Code -->
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                کد تأیید
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                </div>
                <input id="code" 
                       type="text" 
                       name="code" 
                       maxlength="6"
                       class="block w-full pr-10 pl-3 py-3 border border-gray-300 rounded-xl text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all text-center text-2xl tracking-widest font-bold"
                       placeholder="------"
                       required 
                       autofocus>
            </div>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                رمز عبور جدید
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input id="password" 
                       type="password" 
                       name="password"
                       class="block w-full pr-10 pl-3 py-3 border border-gray-300 rounded-xl text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                       placeholder="حداقل 8 کاراکتر"
                       required>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                تکرار رمز عبور
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation"
                       class="block w-full pr-10 pl-3 py-3 border border-gray-300 rounded-xl text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                       placeholder="تکرار رمز عبور"
                       required>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-white font-medium py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
            <span>تغییر رمز عبور</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </button>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" 
               class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>بازگشت به صفحه ورود</span>
            </a>
        </div>
    </form>
</x-guest-layout>
