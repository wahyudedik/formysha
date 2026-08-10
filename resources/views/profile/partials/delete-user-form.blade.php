<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('profile_form.delete_account_title') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('profile_form.delete_account_description') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('profile_form.delete_account_button') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4 sm:p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('profile_form.delete_account_confirm_title') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('profile_form.delete_account_confirm_description') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('profile_form.current_password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('profile_form.current_password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="min-h-[44px]">
                    {{ __('profile_form.cancel') }}
                </x-secondary-button>

                <x-danger-button class="sm:ms-3 min-h-[44px]">
                    {{ __('profile_form.delete_account_button') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
