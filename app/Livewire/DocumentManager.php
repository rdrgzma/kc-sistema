<?php

namespace App\Livewire;

use App\Models\Documento;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    public $model;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount($model): void
    {
        $this->model = $model;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('arquivos')
                    ->label('Upload de Documentos')
                    ->multiple()
                    ->directory('documents/'.class_basename($this->model))
                    ->preserveFilenames()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if (! $state) {
                            return;
                        }

                        foreach ($state as $file) {
                            $this->model->documentos()->create([
                                'nome_arquivo' => $file->getClientOriginalName(),
                                'caminho' => $file->store('documents/'.class_basename($this->model), 'public'),
                                'extensao' => $file->getClientOriginalExtension(),
                                'tamanho' => $file->getSize(),
                                'user_id' => auth()->id(),
                            ]);
                        }

                        $this->form->fill();
                        $this->dispatch('notify', message: 'Arquivos enviados com sucesso!');
                    }),
            ]);
    }

    public function excluirDocumento(int $id): void
    {
        $doc = Documento::find($id);

        if ($doc) {
            Storage::disk('public')->delete($doc->caminho);
            $doc->delete();
        }
    }

    public function render(): View
    {
        return view('livewire.document-manager', [
            'documentos' => $this->model->documentos()->latest()->get(),
        ]);
    }
}
