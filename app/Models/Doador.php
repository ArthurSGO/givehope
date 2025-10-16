<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doador extends Model
{
    use HasFactory;

    protected $table = 'doadores';

    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'telefone',
        'logradouro',
        'numero',
        'cidade',
        'estado',
    ];
}