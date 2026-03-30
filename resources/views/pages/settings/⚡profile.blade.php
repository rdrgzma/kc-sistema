<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

new #[Title('Profile settings')] class extends Component implements HasForms {
    use ProfileValidationRules;
    use InteractsWithForms;

    public ?array $data = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Nome'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label(__('E-mail'))
                    ->email()
                    ->required()
                    ->maxLength(255),
            ])
            ->statePath('data');
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->form->getState();

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->form->fill(['name' => $user->name, 'email' => $user->email]);

        Notification::make()
            ->title('Perfil Atualizado com Sucesso')
            ->success()
            ->send();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            {{ $this->form }}

            @if ($this->hasUnverifiedEmail)
                <div class="mt-4">
                    <p class="text-sm text-slate-800 dark:text-zinc-200">
                        {{ __('Your email address is unverified.') }}

                        <button type="button"
                            class="underline text-sm text-primary-600 hover:text-primary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4 mt-6">
                <div class="flex items-center justify-end">
                    <x-filament::button type="submit" data-test="update-profile-button">
                        {{ __('Salvar') }}
                    </x-filament::button>
                </div>
            </div>
        </form>


    </x-pages::settings.layout>
</section>