<?php

namespace App\Livewire;

use App\Models\Pessoa;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class PessoaVinculosManager extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public Pessoa $pessoa;

    public function table(Table $table): Table
    {
        return $table
            ->query(Pessoa::query()->whereIn('id', $this->pessoa->vinculos()->select('pessoas.id')))
            ->recordUrl(fn (Pessoa $record): string => route('pessoas.show', $record))
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PF' => 'info',
                        'PJ' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('nome_razao')
                    ->label('Nome / Razão Social')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cpf_cnpj')
                    ->label('CPF / CNPJ')
                    ->searchable(),

                TextColumn::make('telefone')
                    ->label('Telefone')
                    ->searchable(),
            ])
            ->headerActions([
                Action::make('vincular')
                    ->label($this->pessoa->tipo === 'PF' ? 'Vincular Pessoa Jurídica' : 'Vincular Pessoa Física')
                    ->color('primary')
                    ->form([
                        Select::make('pessoa_id')
                            ->label('Selecione o Cliente')
                            ->options(function () {
                                $alreadyLinkedIds = $this->pessoa->vinculos()->pluck('pessoas.id')->toArray();
                                $query = Pessoa::query()
                                    ->where('id', '!=', $this->pessoa->id)
                                    ->whereNotIn('id', $alreadyLinkedIds);

                                if ($this->pessoa->tipo === 'PF') {
                                    $query->where('tipo', 'PJ');
                                } else {
                                    $query->where('tipo', 'PF');
                                }

                                return $query->pluck('nome_razao', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->pessoa->vinculos()->attach($data['pessoa_id']);
                    }),
            ])
            ->actions([
                Action::make('desvincular')
                    ->label('Remover Vínculo')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Pessoa $record) {
                        $this->pessoa->vinculos()->detach($record->id);
                    }),
            ])
            ->emptyStateHeading('Sem pessoas vinculadas');
    }

    public function render(): View
    {
        return view('livewire.pessoa-vinculos-manager');
    }
}
