<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'categoria'];

    protected $table = 'itens';
    
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

    public function distribuicoes()
    {
        return $this->belongsToMany(Distribuicao::class, 'distribuicao_item')
            ->withPivot('quantidade', 'unidade', 'origem_estoque_id')
            ->withTimestamps();
    }
}
