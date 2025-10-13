<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
class AdminController extends Controller
{
        public function index()
    {
        return view("admin.dashboard");
    }

}
