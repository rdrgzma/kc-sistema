<?php

namespace App\Filament\Resources\TipoPecaResource\Pages;

use App\Filament\Resources\TipoPecaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoPecas extends ListRecords
{
    protected static string $resource = TipoPecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
