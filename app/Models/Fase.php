<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'valor_custa_padrao',
    ];

    protected function casts(): array
    {
        return [
            'valor_custa_padrao' => 'float',
        ];
    }

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
