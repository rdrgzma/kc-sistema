<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Esqueci a senha') }}</title>
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
                    {{ __('Recuperar Senha') }}
                </h1>
                <p class="mt-3 text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">
                    {{ __('K&C Sistema Jurídico') }}
                </p>
            </div>

            @if(session('status'))
                <div class="p-4 text-sm font-bold text-center text-green-700 bg-green-50 rounded-2xl dark:bg-green-900/30 dark:text-green-300 border border-green-100 dark:border-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest ml-1 mb-2">
                        {{ __('E-mail') }}
                    </label>
                    <input type="email" name="email" id="email" required autofocus placeholder="email@exemplo.com"
                        class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-amber-500 transition-all font-bold text-sm">
                    @error('email')
                        <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full px-6 py-4 text-slate-950 bg-amber-400 hover:bg-amber-500 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-lg shadow-amber-500/20 transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                    {{ __('Enviar link de redefinição') }}
                </button>
            </form>

            <div class="text-sm text-center font-bold text-slate-500 dark:text-zinc-500">
                <span>{{ __('Ou, retorne para') }}</span>
                <a href="{{ route('login') }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 transition-colors">
                    {{ __('Entrar no Sistema') }}
                </a>
            </div>
        </div>
    </div>
        </div>
    </div>
</body>

</html>