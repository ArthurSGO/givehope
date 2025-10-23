<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paroquia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'nome_fantasia',
        'cnpj',
        'abertura',
        'porte',
        'natureza_juridica',
        'situacao',
        'logradouro',
        'numero',
        'bairro',
        'cep',
        'cidade',
        'estado',
        'telefone',
        'email',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function doacoes()
    {
        return $this->hasMany(Doacao::class);
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }
}
