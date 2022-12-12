<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //Registro de nuevo usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' =>  'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        if(!$validator->fails()){
            DB::beginTransaction();
            try {
                //Set data
                $user = new User();
                $user->name = $request->name;
                $user->email = trim($request->email);
                $user->password = Hash::make($request->password);
                $user->save();
                DB::commit();
                return response()->json([
                    'message' => "Usuario creado con exito",
                    'data' => $user,
                ], 200);
            } catch (Exception $e) {
                DB::rollBack();
                return \response($e);
            }
        }else{
            return \response([$validator->errors()]);
        }
    }

    //Método de login de usuario
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' =>  'required|email',
            'password' => 'required'
        ]);

        if(!$validator->fails()){
            $user = User::where('email', '=', $request->email)->first();
            if(isset($user->id)){
                if(Hash::check($request->password, $user->password)){
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'message' => "Acceso concedido, token generado",
                        'token' => $token,
                        'data' => $user
                    ], 200);
                }else{
                    return \response("Datos de acceso incorrectos");
                }
            }else{
                return \response("Datos de acceso incorrectos");
            }
        }
    }

    //Cerrar sesión del usuario
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => "Cierre de sesión exitoso"
        ], 200);
    }

    //Consulta de datos de un usuario en específico
    public function userProfile()
    {
        return \response(auth()->user());
    }

    //Cambio de contraseña, que de momento no es relevante para nosotros
    public function changePassword(Request $request){
        $validator = Validator::make($request->all(),[
            'password' => 'required|confirmed'
        ]);

        if(!$validator->fails()){
            DB::beginTransaction();
            try{
                $user = auth()->user();
                $user->password = Hash::make($request->password);
                $user->update();
                $request->user()->tokens()->delete();
                DB::commit();
                return \response($user);

            }catch(Exception $e){
                return \response("500: Internal server error");
                DB::rollBack();
            }
        }
    }
}
