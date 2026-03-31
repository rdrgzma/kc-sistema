<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Verificação de e-mail') }}</title>
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

            <div class="text-center">
                <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">
                    {{ __('Verificar E-mail') }}
                </h1>
                <p class="mt-3 text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-normal px-4">
                    {{ __('Por favor, clique no link de verificação que enviamos para o seu e-mail.') }}
                </p>
            </div>

            @if(session('status') == 'verification-link-sent')
                <div class="p-4 text-sm font-bold text-center text-green-700 bg-green-50 rounded-2xl dark:bg-green-900/30 dark:text-green-300 border border-green-100 dark:border-green-800">
                    {{ __('Um novo link de verificação foi enviado para o seu e-mail.') }}
                </div>
            @endif

            <div class="flex flex-col gap-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full px-6 py-4 text-slate-950 bg-amber-400 hover:bg-amber-500 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                        {{ __('Reenviar Verificação') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full px-6 py-4 text-slate-500 hover:text-slate-900 dark:text-zinc-500 dark:hover:text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        {{ __('Sair do Sistema') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>