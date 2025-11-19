<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <!-- Header with Avatar -->
                <div class="bg-gradient-to-br from-primary to-primary/80 p-8 text-center">
                    <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-primary font-bold text-3xl">{{ mb_substr($user->name, 0, 2) }}</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ $user->name }}</h3>
                    <p class="text-primary-100 text-sm">عضویت از {{ $user->created_at->format('Y/m/d') }}</p>
                </div>

                <!-- User Details -->
                <div class="p-6 space-y-4">
                    <!-- ID -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">شناسه کاربری</span>
                        <span class="text-sm font-bold text-gray-900">#{{ $user->id }}</span>
                    </div>

                    <!-- Mobile -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">شماره موبایل</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ $user->mobile }}</span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">ایمیل</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ $user->email ?? '—' }}</span>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">تاریخ ثبت‌نام</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('Y/m/d H:i:s') }}</span>
                        </div>
                    </div>

                    <!-- Updated At -->
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm font-medium text-gray-500">آخرین به‌روزرسانی</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('Y/m/d H:i:s') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex gap-3">
                    <a href="{{ route('users.edit', $user) }}" 
                       class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="text-sm">ویرایش کاربر</span>
                    </a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('آیا از حذف این کاربر اطمینان دارید؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white text-red-600 rounded-lg hover:bg-red-50 transition-all border border-gray-200 hover:border-red-200 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span class="text-sm">حذف کاربر</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

