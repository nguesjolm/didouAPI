<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livreur;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LivreurController extends Controller
{
    /**
     * --------------------
     * COMMANDES LIVREUR 
     * --------------------
     */
      //Get all livreur commndes
      function get_livreur_comd_all(Request $request)
      {
         try {
            //Validated
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
            return  get_livreur_comd_all($livreur->idlivreur);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }

      //Get livreur commande by status
      function get_livreur_comd_status(Request $request)
      {
        try {
            

            $user = Auth::user();
            $livreur = getLivreurID($user->id);
           
            return  get_livreur_comd_status($livreur->idlivreur,$request->status_commande);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }

      //Get livreur commande today
      function  get_livreur_today_command(Request $request)
      {
        try {
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
           
            return  get_livreur_today_command($livreur->idlivreur,date('d/m/Y'));
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }

      //Change commande state
      function change_livreur_comd_status(Request $request)
      {
        try {
            //Validated
            $validate_livreur = Validator::make($request->all(),[
                'id_commande'    => 'required',
                'statut_commande' => 'required'
            ]);
            if ($validate_livreur->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreur->errors(),
                                        ]);
            }
            return  updateClientComdstatus($request->statut_commande,$request->id_commande);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }



    /**
     * --------------------
     * COMPTE LIVREUR 
     * --------------------
     */

    /**
     * -------------------
     *  AUTHENTICATION
     * -------------------
     */
      //inscription livreur
      function inscription_livreur(Request $request)
      {
        try {
            //validated
            $validateClient = Validator::make($request->all(),[
                'nom' => 'required|min:4',
                'email' => 'email|unique:users',
                'tel' => 'required|min:10|max:10|unique:users',
                'password' => 'required|min:8',
            ]);
            if ($validateClient->fails()) 
            {
                    return response()->json(['statusCode'=>'401',
                                            'status'=>'false',
                                            'message'=>'Erreur de validation',
                                            'data'=> '',
                                            'error'=> $validateClient->errors(),
                                            ]);
            }
            $password = Hash::make($request->password);
            $user = User::create([
                'name'     => $request->nom,
                'tel'      => $request->tel,
                'role'     => 'livreur',
                'password' => $password
            ]);

            Livreur::create(['status'  => "true",
                             'local'   => $request->zone_id,
                             'id_user' => $user->id,
                            ]);
            
            return response()->json([
                                     'statusCode' =>200,
                                     'status'     => true,
                                     'message'    => 'Ouverture de compte livreur effectué  avec succès',
                                     'token'      => $user->createToken("API TOKEN")->plainTextToken
                                    ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
      }
      //Connection livreur
      function login_livreur(Request $request)
      {
        try {
            //validated
            $valideClient = Validator::make($request->all(),[
                'tel' => 'required|min:10|max:10',
                'password' => 'required',
            ]);
            if ($valideClient->fails()) 
            {
                    return response()->json(['statusCode'=>'401',
                                             'status'=>'false',
                                             'message'=>'Erreur de validation',
                                             'data'=> '',
                                             'error'=> $valideClient->errors(),
                                            ]);
            }

            if (!Auth::attempt($request->only(['tel','password']))) 
            {
                    return response()->json([
                        'statusCode'=>401,
                        'status' => false,
                        'message' => "Le numéro et le mot de passe ne correspondent pas à nos enregistrement",
                    ], 401);
            }

            $user = User::where('tel', $request->tel)->first();
           
            return response()->json([
                                     'statusCode'=>200,
                                     'status' => true,
                                     'message' => "connecté avec succès",
                                     'token' => $user->createToken("API TOKEN")->plainTextToken,
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
      //Get livreur infos
      function get_livreur_info(Request $request)
      {
       
        $user = Auth::user();
        $livreur = getSingleLivreur($user->id);
        return response()->json(['statusCode'=>'200',
                                 'status'=>'true',
                                 'message'=>"profil du livreur connecté",
                                 'data_user'=> $user,
                                 'data_livreur'=> $livreur,
                                 'error'=> '',
                                ]);
      }

      //Update livreur infos
      function update_livreur_info(Request $request)
      {
       
        //Validated
        $validateLivreur = Validator::make($request->all(),[
          'email' => 'email|unique:users',
          'tel' => 'min:10|max:10|unique:users',
        ]);
        if ($validateLivreur->fails()) 
        {
                return response()->json(['statusCode'=>'401',
                                        'status'=>'false',
                                        'message'=>'Erreur de validation',
                                        'data'=> '',
                                        'error'=> $validateLivreur->errors(),
                                        ]);
        }

         $password = Hash::make($request->password);
         $user = Auth::user();

         if ($request->name!='') {
                User::where('id', $user->id)
                     ->update(['name' => $request->name]);
         }

         if ($request->email!='') {
            User::where('id', $user->id)
                 ->update(['email' => $request->email]);
         }

         if ($request->tel!='') {
            User::where('id', $user->id)
                 ->update(['tel' => $request->tel]);
         }

         if ($request->password!='') {
            User::where('id', $user->id)
                 ->update(['password' => $password]);
         }

         if ($request->file('photo')!='') {
            //Traitement des fichiers
            $file = $request->file('photo');
            $lien = env('LIEN_FILE');
            $path = $file->store('Livreur','public');
            $photo = $lien.$path;
            //Update
            Livreur::where('id_user', $user->id)
                     ->update(['photo' => $photo]);
         }





      }

    /**
     * --------------------
     * SOLDE LIVREUR 
     * --------------------
     */
       //Débiter le solde
       function  debiter_solde_livreur(Request $request)
       {
        try {
            //Validated
            $validate_livreur = Validator::make($request->all(),[
                'montant'    => 'required'
            ]);
            if ($validate_livreur->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreur->errors(),
                                        ]);
            }
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
            return  debiter_solde_livreur($livreur->idlivreur,$request->montant);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       }

        //Créditer de solde livreur
        function crediterSoldeLiv(Request $request)
        {
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
            return crediterSoldeLiv($livreur->idlivreur);
        }   

       //Get livreur solde
       function get_livreur_solde(Request $request)
       {
        try {
            $user = Auth::user();
            return getSingleLivreur($user->id);
        } catch (\Throwable $th) {
             //throw $th;
             return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       
       }

       //Get all livreur transactions
       function get_livreur_all_transactions(Request $request)
       {
         try {
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
            return get_livreur_all_transactions($livreur->idlivreur); 
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
