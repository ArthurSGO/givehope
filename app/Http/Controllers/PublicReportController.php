<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicReportController extends Controller
{
    public function index(): View
    {
        $donations = Doacao::query()
            ->with(['itens', 'doador', 'paroquia'])
            ->get();

        $summary = $this->buildSummary($donations);
        $donationTypeBreakdown = $this->buildDonationTypeBreakdown($donations, $summary['total_donations']);
        $itemBreakdown = $this->buildItemBreakdown($donations);
        $topDonors = $this->buildTopDonors($donations);
        $topParishes = $this->buildTopParishes($donations);
        $monthlyTimeline = $this->buildMonthlyTimeline($donations);

        return view('reports.general', [
            'summary' => $summary,
            'donationTypeBreakdown' => $donationTypeBreakdown,
            'itemBreakdown' => $itemBreakdown,
            'topDonors' => $topDonors,
            'topParishes' => $topParishes,
            'monthlyTimeline' => $monthlyTimeline,
        ]);
    }

    protected function buildSummary(Collection $donations): array
    {
        $monetaryTotal = $donations
            ->where('tipo', 'dinheiro')
            ->sum('quantidade');

        $donorsCount = $donations
            ->pluck('doador_id')
            ->filter()
            ->unique()
            ->count();

        $parishesCount = $donations
            ->pluck('paroquia_id')
            ->filter()
            ->unique()
            ->count();

        $itemEntries = $donations->sum(fn($donation) => $donation->itens->count());

        return [
            'total_donations' => $donations->count(),
            'monetary_total' => $monetaryTotal,
            'donors_count' => $donorsCount,
            'parishes_count' => $parishesCount,
            'item_entries' => $itemEntries,
        ];
    }

    protected function buildDonationTypeBreakdown(Collection $donations, int $totalDonations): array
    {
        if ($totalDonations === 0) {
            return [];
        }

        return $donations
            ->groupBy(fn($donation) => $donation->tipo ?: 'outros')
            ->map(function (Collection $group, string $type) use ($totalDonations) {
                $label = Str::of($type)
                    ->replace('_', ' ')
                    ->trim()
                    ->title();

                return [
                    'type' => $type,
                    'label' => $label->isEmpty() ? 'Não informado' : (string) $label,
                    'donations_count' => $group->count(),
                    'quantity_total' => $group->sum('quantidade'),
                    'percentage' => round(($group->count() / $totalDonations) * 100, 1),
                    'monetary_total' => $type === 'dinheiro' ? $group->sum('quantidade') : 0,
                ];
            })
            ->sortByDesc('donations_count')
            ->values()
            ->all();
    }

    protected function buildItemBreakdown(Collection $donations): array
    {
        $itemBreakdown = [];

        foreach ($donations as $donation) {
            foreach ($donation->itens as $item) {
                $key = $item->id . '|' . $item->pivot->unidade;

                if (!isset($itemBreakdown[$key])) {
                    $itemBreakdown[$key] = [
                        'name' => $item->nome,
                        'unit' => $item->pivot->unidade,
                        'quantity' => 0,
                    ];
                }

                $itemBreakdown[$key]['quantity'] += (float) $item->pivot->quantidade;
            }
        }

        return collect($itemBreakdown)
            ->sortByDesc('quantity')
            ->values()
            ->all();
    }

    protected function buildTopDonors(Collection $donations): array
    {
        return $donations
            ->filter(fn($donation) => $donation->relationLoaded('doador') && $donation->doador)
            ->groupBy('doador_id')
            ->map(function (Collection $group) {
                $donor = $group->first()->doador;

                return [
                    'name' => $donor?->nome ?? 'Doadores não identificados',
                    'donations_count' => $group->count(),
                    'monetary_total' => $group->where('tipo', 'dinheiro')->sum('quantidade'),
                ];
            })
            ->sortByDesc('donations_count')
            ->take(5)
            ->values()
            ->all();
    }

    protected function buildTopParishes(Collection $donations): array
    {
        return $donations
            ->filter(fn($donation) => $donation->relationLoaded('paroquia') && $donation->paroquia)
            ->groupBy('paroquia_id')
            ->map(function (Collection $group) {
                $parish = $group->first()->paroquia;

                return [
                    'name' => $parish?->nome ?? 'Paróquia não informada',
                    'donations_count' => $group->count(),
                    'monetary_total' => $group->where('tipo', 'dinheiro')->sum('quantidade'),
                ];
            })
            ->sortByDesc('donations_count')
            ->take(5)
            ->values()
            ->all();
    }

    protected function buildMonthlyTimeline(Collection $donations): array
    {
        return $donations
            ->filter(function ($donation) {
                return (bool) Carbon::make($donation->data_doacao);
            })
            ->groupBy(function ($donation) {
                $date = Carbon::make($donation->data_doacao);

                return $date ? $date->format('Y-m') : 'sem-data';
            })
            ->reject(fn($_, $month) => $month === 'sem-data')
            ->sortKeys()
            ->map(function (Collection $group, string $monthKey) {
                $date = Carbon::createFromFormat('Y-m', $monthKey);

                return [
                    'key' => $monthKey,
                    'label' => $date
                        ? $date->locale('pt_BR')->translatedFormat('F \de Y')
                        : $monthKey,
                    'donations_count' => $group->count(),
                    'monetary_total' => $group->where('tipo', 'dinheiro')->sum('quantidade'),
                ];
            })
            ->values()
            ->all();
    }
}