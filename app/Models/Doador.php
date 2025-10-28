<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doador extends Model
{
    use HasFactory;

    protected $table = 'doadores';

    // Campos que podem ser preenchidos via create() ou update()
    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'telefone',
        'logradouro',
        'cep',
        'numero',
        'cidade',
        'estado',
    ]; //

    public function doacoes()
    {
        return $this->hasMany(Doacao::class);
    }
}