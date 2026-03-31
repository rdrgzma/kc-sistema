<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">

        {{-- Seletor de Tema — lê/escreve diretamente no Alpine Store global --}}
        <div class="flex gap-3 flex-wrap">

            {{-- Light --}}
            <button @click="$store.theme.set('light')"
                    :class="$store.theme.mode === 'light'
                        ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-2 ring-primary-500/30'
                        : 'border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 hover:border-slate-300 dark:hover:border-zinc-600 hover:bg-slate-50 dark:hover:bg-zinc-800'"
                    class="flex items-center gap-2.5 px-5 py-3 rounded-2xl border text-sm font-bold transition-all focus:outline-none active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ __('Light') }}
                <span x-show="$store.theme.mode === 'light'" class="w-2 h-2 rounded-full bg-primary-500 ml-1" x-cloak></span>
            </button>

            {{-- Dark --}}
            <button @click="$store.theme.set('dark')"
                    :class="$store.theme.mode === 'dark'
                        ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-2 ring-primary-500/30'
                        : 'border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 hover:border-slate-300 dark:hover:border-zinc-600 hover:bg-slate-50 dark:hover:bg-zinc-800'"
                    class="flex items-center gap-2.5 px-5 py-3 rounded-2xl border text-sm font-bold transition-all focus:outline-none active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                {{ __('Dark') }}
                <span x-show="$store.theme.mode === 'dark'" class="w-2 h-2 rounded-full bg-primary-500 ml-1" x-cloak></span>
            </button>

            {{-- System --}}
            <button @click="$store.theme.set('system')"
                    :class="$store.theme.mode === 'system'
                        ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-2 ring-primary-500/30'
                        : 'border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 hover:border-slate-300 dark:hover:border-zinc-600 hover:bg-slate-50 dark:hover:bg-zinc-800'"
                    class="flex items-center gap-2.5 px-5 py-3 rounded-2xl border text-sm font-bold transition-all focus:outline-none active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ __('System') }}
                <span x-show="$store.theme.mode === 'system'" class="w-2 h-2 rounded-full bg-primary-500 ml-1" x-cloak></span>
            </button>
        </div>

        <p class="mt-4 text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest">
            A preferência é salva no navegador e aplicada imediatamente.
        </p>

    </x-pages::settings.layout>
</section>
