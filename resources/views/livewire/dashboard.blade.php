<div class="space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md hover:border-primary-400/50">
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Ganhos</p>
            <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2 tracking-tight">{{ $ganhos }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md hover:border-primary-400/50">
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Perdas</p>
            <p class="text-3xl font-black text-rose-600 dark:text-rose-400 mt-2 tracking-tight">{{ $perdas }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md hover:border-primary-400/50">
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Custos</p>
            <p class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-2 tracking-tight">{{ $custos }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-slate-300 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md hover:border-primary-400/50">
            <p class="text-[10px] font-black text-slate-500 dark:text-zinc-500 uppercase tracking-[0.15em]">Eficiência</p>
            <p class="text-3xl font-black text-sky-600 dark:text-sky-400 mt-2 tracking-tight">{{ $eficiencia }}</p>
        </div>

    </div>

    <!-- Row 2: Colored summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-indigo-600 dark:bg-indigo-700 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02] border border-indigo-500">
            <p class="text-[10px] font-black text-white/70 uppercase tracking-widest">Delegadas</p>
            <p class="text-3xl font-black text-white mt-1">180</p>
        </div>
        <div class="bg-emerald-600 dark:bg-emerald-600 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02] border border-emerald-500">
            <p class="text-[10px] font-black text-white/70 uppercase tracking-widest">Concluídas</p>
            <p class="text-3xl font-black text-white mt-1">140</p>
        </div>
        <div class="bg-blue-600 dark:bg-blue-700 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02] border border-blue-500">
            <p class="text-[10px] font-black text-white/70 uppercase tracking-widest">No Prazo</p>
            <p class="text-3xl font-black text-white mt-1">110</p>
        </div>
        <div class="bg-rose-600 dark:bg-rose-600 p-6 rounded-2xl shadow-lg transition-all hover:scale-[1.02] border border-rose-500">
            <p class="text-[10px] font-black text-white/70 uppercase tracking-widest">Atrasadas</p>
            <p class="text-3xl font-black text-white mt-1">30</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm">
            <h2 class="text-xl font-black text-slate-900 dark:text-zinc-50 mb-8 uppercase tracking-widest text-[11px]">Análise Financeira</h2>
            <div class="h-80 flex items-center justify-center bg-slate-50 dark:bg-zinc-800/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-zinc-700">
                <p class="text-slate-400 dark:text-zinc-500 font-black uppercase tracking-[0.2em] text-[10px]">Gráfico de Fluxo de Caixa</p>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-slate-300 dark:border-zinc-800 shadow-sm">
            <h2 class="text-xl font-black text-slate-900 dark:text-zinc-50 mb-8 uppercase tracking-widest text-[11px]">Interações Recentes</h2>
            <div class="h-80 flex items-center justify-center bg-slate-50 dark:bg-zinc-800/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-zinc-700">
                 <p class="text-slate-400 dark:text-zinc-500 font-black uppercase tracking-[0.2em] text-[10px]">Mapa de Engajamento</p>
            </div>
        </div>
    </div>

</div>