<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doacao extends Model
{
    use HasFactory;

    protected $table = 'doacoes';
    
    protected $fillable = [
        'paroquia_id',
        'doador_id',
        'tipo',
        'descricao',
        'quantidade',
        'unidade',
        'data_doacao',
    ];

    public function paroquia()
    {
        return $this->belongsTo(Paroquia::class);
    }

    public function doador()
    {
        return $this->belongsTo(Doador::class);
    }
}