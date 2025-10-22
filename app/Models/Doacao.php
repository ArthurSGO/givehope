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
            ->setDescriptionForEvent(function (string $eventName) {
                if ($eventName === 'updated') {
                    return 'Uma doação foi atualizada';
                }
                if ($eventName === 'deleted') {
                    return 'Uma doação foi excluída';
                }
                return "Uma doação foi {$eventName}";
            })
            ->useLogName('Doações')
            ->dontSubmitEmptyLogs();
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'doacao_item')
            ->withPivot('quantidade', 'unidade')
            ->withTimestamps();
    }
}
