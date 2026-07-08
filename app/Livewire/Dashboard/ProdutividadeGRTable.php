<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class ProdutividadeGRTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->whereNotNull('acao_gr')
                    ->with(['pessoa', 'pecaProcessual.tipoPeca'])
            )
            ->columns([
                TextColumn::make('pessoa.nome_razao')
                    ->label('Cliente')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pecaProcessual.tipoPeca.nome')
                    ->label('Tipo de Documento')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('acao_gr')
                    ->label('Ação')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('data_solicitacao')
                    ->label('Data de Solicitação')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('data_envio')
                    ->label('Data de Envio')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('repeticoes')
                    ->label('Repetições')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Nenhuma tarefa de GR encontrada');
    }

    public function render(): View
    {
        return view('livewire.dashboard.produtividade-gr-table');
    }
}
