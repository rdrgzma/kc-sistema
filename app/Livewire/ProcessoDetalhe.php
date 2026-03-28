<?php

namespace App\Livewire;

use App\Models\Processo;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class ProcessoDetalhe extends Component
{
    public Processo $processo;

    public function mount(Processo $processo)
    {
        // Carregamos as relações necessárias
        $this->processo = $processo->load(['pessoa', 'timelineEvents.user']);
    }


}; // <-- O fechamento da classe DEVE estar aqui, após os métodos.
