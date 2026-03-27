<aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
    <div class="h-20 flex items-center px-6 border-b border-gray-100">
        <span class="text-xl font-bold text-gray-800 flex items-center gap-2">
            ⚖️ K&C Analytics
        </span>
    </div>
    
<nav class="flex-1 p-4 space-y-2">
    <a href="{{ route('dashboard') }}" 
       wire:navigate
       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        Dashboard
    </a>

    <a href="{{ route('pessoas.index') }}" 
       wire:navigate
       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('pessoas.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50' }}">
        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('pessoas.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        Pessoas
    </a>

    <a href="#" 
       class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Processos
    </a>
</nav>
</aside>