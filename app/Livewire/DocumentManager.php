<?php

namespace App\Livewire;

use App\Models\Documento;
use App\Models\Pasta;
use App\Models\Pessoa;
use App\Models\Processo;
use App\Models\Task;
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

    public function getPastableInfo(): array
    {
        if ($this->model instanceof Task) {
            if ($this->model->processo_id) {
                return [
                    'type' => Processo::class,
                    'id' => $this->model->processo_id,
                ];
            }

            if ($this->model->pessoa_id) {
                return [
                    'type' => Pessoa::class,
                    'id' => $this->model->pessoa_id,
                ];
            }
        }

        return [
            'type' => get_class($this->model),
            'id' => $this->model->id,
        ];
    }

    public function createFolderAction(): Action
    {
        $pastable = $this->getPastableInfo();

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
                    ->options(fn () => Pasta::where('pastable_id', $pastable['id'])
                        ->where('pastable_type', $pastable['type'])
                        ->pluck('nome', 'id'))
                    ->default($this->current_folder_id)
                    ->searchable(),
            ])
            ->action(function (array $data) use ($pastable) {
                Pasta::create([
                    'nome' => $data['nome'],
                    'parent_id' => $data['parent_id'],
                    'pastable_id' => $pastable['id'],
                    'pastable_type' => $pastable['type'],
                    'escritorio_id' => auth()->user()->escritorio_id,
                ]);

                $this->dispatch('notify', message: 'Pasta criada com sucesso!');
            })
            ->modalHeading('Criar Nova Pasta')
            ->modalWidth('xl');
    }

    public function uploadAction(): Action
    {
        $pastable = $this->getPastableInfo();

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
                    ->options(fn () => Pasta::where('pastable_id', $pastable['id'])
                        ->where('pastable_type', $pastable['type'])
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
                    $insertData = [
                        'nome_arquivo' => basename($file),
                        'caminho' => $file,
                        'extensao' => pathinfo($file, PATHINFO_EXTENSION),
                        'tamanho' => Storage::disk('public')->exists($file) ? Storage::disk('public')->size($file) : 0,
                        'pasta_id' => $data['pasta_id'],
                        'user_id' => auth()->id(),
                    ];

                    if ($this->model instanceof Task) {
                        $insertData['task_id'] = $this->model->id;
                        if ($this->model->processo_id) {
                            $insertData['documentable_type'] = Processo::class;
                            $insertData['documentable_id'] = $this->model->processo_id;
                        } elseif ($this->model->pessoa_id) {
                            $insertData['documentable_type'] = Pessoa::class;
                            $insertData['documentable_id'] = $this->model->pessoa_id;
                        } else {
                            $insertData['documentable_type'] = Task::class;
                            $insertData['documentable_id'] = $this->model->id;
                        }
                        Documento::create($insertData);
                    } else {
                        $this->model->documentos()->create($insertData);
                    }
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
        $pastable = $this->getPastableInfo();

        $queryFolders = Pasta::where('pastable_id', $pastable['id'])
            ->where('pastable_type', $pastable['type'])
            ->where('parent_id', $this->current_folder_id);

        if ($this->model instanceof Task) {
            if ($this->model->processo_id) {
                $queryDocs = Documento::where('documentable_type', Processo::class)
                    ->where('documentable_id', $this->model->processo_id)
                    ->where('pasta_id', $this->current_folder_id);
            } elseif ($this->model->pessoa_id) {
                $queryDocs = Documento::where('documentable_type', Pessoa::class)
                    ->where('documentable_id', $this->model->pessoa_id)
                    ->where('pasta_id', $this->current_folder_id);
            } else {
                $queryDocs = Documento::where('documentable_type', Task::class)
                    ->where('documentable_id', $this->model->id)
                    ->where('pasta_id', $this->current_folder_id);
            }
        } else {
            $queryDocs = Documento::where('documentable_id', $this->model->id)
                ->where('documentable_type', get_class($this->model))
                ->where('pasta_id', $this->current_folder_id);
        }

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
        $treeFolders = Pasta::where('pastable_id', $pastable['id'])
            ->where('pastable_type', $pastable['type'])
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
