<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardProdutividadeEquipe extends Component
{
    public ?string $dataInicio = null;

    public ?string $dataFim = null;

    protected $queryString = [
        'dataInicio' => ['except' => ''],
        'dataFim' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->dataInicio = $this->dataInicio ?? now()->subDays(30)->toDateString();
        $this->dataFim = $this->dataFim ?? now()->toDateString();
    }

    public function render()
    {
        return view('livewire.dashboard-produtividade-equipe');
    }
}
