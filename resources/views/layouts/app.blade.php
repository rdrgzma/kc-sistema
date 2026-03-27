<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
          }
      }"
      :class="{ 'dark': darkMode }">
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
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans antialiased flex h-screen overflow-hidden transition-colors">


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
