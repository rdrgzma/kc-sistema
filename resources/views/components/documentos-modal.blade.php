<div class="px-2 py-4">
    @livewire(\App\Livewire\DocumentManager::class, ['model' => $record], key('doc-manager-'.class_basename($record).'-'.$record->id))
</div>
