<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            نام انگلیسی نقش <span class="text-red-500">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $role->name) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror"
                               placeholder="مثال: manager"
                               required>
                        <p class="mt-1 text-xs text-gray-500">از حروف انگلیسی کوچک و _ استفاده کنید</p>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                            نام نمایشی <span class="text-red-500">*</span>
                        </label>
                        <input id="display_name" 
                               type="text" 
                               name="display_name" 
                               value="{{ old('display_name', $role->display_name) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('display_name') border-red-500 @enderror"
                               placeholder="مثال: مدیر سیستم"
                               required>
                        @error('display_name')
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
                                  rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('description') border-red-500 @enderror"
                                  placeholder="توضیحات مربوط به این نقش">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div class="border-t border-gray-200 pt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            دسترسی‌ها
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($permissions as $permission)
                                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-all {{ $role->permissions->contains($permission->id) ? 'bg-primary/5 border-primary/30' : '' }}">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $permission->name }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('permissions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-all border border-primary font-medium">
                            به‌روزرسانی نقش
                        </button>
                        <a href="{{ route('roles.index') }}" 
                           class="flex-1 bg-white text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all border border-gray-300 font-medium text-center">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

