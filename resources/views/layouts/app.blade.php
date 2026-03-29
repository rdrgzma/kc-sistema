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
        // Ant-FOUC para o load inicial
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans antialiased flex h-screen overflow-hidden transition-colors"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark',
          isSidebarOpen: localStorage.getItem('sidebar') !== 'collapsed',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          toggleSidebar() {
              this.isSidebarOpen = !this.isSidebarOpen;
              localStorage.setItem('sidebar', this.isSidebarOpen ? 'open' : 'collapsed');
          }
      }">

    <!-- Este script roda nativamente e sincronamente a CADA wire:navigate porque o Livewire reinjeta o body -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
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
