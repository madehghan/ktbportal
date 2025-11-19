<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            حذف حساب کاربری
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            پس از حذف حساب کاربری، تمام اطلاعات و داده‌های شما به صورت دائمی حذف خواهند شد. قبل از حذف حساب، لطفاً هر اطلاعاتی که می‌خواهید نگه دارید را دانلود کنید.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >حذف حساب کاربری</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" dir="rtl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                آیا از حذف حساب کاربری خود اطمینان دارید؟
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                پس از حذف حساب کاربری، تمام اطلاعات و داده‌های شما به صورت دائمی حذف خواهند شد. لطفاً رمز عبور خود را وارد کنید تا حذف حساب را تأیید کنید.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="رمز عبور" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="رمز عبور"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    انصراف
                </x-secondary-button>

                <x-danger-button>
                    حذف حساب کاربری
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
