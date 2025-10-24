<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstoqueMovimentacao extends Model
{
    use HasFactory;

    protected $table = 'estoque_movimentacoes';

    protected $fillable = [
        'estoque_id',
        'paroquia_id',
        'item_id',
        'distribuicao_id',
        'user_id',
        'tipo',
        'quantidade',
        'unidade',
        'motivo',
    ];

    protected $casts = [
        'quantidade' => 'float',
    ];

    public function estoque(): BelongsTo
    {
        return $this->belongsTo(Estoque::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function distribuicao(): BelongsTo
    {
        return $this->belongsTo(Distribuicao::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}