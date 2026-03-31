<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasta extends Model
{
    protected $fillable = ['nome', 'parent_id', 'pastable_id', 'pastable_type', 'escritorio_id'];

    public function pastable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Pasta::class, 'parent_id');
    }

    public function subpastas()
    {
        return $this->hasMany(Pasta::class, 'parent_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
