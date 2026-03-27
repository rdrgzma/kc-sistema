<header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $headerTitle ?? 'Painel Estratégico' }}</h1>
        <p class="text-sm text-gray-500">{{ $headerSubtitle ?? 'Silva & Associados' }}</p>
    </div>

    <div class="flex items-center gap-6">
        <div class="relative">
            <input type="text" placeholder="Buscar..." class="w-64 pl-4 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
        </div>
        
        <div class="text-right">
            <p class="text-sm font-bold text-gray-800">DR. Carlos Silva</p>
            <p class="text-xs text-gray-500">Proprietário</p>
        </div>
    </div>
</header>
