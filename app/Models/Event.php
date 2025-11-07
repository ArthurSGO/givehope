<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Event extends Model
{
    use HasFactory;

    public const STATUS_SOON = 'soon';
    public const STATUS_IN_PROGRESS = 'inprogress';
    public const STATUS_FINISHED = 'finished';

    protected const STATUS_LABELS = [
        self::STATUS_SOON => 'Em breve',
        self::STATUS_IN_PROGRESS => 'Em andamento',
        self::STATUS_FINISHED => 'Finalizado',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'tagline',
        'description',
        'start_date',
        'end_date',
        'location',
        'highlight_needs',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['image_url', 'status', 'status_label'];

    public function scopeInProgress(Builder $query): Builder
    {
        $today = now()->startOfDay();

        return $query
            ->whereNotNull('start_date')
            ->whereDate('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            });
    }


    public static function statusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    public function getStatusAttribute(): string
    {
        $now = now();
        $today = $now->copy()->startOfDay();

        if ($this->end_date && $this->end_date->copy()->endOfDay()->lt($now)) {
            return self::STATUS_FINISHED;
        }

        if ($this->start_date && $this->start_date->greaterThan($today)) {
            return self::STATUS_SOON;
        }

        if (!$this->start_date) {
            return self::STATUS_SOON;
        }

        return self::STATUS_IN_PROGRESS;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }

        return asset('images/event-placeholder.svg');
    }
}