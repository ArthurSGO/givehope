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

    public function createUserForm()
    {
        return view("admin.users.create");
    }

    public function storeUser(Request $request)
    {
        $messages = [
            'required' => 'O campo :attribute é obrigatório. Por favor, preencha-o.',
            'email.unique' => 'Este email já está sendo utilizado.',
            'name.regex' => 'O campo Nome não pode conter números.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
        ];
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚçÇãõÃÕàèìòùÀÈÌÒÙº\.\-]+$/u'],
            'email'=> ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string','min:8','confirmed'],
            'is_admin' => ['required', 'boolean'],
        ], $messages);

        User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'is_admin' => $request->is_admin,
        ]);

        return redirect()->route('admin.dashboard')->with('success','Novo usuário criado com sucesso');
    }

    public function listUser()
    {
        $users = User::all();
        
        return view('admin.users.list', compact('users'));
    }
}
