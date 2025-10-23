<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'categoria'];

    public function doacoes()
    {
        return $this->belongsToMany(Doacao::class, 'doacao_item')
            ->withPivot('quantidade', 'unidade')
            ->withTimestamps();
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }
}
