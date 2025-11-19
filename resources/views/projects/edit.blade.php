<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            نام پروژه <span class="text-red-500">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $project->name) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror"
                               placeholder="نام پروژه را وارد کنید"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            توضیحات
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('description') border-red-500 @enderror"
                                  placeholder="توضیحات پروژه را وارد کنید">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date_jalali" class="block text-sm font-medium text-gray-700 mb-2">
                                تاریخ شروع (شمسی) <span class="text-red-500">*</span>
                            </label>
                            <input id="start_date_jalali" 
                                   type="text" 
                                   name="start_date_jalali" 
                                   value="{{ old('start_date_jalali', $project->start_date_jalali) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('start_date_jalali') border-red-500 @enderror"
                                   placeholder="مثال: 1403/08/26"
                                   required
                                   autocomplete="off">
                            @error('start_date_jalali')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date_jalali" class="block text-sm font-medium text-gray-700 mb-2">
                                تاریخ پایان (شمسی) <span class="text-red-500">*</span>
                            </label>
                            <input id="end_date_jalali" 
                                   type="text" 
                                   name="end_date_jalali" 
                                   value="{{ old('end_date_jalali', $project->end_date_jalali) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('end_date_jalali') border-red-500 @enderror"
                                   placeholder="مثال: 1403/09/26"
                                   required
                                   autocomplete="off">
                            @error('end_date_jalali')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status and Budget -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                وضعیت <span class="text-red-500">*</span>
                            </label>
                            <select id="status" 
                                    name="status"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('status') border-red-500 @enderror"
                                    required>
                                <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>در انتظار</option>
                                <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>در حال انجام</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Budget -->
                        <div>
                            <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">
                                بودجه (تومان)
                            </label>
                            <input id="budget" 
                                   type="number" 
                                   name="budget" 
                                   value="{{ old('budget', $project->budget) }}"
                                   step="0.01"
                                   min="0"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('budget') border-red-500 @enderror"
                                   placeholder="0">
                            @error('budget')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Employees Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            کارمندان
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto @error('user_ids') border-red-500 @enderror">
                            @forelse($users as $user)
                                @php
                                    $isChecked = in_array($user->id, old('user_ids', $project->users->pluck('id')->toArray()));
                                @endphp
                                <div class="flex items-center gap-3 py-2 hover:bg-gray-50 px-2 rounded">
                                    <input type="checkbox" 
                                           name="user_ids[]" 
                                           value="{{ $user->id }}"
                                           id="user_{{ $user->id }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <label for="user_{{ $user->id }}" class="text-sm cursor-pointer flex-1">
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $user->mobile }}
                                            </span>
                                            @if($user->role)
                                                <span class="mx-1">•</span>
                                                <span>{{ $user->role->display_name }}</span>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">هیچ کارمندی یافت نشد</p>
                            @endforelse
                        </div>
                        <p class="mt-1 text-xs text-gray-500">می‌توانید چند کارمند یا هیچ کدام را انتخاب کنید</p>
                        @error('user_ids')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customers Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            مشتریان
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto @error('customer_ids') border-red-500 @enderror">
                            @forelse($customers as $customer)
                                @php
                                    $isChecked = in_array($customer->id, old('customer_ids', $project->customers->pluck('id')->toArray()));
                                @endphp
                                <div class="flex items-center gap-3 py-2 hover:bg-gray-50 px-2 rounded">
                                    <input type="checkbox" 
                                           name="customer_ids[]" 
                                           value="{{ $customer->id }}"
                                           id="customer_{{ $customer->id }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <label for="customer_{{ $customer->id }}" class="text-sm cursor-pointer flex-1">
                                        <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $customer->mobile }}
                                            </span>
                                            @if($customer->company)
                                                <span class="mx-1">•</span>
                                                <span>{{ $customer->company }}</span>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">هیچ مشتری یافت نشد</p>
                            @endforelse
                        </div>
                        <p class="mt-1 text-xs text-gray-500">می‌توانید چند مشتری یا هیچ کدام را انتخاب کنید</p>
                        @error('customer_ids')
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
                                  placeholder="یادداشت‌های مربوط به پروژه را وارد کنید">{{ old('notes', $project->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                            به‌روزرسانی پروژه
                        </button>
                        <a href="{{ route('projects.index') }}" 
                           class="flex-1 bg-white text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all border border-gray-300 font-medium text-center">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#start_date_jalali').persianDatepicker({
                initialValue: true,
                format: 'YYYY/MM/DD',
                autoClose: true,
                calendar: {
                    persian: {
                        locale: 'en'
                    }
                },
                observer: true,
                altField: '#start_date_jalali',
                altFormat: 'YYYY/MM/DD'
            });

            $('#end_date_jalali').persianDatepicker({
                initialValue: true,
                format: 'YYYY/MM/DD',
                autoClose: true,
                calendar: {
                    persian: {
                        locale: 'en'
                    }
                },
                observer: true,
                altField: '#end_date_jalali',
                altFormat: 'YYYY/MM/DD'
            });
        });
    </script>
    @endpush
</x-app-layout>

