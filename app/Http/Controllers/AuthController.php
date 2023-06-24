<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try 
                {
                    $validateUser = Validator::make($request->all(),
                    [
                        'email' => 'required|email',
                        'password' => 'required'
                    ]);

                    if ($validateUser->fails())
                    {
                        return response()->json([
                            'statusCode'=>401,
                            'status' => false,
                            'message' => 'Erreur de validation',
                            'errors' => $validateUser->errors()
                        ], 401);
                    }

                    if (!Auth::attempt($request->only(['email','password','role']))) 
                    {
                        return response()->json([
                            'statusCode'=>401,
                            'status' => false,
                            'message' => "L'email et le mot de passe ne correspond pas à nos enregistrement",
                        ], 401);
                    }

                    $user = User::where('email', $request->email)->first();
                    return response()->json([
                        'statusCode'=>200,
                        'status' => true,
                        'message' => "L'utilisateur s'est connecté avec succès",
                        'token' => $user->createToken("API TOKEN")->plainTextToken,
                        'token_type' => 'Bearer',
                    ], 200);

                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statusCode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
    }
}
