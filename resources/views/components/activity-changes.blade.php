@php
    $properties = $record->properties;
    $attributes = $properties->get('attributes') ?? [];
    $old = $properties->get('old') ?? [];
    $keys = array_keys($attributes);
@endphp

<div class="space-y-4">
    <div class="text-xs font-medium text-slate-500 dark:text-zinc-400">
        Resumo de valores alterados durante esta operação:
    </div>

    @if(empty($keys))
        <div class="text-sm font-bold text-slate-600 dark:text-zinc-500 py-4 text-center">
            Nenhuma propriedade detalhada encontrada.
        </div>
    @else
        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-zinc-800 shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-zinc-800 text-left text-xs font-bold text-slate-700 dark:text-zinc-300">
                <thead class="bg-slate-50 dark:bg-zinc-800 text-[10px] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-3">Campo</th>
                        <th class="px-6 py-3">Valor Antigo</th>
                        <th class="px-6 py-3">Valor Novo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @foreach($keys as $key)
                        @php
                            $oldVal = $old[$key] ?? null;
                            $newVal = $attributes[$key] ?? null;

                            $formatValue = function ($val) {
                                if (is_null($val)) {
                                    return '-';
                                }
                                if (is_bool($val)) {
                                    return $val ? 'Sim' : 'Não';
                                }
                                if (is_array($val)) {
                                    return json_encode($val, JSON_UNESCAPED_UNICODE);
                                }
                                return (string) $val;
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 font-black text-slate-900 dark:text-zinc-50 uppercase tracking-wider text-[9px]">{{ $key }}</td>
                            <td class="px-6 py-4 text-red-600 dark:text-red-400 font-semibold break-all">{{ $formatValue($oldVal) }}</td>
                            <td class="px-6 py-4 text-green-600 dark:text-green-400 font-semibold break-all">{{ $formatValue($newVal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
