<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index()
    {
        $logs = Activity::with(['subject' => function ($query) {
            $query->with(['doador:id,nome', 'items:id,nome']);
        }, 'causer'])
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
        //
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
