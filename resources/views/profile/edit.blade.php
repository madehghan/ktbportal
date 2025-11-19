<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm"
                 x-data="{ 
                     activeTab: window.location.hash ? window.location.hash.substring(1) : 'profile',
                     changeTab(tab) {
                         this.activeTab = tab;
                         window.location.hash = tab;
                     }
                 }"
                 x-init="
                     window.addEventListener('hashchange', () => {
                         if (window.location.hash) {
                             activeTab = window.location.hash.substring(1);
                         }
                     });
                 ">
                <!-- Header -->
                <div class="bg-gradient-to-br from-primary to-primary/80 py-6 px-8">
                    <div class="flex items-center gap-4" dir="rtl">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0 overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-primary font-bold text-2xl">{{ mb_substr($user->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">{{ $user->name }}</h3>
                            <p class="text-primary-100 text-sm">{{ $user->mobile }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs Header -->
                <div class="px-6 pt-4 border-b border-gray-200" dir="rtl">
                    <div class="flex flex-wrap gap-2">
                        <button @click="changeTab('profile')"
                                :class="activeTab === 'profile' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition">
                            اطلاعات و ویرایش
                        </button>
                        <button @click="changeTab('login-logs')"
                                :class="activeTab === 'login-logs' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition">
                            لاگ ورود
                        </button>
                    </div>
                </div>

                <!-- Profile Tab -->
                <div class="p-6 space-y-6" x-show="activeTab === 'profile'" x-cloak>
                    <!-- Avatar Upload Section -->
                    <div class="border-b border-gray-200 pb-6">
                        @include('profile.partials.update-avatar-form')
                    </div>

                    <!-- Profile Information Form -->
                    <div>
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Update Password Form -->
                    <div class="border-t border-gray-200 pt-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Login Logs Tab -->
                <div class="p-6" x-show="activeTab === 'login-logs'" x-cloak>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">تاریخچه ورود</h3>
                        <p class="text-sm text-gray-600">لیست تمام ورودهای شما به سیستم</p>
                    </div>

                    @if($loginLogs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            شماره موبایل
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            آدرس IP
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            مرورگر
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            تاریخ و زمان ورود
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($loginLogs as $log)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-900">{{ $log->mobile }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $log->ip_address ?? '—' }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-sm text-gray-900" title="{{ $log->user_agent }}">
                                                    {{ Str::limit($log->user_agent ?? '—', 50) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-900">{{ $log->login_at->format('Y/m/d H:i:s') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $loginLogs->links() }}
                        </div>
                    @else
                        <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm">هنوز لاگ ورودی ثبت نشده است</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
