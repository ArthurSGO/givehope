<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Distribuicao extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'distribuicoes';

    public const STATUS_RESERVADO = 'reservado';
    public const STATUS_ENVIADO = 'enviado';
    public const STATUS_ENTREGUE = 'entregue';

    protected $fillable = [
        'paroquia_id',
        'beneficiario_id',
        'status',
        'observacoes',
        'reservado_em',
        'enviado_em',
        'entregue_em',
        'estoque_debitado_em',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reservado_em' => 'datetime',
        'enviado_em' => 'datetime',
        'entregue_em' => 'datetime',
        'estoque_debitado_em' => 'datetime',
    ];

    public function paroquia(): BelongsTo
    {
        return $this->belongsTo(Paroquia::class);
    }

    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'distribuicao_item')
            ->withPivot(['estoque_id', 'quantidade', 'quantidade_reservada', 'unidade'])
            ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'beneficiario_id',
                'status',
                'observacoes',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                return match ($eventName) {
                    'created' => 'Uma distribuição foi criada',
                    'updated' => 'Uma distribuição foi atualizada',
                    'deleted' => 'Uma distribuição foi removida',
                    default => "Uma distribuição foi {$eventName}",
                };
            })
            ->useLogName('Distribuições')
            ->dontSubmitEmptyLogs();
    }
}