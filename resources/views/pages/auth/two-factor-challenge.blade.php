<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Autenticação em duas etapas') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('img/background.jpg') }}');">
    <div class="w-full max-w-md p-10 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md rounded-[2.5rem] shadow-2xl border border-white/20">
        <div class="flex flex-col gap-8">
            <div class="flex justify-center -mt-4">
                <div class="p-4 bg-white rounded-3xl shadow-sm border border-slate-100">
                    <img src="{{ asset('img/logo-kc.jpeg') }}" alt="Logo KC" class="h-24 w-auto object-contain">
                </div>
            </div>

            <div class="flex flex-col gap-8" x-data="{
                    showRecoveryInput: {{ $errors->has('recovery_code') ? 'true' : 'false' }},
                    code: '',
                    recovery_code: '',
                    toggleInput() {
                        this.showRecoveryInput = !this.showRecoveryInput;
                        this.code = '';
                        this.recovery_code = '';
                        this.$nextTick(() => {
                            this.showRecoveryInput
                                ? this.$refs.recovery_code?.focus()
                                : this.$refs.code?.focus();
                        });
                    },
                }">
                <div x-show="!showRecoveryInput" class="text-center">
                    <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">
                        {{ __('Autenticação') }}
                    </h1>
                    <p class="mt-3 text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-normal px-4">
                        {{ __('Digite o código fornecido pelo seu aplicativo autenticador.') }}
                    </p>
                </div>

                <div x-show="showRecoveryInput" class="text-center">
                    <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">
                        {{ __('Recuperação') }}
                    </h1>
                    <p class="mt-3 text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-normal px-4">
                        {{ __('Confirme o acesso digitando um de seus códigos de recuperação.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('two-factor.login.store') }}">
                    @csrf

                    <div class="flex flex-col gap-8">
                        <div x-show="!showRecoveryInput">
                            <label for="code" class="block text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest ml-1 mb-2">
                                {{ __('Código OTP') }}
                            </label>
                            <input type="text" name="code" id="code" x-ref="code" x-model="code" required
                                autocomplete="one-time-code" placeholder="000000"
                                class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 transition-all font-bold text-sm tracking-[0.5em] text-center">
                            @error('code')
                                <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 ml-1 text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="showRecoveryInput">
                            <label for="recovery_code" class="block text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest ml-1 mb-2">
                                {{ __('Código de Reserva') }}
                            </label>
                            <input type="text" name="recovery_code" id="recovery_code" x-ref="recovery_code"
                                x-model="recovery_code" required autocomplete="one-time-code" placeholder="abcd-1234"
                                class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 transition-all font-bold text-sm text-center tracking-widest">
                            @error('recovery_code')
                                <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 ml-1 text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full px-6 py-4 text-slate-950 bg-amber-400 hover:bg-amber-500 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                            {{ __('Verificar Código') }}
                        </button>
                    </div>

                    <div class="mt-8 text-sm text-center font-bold text-slate-500">
                        <span class="opacity-60">{{ __('ou você pode') }}</span>
                        <button type="button" @click="toggleInput()"
                            class="ml-2 font-black text-amber-600 hover:text-amber-700 dark:text-amber-400 underline decoration-amber-500/30 underline-offset-4">
                            <span x-show="!showRecoveryInput">{{ __('usar código de recuperação') }}</span>
                            <span x-show="showRecoveryInput">{{ __('usar aplicativo autenticador') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</body>

</html>