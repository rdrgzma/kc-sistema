<?php

namespace App\Livewire;

use App\Models\Documento;
use App\Models\Pasta;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithFileUploads;

    public $model;

    public $current_folder_id = null;

    public function mount($model): void
    {
        $this->model = $model;
    }

    public function createFolderAction(): Action
    {
        return Action::make('createFolder')
            ->label('Nova Pasta')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->form([
                TextInput::make('nome')
                    ->label('Nome da Pasta')
                    ->required()
                    ->maxLength(255),
                Select::make('parent_id')
                    ->label('Pasta Superior (Opcional)')
                    ->options(fn () => Pasta::where('pastable_id', $this->model->id)
                        ->where('pastable_type', get_class($this->model))
                        ->pluck('nome', 'id'))
                    ->default($this->current_folder_id)
                    ->searchable(),
            ])
            ->action(function (array $data) {
                Pasta::create([
                    'nome' => $data['nome'],
                    'parent_id' => $data['parent_id'],
                    'pastable_id' => $this->model->id,
                    'pastable_type' => get_class($this->model),
                    'escritorio_id' => auth()->user()->escritorio_id,
                ]);

                $this->dispatch('notify', message: 'Pasta criada com sucesso!');
            })
            ->modalHeading('Criar Nova Pasta')
            ->modalWidth('xl');
    }

    public function uploadAction(): Action
    {
        return Action::make('upload')
            ->label('Subir Arquivos')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->form([
                FileUpload::make('arquivos')
                    ->label('Selecionar Arquivos')
                    ->multiple()
                    ->disk('public')
                    ->directory('documents/'.class_basename($this->model))
                    ->preserveFilenames()
                    ->required(),
                Select::make('pasta_id')
                    ->label('Salvar na Pasta')
                    ->options(fn () => Pasta::where('pastable_id', $this->model->id)
                        ->where('pastable_type', get_class($this->model))
                        ->pluck('nome', 'id'))
                    ->default($this->current_folder_id)
                    ->searchable()
                    ->placeholder('Raiz do GED'),
            ])
            ->action(function (array $data) {
                if (! isset($data['arquivos'])) {
                    return;
                }

                foreach ($data['arquivos'] as $file) {
                    $this->model->documentos()->create([
                        'nome_arquivo' => basename($file),
                        'caminho' => $file,
                        'extensao' => pathinfo($file, PATHINFO_EXTENSION),
                        'tamanho' => Storage::disk('public')->exists($file) ? Storage::disk('public')->size($file) : 0,
                        'pasta_id' => $data['pasta_id'],
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->dispatch('notify', message: 'Arquivos enviados com sucesso!');
            })
            ->modalHeading('Upload de Documentos')
            ->modalWidth('2xl');
    }

    public function goToFolder($id = null)
    {
        $this->current_folder_id = $id;
    }

    public function excluirDocumento(int $id): void
    {
        $doc = Documento::find($id);
        if ($doc) {
            Storage::disk('public')->delete($doc->caminho);
            $doc->delete();
        }
    }

    public function excluirPasta(int $id): void
    {
        $pasta = Pasta::find($id);
        if ($pasta) {
            // Documentos na pasta perdem a referência (set null na migration)
            $pasta->delete();
            if ($this->current_folder_id == $id) {
                $this->current_folder_id = null;
            }
        }
    }

    public function editFolderAction(): Action
    {
        return Action::make('editFolder')
            ->label('Renomear Pasta')
            ->icon('heroicon-o-pencil')
            ->color('warning')
            ->form([
                TextInput::make('nome')
                    ->label('Nome da Pasta')
                    ->required()
                    ->maxLength(255),
            ])
            ->fillForm(fn (array $arguments) => [
                'nome' => Pasta::find($arguments['id'])?->nome,
            ])
            ->action(function (array $data, array $arguments) {
                $pasta = Pasta::find($arguments['id']);
                if ($pasta) {
                    $pasta->update(['nome' => $data['nome']]);
                    $this->dispatch('notify', message: 'Pasta renomeada com sucesso!');
                }
            });
    }

    public function editDocumentAction(): Action
    {
        return Action::make('editDocument')
            ->label('Editar Documento')
            ->icon('heroicon-o-pencil')
            ->color('warning')
            ->form([
                TextInput::make('nome_arquivo')
                    ->label('Nome do Arquivo')
                    ->required()
                    ->maxLength(255),
            ])
            ->fillForm(fn (array $arguments) => [
                'nome_arquivo' => Documento::find($arguments['id'])?->nome_arquivo,
            ])
            ->action(function (array $data, array $arguments) {
                $doc = Documento::find($arguments['id']);
                if ($doc) {
                    $doc->update(['nome_arquivo' => $data['nome_arquivo']]);
                    $this->dispatch('notify', message: 'Documento renomeado com sucesso!');
                }
            });
    }

    public function render(): View
    {
        $queryFolders = Pasta::where('pastable_id', $this->model->id)
            ->where('pastable_type', get_class($this->model))
            ->where('parent_id', $this->current_folder_id);

        $queryDocs = Documento::where('documentable_id', $this->model->id)
            ->where('documentable_type', get_class($this->model))
            ->where('pasta_id', $this->current_folder_id);

        $breadcrumb = [];
        if ($this->current_folder_id) {
            $folder = Pasta::find($this->current_folder_id);
            $temp = $folder;
            while ($temp) {
                array_unshift($breadcrumb, $temp);
                $temp = $temp->parent;
            }
        }

        // Para a árvore lateral (global do modelo)
        $treeFolders = Pasta::where('pastable_id', $this->model->id)
            ->where('pastable_type', get_class($this->model))
            ->whereNull('parent_id')
            ->with('subpastas')
            ->get();

        return view('livewire.document-manager', [
            'folders' => $queryFolders->get(),
            'documentos' => $queryDocs->latest()->get(),
            'breadcrumb' => $breadcrumb,
            'treeFolders' => $treeFolders,
        ]);
    }
}
