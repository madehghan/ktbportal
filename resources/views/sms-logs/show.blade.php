<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <!-- Status Header -->
                <div class="p-6 {{ $smsLog->status === 'sent' ? 'bg-green-50' : ($smsLog->status === 'failed' ? 'bg-red-50' : 'bg-yellow-50') }} border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($smsLog->status === 'sent')
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-green-900">ارسال موفق</h3>
                                    <p class="text-sm text-green-700">پیامک با موفقیت ارسال شد</p>
                                </div>
                            @elseif($smsLog->status === 'failed')
                                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-red-900">ارسال ناموفق</h3>
                                    <p class="text-sm text-red-700">خطا در ارسال پیامک</p>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-yellow-900">در حال ارسال</h3>
                                    <p class="text-sm text-yellow-700">پیامک در صف ارسال است</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">شناسه</span>
                        <span class="text-sm font-bold text-gray-900">#{{ $smsLog->id }}</span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">نوع پیامک</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                            {{ $smsLog->type }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">شماره گیرنده</span>
                        <span class="text-sm font-mono font-medium text-gray-900">{{ $smsLog->to_number }}</span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">شماره فرستنده</span>
                        <span class="text-sm font-mono font-medium text-gray-900">{{ $smsLog->from_number }}</span>
                    </div>

                    @if($smsLog->message)
                        <div class="py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500 block mb-2">متن پیامک</span>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $smsLog->message }}</p>
                            </div>
                        </div>
                    @endif

                    @if($smsLog->message_outbox_id)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">شناسه پیامک IPPanel</span>
                            <span class="text-sm font-mono font-medium text-gray-900">{{ $smsLog->message_outbox_id }}</span>
                        </div>
                    @endif

                    @if($smsLog->error_message)
                        <div class="py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500 block mb-2">پیام خطا</span>
                            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                                <p class="text-sm text-red-900">{{ $smsLog->error_message }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">ارسال کننده</span>
                        <span class="text-sm font-medium text-gray-900">{{ $smsLog->user?->name ?? '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">تاریخ ایجاد</span>
                        <span class="text-sm font-medium text-gray-900" dir="ltr">{{ $smsLog->created_at->format('Y/m/d H:i:s') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm font-medium text-gray-500">آخرین به‌روزرسانی</span>
                        <span class="text-sm font-medium text-gray-900" dir="ltr">{{ $smsLog->updated_at->format('Y/m/d H:i:s') }}</span>
                    </div>

                    @if($smsLog->response)
                        <div class="py-3">
                            <span class="text-sm font-medium text-gray-500 block mb-2">پاسخ API</span>
                            <details class="group">
                                <summary class="cursor-pointer list-none p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all">
                                    <span class="text-sm font-medium text-gray-700">مشاهده پاسخ کامل API</span>
                                </summary>
                                <div class="mt-2 p-4 bg-gray-900 rounded-lg border border-gray-700 overflow-x-auto">
                                    <pre class="text-xs text-green-400 font-mono">{{ json_encode(json_decode($smsLog->response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </details>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('sms-logs.index') }}" 
                       class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-all border border-gray-300 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="text-sm">بازگشت به لیست</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

