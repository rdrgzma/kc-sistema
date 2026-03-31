<div class="{{ $model ? 'py-4 px-0' : 'px-6 py-12 max-w-[95%] mx-auto' }} space-y-8">
    @if(!$model)
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Financeiro</h1>
                <p class="text-slate-500 dark:text-zinc-400 mt-1 font-bold tracking-tight">Gestão automatizada de receitas e despesas.</p>
            </div>
            
            <div class="flex items-center gap-4">
                 <!-- Card de Saldo Sênior -->
                 <div class="flex gap-4">
                    @php
                        $receitas = \App\Models\LancamentoFinanceiro::estratificado()->where('tipo', 'receita')->where('status', 'pago')->sum('valor');
                        $despesas = \App\Models\LancamentoFinanceiro::estratificado()->where('tipo', 'despesa')->where('status', 'pago')->sum('valor');
                        $saldo = $receitas - $despesas;
                    @endphp
                    <div class="px-4 py-2 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-2xl shadow-sm">
                        <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">Saldo Atual</span>
                        <p class="text-xl font-black {{ $saldo >= 0 ? 'text-primary-600' : 'text-danger-600' }} tracking-tighter">
                            R$ {{ number_format($saldo, 2, ',', '.') }}
                        </p>
                    </div>
                 </div>
            </div>
        </div>
    @endif

    <div class="{{ $model ? '' : 'bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm' }}">
        <div class="{{ $model ? '' : 'p-6' }}">
            {{ $this->table }}
        </div>
    </div>

    <!-- Modals e Actions do Filament -->
    <x-filament-actions::modals />
</div>
