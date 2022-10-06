<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credenciais = $request->all(['email', 'password']);

        $token = auth('api')->attempt($credenciais);

        if($token){//Usuário autentificado com sucesso
            return response()->json(['token' => $token], 200);
        }else{//erro ou senha
            return response()->json(['erro' => 'Email ou senha invalidas'], 403);
        }
    }

    public function logout()
    {
        return 'logout';

    }

    public function refresh()
    {
        return 'refresh';
    }

    public function me()
    {
        return 'me';
    }
}
