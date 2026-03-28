<?php

namespace App\Livewire;

use App\Models\Pessoa;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

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
