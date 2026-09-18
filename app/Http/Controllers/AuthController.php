<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $error = false;
        $message = '';
        $token = null;
        $user = null;

        $val_usuario = User::where('email', $request->email)->first();
        if ($val_usuario) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                if (empty($val_usuario->categoria)) {
                    $error = true;
                    $message = 'Lo sentimos, pero su usuario no tiene un rol asignado, por favor comuniquese con el administrador del sistema para que le asigne uno.';
                } else {
                    if($val_usuario->active == 1){
                        $user = Auth::user();
                        $token = $user->createToken('token', ['*'], now()->addDays(1))->plainTextToken;
                    } else {
                        $error = true;
                        $message = 'Lo sentimos, actualmente estas inactivo, no podras iniciar sesión, para mayor información comunicate con nosotros!';
                    }
                }
            } else {
                $error = true;
                $message = 'Error! No coinciden las credenciales ingresadas, intente de nuevo.';
            }
        } else {
            $error = true;
            $message = 'Error! No existe ningun usuario registrado con este correo.';
        }

        return [
            'error' => $error,
            'message' => $message,
            'token' => $token,
            'user' => $user
        ];
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return [
            'user' => null
        ];
    }

}
