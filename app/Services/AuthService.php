<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }


    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        //utilizaremos sanctum para autenticação e proteção das rotas
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return ['user' => $user, 'token' => $token];

        }


        public function login(array $credentials)
        {
            $user = User::where('email',$credentials['email'])->first();

            if(!$user || !Hash::check($credentials['password'], $user->password))
            {  
                throw ValidationException::withMessages([
                'email' => ['As credenciais informadas estão incorretas.'],
            ]);
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return ['user' => $user, "token" => $token];
        }

}
