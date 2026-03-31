<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $users = User::where('is_admin', false)->latest()->get();
        return response()->json($users);
    }

    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $user->delete();    
        return response()->json(['message' => 'Usuário excluído com sucesso.']);
    }
}