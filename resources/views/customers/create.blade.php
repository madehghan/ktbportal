<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
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

                    <!-- Company -->
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                            نام شرکت
                        </label>
                        <input id="company" 
                               type="text" 
                               name="company" 
                               value="{{ old('company') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('company') border-red-500 @enderror"
                               placeholder="نام شرکت را وارد کنید">
                        @error('company')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            آدرس
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('address') border-red-500 @enderror"
                                  placeholder="آدرس کامل را وارد کنید">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            یادداشت‌ها
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('notes') border-red-500 @enderror"
                                  placeholder="یادداشت‌های مربوط به مشتری را وارد کنید">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                            ذخیره مشتری
                        </button>
                        <a href="{{ route('customers.index') }}" 
                           class="flex-1 bg-white text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all border border-gray-300 font-medium text-center">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

