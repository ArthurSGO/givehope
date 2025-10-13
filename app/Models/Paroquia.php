<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paroquia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cnpj',
        'logradouro',
        'cidade',
        'estado',
        'telefone',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
