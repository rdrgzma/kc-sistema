<?php

namespace App\Livewire;

use App\Models\Pessoa;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PessoaDetalhe extends Component
{
    public Pessoa $pessoa;

    public function mount(Pessoa $pessoa)
    {
        $this->pessoa = $pessoa->load('documentos');
    }

    public function render(): View
    {
        return view('livewire.pessoa-detalhe');
    }
}
