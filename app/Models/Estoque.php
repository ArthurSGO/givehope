<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read float $quantidade_reservada
 * @property-read float $quantidade_disponivel
 */

class Estoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'paroquia_id',
        'item_id',
        'unidade',
        'quantidade',
    ];

    protected $casts = [
        'quantidade' => 'float',
    ];

    public function paroquia()
    {
        return $this->belongsTo(Paroquia::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(EstoqueMovimentacao::class);
    }

    public function distribuicaoItems()
    {
        return $this->hasMany(DistribuicaoItem::class);
    }

    public function getQuantidadeReservadaAttribute(): float
    {
        return (float) $this->distribuicaoItems()
            ->whereHas('distribuicao', function ($query) {
                $query->where('status', Distribuicao::STATUS_RESERVADO)
                    ->whereNull('estoque_debitado_em');
            })
            ->sum('quantidade_reservada');
    }

    public function getQuantidadeDisponivelAttribute(): float
    {
        $quantidadeAtual = (float) ($this->quantidade ?? 0);

        return max(0, $quantidadeAtual - $this->quantidade_reservada);
    }
}