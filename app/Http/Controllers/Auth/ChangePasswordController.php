<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    /**
     * Show the form for changing the authenticated user's password.
     */
    public function show()
    {
        return view('auth.passwords.change');
    }

    /**
     * Update the authenticated user's password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        return redirect()
            ->route('password.change')
            ->with('status', 'Senha atualizada com sucesso!');
    }
}