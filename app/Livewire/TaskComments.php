<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;

class TaskComments extends Component
{
    public Task $task;

    public string $novoComentario = '';

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function adicionarComentario()
    {
        $this->validate([
            'novoComentario' => 'required|string|min:2',
        ]);

        // Cria o comentário atrelado polimorficamente à Tarefa
        $this->task->comentarios()->create([
            'user_id' => auth()->id(),
            'content' => $this->novoComentario,
        ]);

        $this->novoComentario = ''; // Limpa a caixa de texto

        // Dispara um evento (opcional) caso a Timeline precise de se atualizar
        $this->dispatch('comentarioAdicionado');
    }

    public function render()
    {
        return view('livewire.task-comments', [
            'comentarios' => $this->task->comentarios()->with('user')->get(),
        ]);
    }
}
