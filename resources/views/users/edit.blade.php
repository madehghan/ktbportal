<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Avatar Section -->
                    <div class="pb-6 border-b border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            تصویر پروفایل
                        </label>
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-primary flex items-center justify-center">
                                            <span class="text-white font-bold text-2xl">{{ mb_substr($user->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <input type="file" 
                                       id="avatar-input" 
                                       name="avatar"
                                       accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 file:cursor-pointer">
                                <p class="mt-2 text-xs text-gray-500">فرمت‌های مجاز: JPG, PNG, GIF. حداکثر اندازه: 5MB</p>
                                @error('avatar')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            نام و نام خانوادگی <span class="text-red-500">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
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
                               value="{{ old('mobile', $user->mobile) }}"
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
                               value="{{ old('email', $user->email) }}"
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
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">تغییر رمز عبور (اختیاری)</h3>
                        <p class="text-xs text-gray-500 mb-4">اگر می‌خواهید رمز عبور را تغییر دهید، فیلدهای زیر را پر کنید. در غیر این صورت خالی بگذارید.</p>
                        
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                رمز عبور جدید
                            </label>
                            <input id="password" 
                                   type="password" 
                                   name="password"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('password') border-red-500 @enderror"
                                   placeholder="حداقل 8 کاراکتر">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                تکرار رمز عبور جدید
                            </label>
                            <input id="password_confirmation" 
                                   type="password" 
                                   name="password_confirmation"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                   placeholder="تکرار رمز عبور">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                            به‌روزرسانی کاربر
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

