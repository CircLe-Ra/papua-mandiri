<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use function Livewire\Volt\{state, usesFileUploads};

usesFileUploads();
state([
    'name' => fn () => auth()->user()->name,
    'email' => fn () => auth()->user()->email,
]);
state('profile_photo_path');

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'profile_photo_path' => ['nullable', 'image', 'max:2048'],
        ]);

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }
    if ($this->profile_photo_path) {
        // Hapus foto profil lama jika ada
        if ($user->profile_photo_path) {
            \Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $this->profile_photo_path->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
    }
    $user->save();
    if ($this->profile_photo_path) {
        $this->dispatch('refresh-image', path: $path);
    }
    $this->dispatch('pond-reset');
    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>

<section >
    <header>
        <h2 class="text-lg font-medium text-gray-500 border-gray-200 dark:border-gray-700 dark:text-gray-400">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 items-center ">
        <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
            <div>
                <x-input-label for="name" :value="__('Name')" class="text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400" />
                <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400" />
                <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="profile_photo_path" :value="__('Profile Photo')" class="mb-2 text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400" />
                <x-form.filepond wire:model="profile_photo_path" />
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo_path')" />
            </div>

            <div class="flex items-center gap-4">
                <x-button color="blue" size="md" type="submit">{{ __('Save') }}</x-button>

                <x-action-message class="me-3 text-gray-600 dark:text-gray-400" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
        <div class="flex justify-center">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="Profile Photo" class="rounded-full w-40 h-40 object-cover" />
            @endif
        </div>
    </div>
</section>
