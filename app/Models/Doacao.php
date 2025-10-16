<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Doacao extends Model
{
    use HasFactory, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tipo', 'quantidade', 'unidade', 'descricao'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Uma doação foi {$eventName}")
            ->useLogName('Doações');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'doacao_item')
            ->withPivot('quantidade', 'unidade') // Informa que a tabela pivot tem colunas extras
            ->withTimestamps();
    }
}
