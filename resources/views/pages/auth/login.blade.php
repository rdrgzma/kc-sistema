<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Entrar') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('img/background.jpg') }}');">
    <div class="w-full max-w-md p-10 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md rounded-[2.5rem] shadow-2xl border border-white/20">
        <div class="flex flex-col gap-8">
            <div class="flex justify-center -mt-4">
                <div class="p-4 bg-white rounded-3xl shadow-sm border border-slate-100">
                    <img src="{{ asset('img/logo-kc.jpeg') }}" alt="Logo KC" class="h-28 w-auto object-contain">
                </div>
            </div>

            <div class="text-center">
                <h1 class="text-3xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">
                    {{ __('Entrar na sua conta') }}
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

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest ml-1 mb-2">
                        {{ __('E-mail') }}
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="email@exemplo.com"
                        class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 transition-all font-bold text-sm">
                    @error('email')
                        <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="relative">
                    <div class="flex justify-between items-center ml-1 mb-2">
                        <label for="password" class="block text-xs font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">
                            {{ __('Senha') }}
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 transition-colors">
                                {{ __('Esqueceu a senha?') }}
                            </a>
                        @endif
                    </div>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-800 border-none rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 transition-all font-bold text-sm">
                    @error('password')
                        <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center ml-1">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                        class="h-5 w-5 text-primary-600 border-slate-300 dark:border-zinc-700 rounded-lg focus:ring-primary-500 dark:bg-zinc-800 transition-all">
                    <label for="remember" class="ml-3 block text-sm font-bold text-slate-600 dark:text-zinc-400">
                        {{ __('Lembrar-me') }}
                    </label>
                </div>

                <button type="submit"
                    class="w-full px-6 py-4 text-slate-950 bg-amber-400 hover:bg-amber-500 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-lg shadow-amber-500/20 transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                    {{ __('Entrar no Sistema') }}
                </button>
            </form>

            @if(Route::has('register'))
                <div class="text-sm text-center font-bold text-slate-500 dark:text-zinc-500">
                    <span>{{ __('Não tem uma conta?') }}</span>
                    <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 transition-colors">
                        {{ __('Cadastre-se') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>