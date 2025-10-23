<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}