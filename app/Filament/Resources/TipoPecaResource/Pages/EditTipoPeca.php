<?php

namespace App\Filament\Resources\TipoPecaResource\Pages;

use App\Filament\Resources\TipoPecaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoPeca extends EditRecord
{
    protected static string $resource = TipoPecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
