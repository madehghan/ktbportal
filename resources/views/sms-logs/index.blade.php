<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">کل پیامک‌ها</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">ارسال شده</p>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['sent'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">ناموفق</p>
                            <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">در حال ارسال</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
                <form method="GET" action="{{ route('sms-logs.index') }}" class="flex flex-wrap gap-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="جستجو با شماره موبایل" 
                           class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>ارسال شده</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>ناموفق</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در حال ارسال</option>
                    </select>

                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">همه انواع</option>
                        <option value="welcome" {{ request('type') == 'welcome' ? 'selected' : '' }}>خوش‌آمدگویی</option>
                        <option value="verification" {{ request('type') == 'verification' ? 'selected' : '' }}>تأیید هویت</option>
                        <option value="pattern" {{ request('type') == 'pattern' ? 'selected' : '' }}>الگو</option>
                        <option value="webservice" {{ request('type') == 'webservice' ? 'selected' : '' }}>وب‌سرویس</option>
                    </select>

                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all border border-primary">
                        جستجو
                    </button>

                    @if(request()->anyFilled(['search', 'status', 'type']))
                        <a href="{{ route('sms-logs.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all border border-gray-300">
                            پاک کردن فیلتر
                        </a>
                    @endif
                </form>
            </div>

            <!-- SMS Logs Table -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">شناسه</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">گیرنده</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">پیام</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">وضعیت</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ارسال کننده</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاریخ</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($smsLogs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $log->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $log->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $log->to_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($log->message, 40) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($log->status === 'sent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                                ارسال شده
                                            </span>
                                        @elseif($log->status === 'failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                                ناموفق
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                در حال ارسال
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->user?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" dir="ltr">
                                        {{ $log->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('sms-logs.show', $log) }}" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all border border-transparent hover:border-blue-200"
                                           title="مشاهده جزئیات">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                            <p class="text-gray-500 text-sm">هیچ لاگ پیامکی یافت نشد</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($smsLogs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $smsLogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Send SMS Modal -->
    <div x-data="{ open: @js($errors->any()), recipientType: @js(old('recipient_type', 'user')), selectedUser: @js(old('user_id', '')) }" 
         @open-sms-modal.window="open = true"
         x-show="open" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="open = false"></div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click.away="open = false" 
                 class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-auto transform transition-all"
                 x-show="open"
                 x-transition>
                
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200" dir="rtl">
                    <h3 class="text-xl font-bold text-gray-900">ارسال پیامک جدید</h3>
                    <button @click="open = false" 
                            class="text-gray-400 hover:text-gray-600 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('sms-logs.send') }}" method="POST" dir="rtl" id="smsForm">
                    @csrf
                    <div class="p-6 space-y-4">
                        
                        <!-- Validation Errors -->
                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="font-medium text-red-800 mb-1">خطاهای فرم:</p>
                                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Recipient Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                نوع گیرنده <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" 
                                           name="recipient_type" 
                                           value="user" 
                                           x-model="recipientType"
                                           class="text-primary focus:ring-primary">
                                    <span class="text-sm">انتخاب از کاربران</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" 
                                           name="recipient_type" 
                                           value="manual" 
                                           x-model="recipientType"
                                           class="text-primary focus:ring-primary">
                                    <span class="text-sm">وارد کردن شماره</span>
                                </label>
                            </div>
                        </div>

                        <!-- Select User -->
                        <div x-show="recipientType === 'user'" x-cloak>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                انتخاب کاربر <span class="text-red-500">*</span>
                            </label>
                            <select id="user_id" 
                                    name="user_id" 
                                    x-model="selectedUser"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('user_id') border-red-500 @enderror">
                                <option value="">انتخاب کنید...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->mobile }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Manual Mobile Number -->
                        <div x-show="recipientType === 'manual'" x-cloak>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                                شماره موبایل <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="mobile" 
                                   name="mobile" 
                                   value="{{ old('mobile') }}"
                                   placeholder="09123456789"
                                   maxlength="11"
                                   pattern="[0-9]*"
                                   inputmode="numeric"
                                   @input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('mobile') border-red-500 @enderror"
                                   dir="ltr">
                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">شماره را با 09 شروع کنید</p>
                            @enderror
                        </div>

                        <!-- Message Content -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                متن پیامک <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="5"
                                      maxlength="500"
                                      required
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('message') border-red-500 @enderror"
                                      placeholder="متن پیامک خود را وارد کنید...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">حداکثر 500 کاراکتر</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl" dir="rtl">
                        <button type="button" 
                                @click="open = false"
                                class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all font-medium">
                            انصراف
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            ارسال پیامک
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // Auto-open modal if validation errors exist
        @if($errors->any())
            window.addEventListener('DOMContentLoaded', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        @endif
        
        // Debug form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('smsForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitting...');
                    console.log('Action:', form.action);
                    console.log('Method:', form.method);
                    
                    const formData = new FormData(form);
                    console.log('Form data:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key + ':', value);
                    }
                });
            }
        });
    </script>
</x-app-layout>

