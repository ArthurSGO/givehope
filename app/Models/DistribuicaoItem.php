<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistribuicaoItem extends Model
{
    use HasFactory;

    protected $table = 'distribuicao_item';

    protected $fillable = [
        'distribuicao_id',
        'estoque_id',
        'item_id',
        'unidade',
        'quantidade',
        'quantidade_reservada',
    ];

    protected $casts = [
        'quantidade' => 'float',
        'quantidade_reservada' => 'float',
    ];

    public function distribuicao(): BelongsTo
    {
        return $this->belongsTo(Distribuicao::class);
    }

    public function estoque(): BelongsTo
    {
        return $this->belongsTo(Estoque::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}