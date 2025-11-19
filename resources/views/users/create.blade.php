<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            نام و نام خانوادگی <span class="text-red-500">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror"
                               placeholder="نام و نام خانوادگی را وارد کنید"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            شماره موبایل <span class="text-red-500">*</span>
                        </label>
                        <input id="mobile" 
                               type="tel" 
                               name="mobile" 
                               value="{{ old('mobile') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('mobile') border-red-500 @enderror"
                               placeholder="09123456789"
                               required>
                        @error('mobile')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            ایمیل
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('email') border-red-500 @enderror"
                               placeholder="example@example.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                            نقش کاربری
                        </label>
                        <select id="role_id" 
                                name="role_id"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('role_id') border-red-500 @enderror">
                            <option value="">انتخاب نقش</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            رمز عبور <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input id="password" 
                                   type="text" 
                                   name="password"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('password') border-red-500 @enderror"
                                   placeholder="حداقل 8 کاراکتر"
                                   required>
                            <button type="button"
                                    onclick="generateRandomPassword()"
                                    class="px-4 py-3 bg-gray-100 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200 transition-all whitespace-nowrap flex items-center gap-2"
                                    title="تولید رمز عبور تصادفی">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="text-sm font-medium">تولید خودکار</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            تکرار رمز عبور <span class="text-red-500">*</span>
                        </label>
                        <input id="password_confirmation" 
                               type="text" 
                               name="password_confirmation"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                               placeholder="تکرار رمز عبور"
                               required>
                    </div>

                    <script>
                        function generateRandomPassword() {
                            // Generate 8 character random password with numbers and letters
                            const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
                            let password = '';
                            for (let i = 0; i < 8; i++) {
                                password += chars.charAt(Math.floor(Math.random() * chars.length));
                            }
                            
                            // Set password in both fields
                            document.getElementById('password').value = password;
                            document.getElementById('password_confirmation').value = password;
                            
                            // Show notification
                            const notification = document.createElement('div');
                            notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
                            notification.innerHTML = `
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>رمز عبور تصادفی تولید شد</span>
                            `;
                            document.body.appendChild(notification);
                            
                            // Remove notification after 3 seconds
                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                        }
                    </script>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                            ذخیره کاربر
                        </button>
                        <a href="{{ route('users.index') }}" 
                           class="flex-1 bg-white text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all border border-gray-300 font-medium text-center">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

