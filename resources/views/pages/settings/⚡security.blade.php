<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;

new #[Title('Security settings')] class extends Component implements HasForms {
    use InteractsWithForms;

    public ?array $passwordData = [];

    public bool $canManageTwoFactor;

    public bool $twoFactorEnabled;

    public bool $requiresConfirmation;

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $this->form->fill([]);
        
        $this->canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        if ($this->canManageTwoFactor) {
            if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
                $disableTwoFactorAuthentication(auth()->user());
            }

            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
            $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make('Alteração de Senha')
                    ->description('Certifique-se de que sua conta está usando uma senha longa e aleatória para se manter segura.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('Senha atual'))
                            ->password()
                            ->required()
                            ->currentPassword()
                            ->revealable(),
                        TextInput::make('password')
                            ->label(__('Nova senha'))
                            ->password()
                            ->required()
                            ->confirmed()
                            ->minLength(8)
                            ->revealable(),
                        TextInput::make('password_confirmation')
                            ->label(__('Confirme a nova senha'))
                            ->password()
                            ->required()
                            ->revealable(),
                    ])->columns(1)
            ])
            ->statePath('passwordData');
    }

    public function updatePassword(): void
    {
        $validated = $this->form->getState();

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->form->fill([]);

        Notification::make()
            ->title('Senha Alterada')
            ->success()
            ->send();

        $this->dispatch('password-updated');
    }

    /**
     * Handle the two-factor authentication enabled event.
     */
    #[On('two-factor-enabled')]
    public function onTwoFactorEnabled(): void
    {
        $this->twoFactorEnabled = true;
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Security settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Security')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            
            {{ $this->form }}

            <div class="flex items-center gap-4 mt-6">
                <div class="flex items-center justify-end">
                    <x-filament::button type="submit" data-test="update-password-button">
                        {{ __('Salvar Senha') }}
                    </x-filament::button>
                </div>
            </div>
        </form>

        @if ($canManageTwoFactor)
            <section class="mt-12">
                <flux:heading>{{ __('Two-factor authentication') }}</flux:heading>
                <flux:subheading>{{ __('Manage your two-factor authentication settings') }}</flux:subheading>

                <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="space-y-4">
                            <flux:text>
                                {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                            </flux:text>

                            <div class="flex justify-start">
                                <flux:button
                                    variant="danger"
                                    wire:click="disable"
                                >
                                    {{ __('Disable 2FA') }}
                                </flux:button>
                            </div>

                            <livewire:pages::settings.two-factor.recovery-codes :$requiresConfirmation />
                        </div>
                    @else
                        <div class="space-y-4">
                            <flux:text variant="subtle">
                                {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                            </flux:text>

                            <flux:modal.trigger name="two-factor-setup-modal">
                                <flux:button
                                    variant="primary"
                                    wire:click="$dispatch('start-two-factor-setup')"
                                >
                                    {{ __('Enable 2FA') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <livewire:pages::settings.two-factor-setup-modal :requires-confirmation="$requiresConfirmation" />
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </x-pages::settings.layout>
</section>
