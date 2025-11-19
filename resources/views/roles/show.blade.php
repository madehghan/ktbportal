<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Role Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gradient-to-br from-primary to-primary/80 p-8 text-center">
                            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">{{ $role->display_name }}</h3>
                            <p class="text-primary-100 text-sm font-mono">{{ $role->name }}</p>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">شناسه</span>
                                <span class="text-sm font-bold text-gray-900">#{{ $role->id }}</span>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">تعداد کاربران</span>
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-primary/10 text-primary">
                                    {{ $role->users->count() }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-3">
                                <span class="text-sm font-medium text-gray-500">تعداد دسترسی‌ها</span>
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-green-100 text-green-700">
                                    {{ $role->permissions->count() }}
                                </span>
                            </div>
                        </div>

                        @if($role->description)
                            <div class="px-6 pb-6">
                                <p class="text-sm text-gray-600 text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    {{ $role->description }}
                                </p>
                            </div>
                        @endif

                        <div class="p-6 bg-gray-50 border-t border-gray-200 flex gap-3">
                            <a href="{{ route('roles.edit', $role) }}" 
                               class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span class="text-sm">ویرایش</span>
                            </a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="flex-1"
                                  onsubmit="return confirm('آیا از حذف این نقش اطمینان دارید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white text-red-600 rounded-lg hover:bg-red-50 transition-all border border-gray-200 hover:border-red-200 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span class="text-sm">حذف</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Permissions & Users -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Permissions List -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            دسترسی‌های این نقش
                        </h3>
                        @if($role->permissions->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($role->permissions as $permission)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $permission->display_name }}</p>
                                            <p class="text-xs text-gray-500 truncate font-mono">{{ $permission->name }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">هیچ دسترسی تعریف نشده است</p>
                            </div>
                        @endif
                    </div>

                    <!-- Users List -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            کاربران با این نقش
                        </h3>
                        @if($role->users->count() > 0)
                            <div class="space-y-3">
                                @foreach($role->users as $user)
                                    <a href="{{ route('users.show', $user) }}" class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-all">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-bold text-sm">{{ mb_substr($user->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $user->mobile }}</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">هیچ کاربری با این نقش وجود ندارد</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

