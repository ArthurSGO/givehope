<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index()
    {
        $logs = Activity::with([
            'causer:id,name',
            'subject' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Doacao::class => ['doador:id,nome', 'items:id,nome'],
                ]);
            },
        ])
            ->where('log_name', 'Doações')
            ->latest()
            ->paginate(15);

        return view('admin.logs.list', compact('logs'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $log = Activity::with([
            'causer:id,name',
            'subject' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Doacao::class => ['doador:id,nome', 'items:id,nome'],
                ]);
            },
        ])
            ->where('log_name', 'Doações')
            ->findOrFail($id);

        return view('admin.logs.show', compact('log'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
