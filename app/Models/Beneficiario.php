<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'telefone',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'cpf',
        'rg',
        'data_nascimento',
        'email',
        'ponto_referencia',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function getTelefoneFormatadoAttribute(): ?string
    {
        if (empty($this->telefone)) {
            return null;
        }

        $telefone = preg_replace('/[^0-9]/', '', $this->telefone);

        if (strlen($telefone) === 11) {
            return sprintf('(%s) %s-%s', substr($telefone, 0, 2), substr($telefone, 2, 5), substr($telefone, 7));
        }

        if (strlen($telefone) === 10) {
            return sprintf('(%s) %s-%s', substr($telefone, 0, 2), substr($telefone, 2, 4), substr($telefone, 6));
        }

        return $this->telefone;
    }

    public function getCpfFormatadoAttribute(): ?string
    {
        if (empty($this->cpf)) {
            return null;
        }

        $cpf = preg_replace('/[^0-9]/', '', $this->cpf);

        if (strlen($cpf) !== 11) {
            return $this->cpf;
        }

        return sprintf('%s.%s.%s-%s', substr($cpf, 0, 3), substr($cpf, 3, 3), substr($cpf, 6, 3), substr($cpf, 9));
    }

    public function getCepFormatadoAttribute(): ?string
    {
        if (empty($this->cep)) {
            return null;
        }

        $cep = preg_replace('/[^0-9]/', '', $this->cep);

        if (strlen($cep) !== 8) {
            return $this->cep;
        }

        return sprintf('%s-%s', substr($cep, 0, 5), substr($cep, 5));
    }
}
