<div>
    <div class="mb-4">
        <p class="text-sm text-slate-500 dark:text-zinc-400">
            Período: 
            <span class="font-medium text-slate-900 dark:text-zinc-100">
                {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
            </span>
        </p>
    </div>

    @if($resumo->isEmpty())
        <div class="text-center py-6 bg-slate-50 dark:bg-zinc-800/50 rounded-xl border border-slate-200 dark:border-zinc-700 border-dashed">
            <p class="text-sm text-slate-500 dark:text-zinc-400">Nenhuma produção registrada neste período.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-zinc-700">
            <table class="w-full text-left text-sm text-slate-600 dark:text-zinc-300">
                <thead class="bg-slate-50 dark:bg-zinc-800 text-xs uppercase font-semibold text-slate-500 dark:text-zinc-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tipo de Documento / Peça</th>
                        <th scope="col" class="px-6 py-3 text-right">Total Produzido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @php $totalGeral = 0; @endphp
                    @foreach($resumo as $item)
                        @php $totalGeral += $item->total; @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-zinc-100">
                                {{ $item->tipoPeca?->nome ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                                    {{ $item->total }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-zinc-800 font-semibold text-slate-900 dark:text-zinc-100">
                    <tr>
                        <td class="px-6 py-4 text-right">Total Geral</td>
                        <td class="px-6 py-4 text-right">{{ $totalGeral }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
