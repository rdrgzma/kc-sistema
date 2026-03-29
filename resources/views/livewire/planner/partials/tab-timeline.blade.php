<div class="py-4 h-[500px] overflow-y-auto custom-scrollbar pr-2">
    @livewire('timeline-feed', ['model' => $task], key('timeline-' . $task->id))
</div>