<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends Component
{
    // Aqui no futuro injetaremos os Services para buscar os dados reais do banco
    public string $ganhos = 'R$ 780.000,00';
    public string $perdas = 'R$ 320.000,00';
    public string $custos = 'R$ 180.000,00';
    public string $eficiencia = '78%';


    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
