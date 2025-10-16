<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiario;

class BeneficiarioController extends Controller
{
    public function index()
    {
        $beneficiarios = Beneficiario::all();
        return view("admin.beneficiarios.list", compact("beneficiarios"));
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
