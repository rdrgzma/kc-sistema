<!DOCTYPE html>
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
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex h-screen overflow-hidden">

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
