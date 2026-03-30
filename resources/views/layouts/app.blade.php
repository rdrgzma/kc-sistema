<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'K&C Analytics' }}</title>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles
    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Anti-FOUC: aplica tema antes do Alpine iniciar
        (function() {
            var theme = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var isDark = theme === 'dark' || (theme === 'system' && prefersDark) || (!theme && prefersDark);
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    <script>
        // Alpine Store global: fonte única de verdade para o tema
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                mode: localStorage.getItem('theme') || 'system',

                get isDark() {
                    if (this.mode === 'dark') return true;
                    if (this.mode === 'light') return false;
                    return window.matchMedia('(prefers-color-scheme: dark)').matches;
                },

                set(mode) {
                    this.mode = mode;
                    localStorage.setItem('theme', mode);
                    document.documentElement.classList.toggle('dark', this.isDark);
                },

                toggle() {
                    this.set(this.isDark ? 'light' : 'dark');
                }
            });
        });
    </script>
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans antialiased flex h-screen overflow-hidden transition-colors"
      x-data="{
          isSidebarOpen: localStorage.getItem('sidebar') !== 'collapsed',
          toggleSidebar() {
              this.isSidebarOpen = !this.isSidebarOpen;
              localStorage.setItem('sidebar', this.isSidebarOpen ? 'open' : 'collapsed');
          }
      }">

    <!-- Sync tema a cada wire:navigate -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var isDark = theme === 'dark' || (theme === 'system' && prefersDark) || (!theme && prefersDark);
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>


    @include('layouts.app.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        
        @include('layouts.app.header')

        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @filamentScripts
   
</body>
</html>
