<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Paroquia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function create()
    {
        $paroquias = Paroquia::orderBy('nome')->get();
        return view("admin.users.create", compact('paroquias'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => 'O campo :attribute é obrigatório. Por favor, preencha-o.',
            'email.unique' => 'Este email já está sendo utilizado.',
            'name.regex' => 'O campo Nome não pode conter números.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'paroquia_id.required_if' => 'É obrigatório selecionar uma paróquia para um usuário do tipo "Responsável".',
        ];

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚçÇãõÃÕàèìòùÀÈÌÒÙº\.\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['required', 'boolean'],
            'paroquia_id' => ['nullable', 'required_if:is_admin,0', 'exists:paroquias,id']
        ], $messages);

        $isAdmin = $request->input('is_admin') == '1';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $isAdmin,
            'paroquia_id' => $isAdmin ? null : $request->paroquia_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Novo usuário criado com sucesso');
    }

    public function index()
    {
        $users = User::all();

        return view('admin.users.list', compact('users'));
    }

    public function edit(User $user)
    {
        $paroquias = Paroquia::orderBy('nome')->get();
        return view('admin.users.edit', compact('user', 'paroquias'));
    }

    public function update(Request $request, User $user)
    {
        $messages = [
            'required' => 'O campo :attribute é obrigatório. Por favor, preencha-o.',
            'email.unique' => 'Este email já está sendo utilizado.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'paroquia_id.required_if' => 'É obrigatório selecionar uma paróquia para um usuário do tipo "Responsável".',
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'required|boolean',
            'paroquia_id' => ['nullable', 'required_if:is_admin,0', 'exists:paroquias,id']
        ], $messages);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = $request->is_admin;
        $user->paroquia_id = $request->is_admin ? null : $request->paroquia_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', 'Usuário atualizado com sucesso!');
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->id == $user->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluido com sucesso!');
    }
}
