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

      //Get livreur commande today by status
      function get_livreur_today_command_status(Request $request)
      {
        try {
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
            return  get_livreur_today_command_status($livreur->idlivreur,date('d/m/Y'),$request->status);
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
                return response()->json(['statusCode'=>'404',
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
                    return response()->json(['statusCode'=>'404',
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
                    return response()->json(['statusCode'=>'404',
                                             'status'=>'false',
                                             'message'=>'Erreur de validation',
                                             'data'=> '',
                                             'error'=> $valideClient->errors(),
                                            ]);
            }

            if (!Auth::attempt($request->only(['tel','password']))) 
            {
                    return response()->json([
                        'statusCode'=>404,
                        'status' => false,
                        'message' => "Le numéro et le mot de passe ne correspondent pas à nos enregistrement",
                    ], 404);
            }

            $user = User::where('tel', $request->tel)->first();
            $livreur = getLivreurInfo($user->id);
            #Generer FCM Token
            if ($request->tokenFCM)
            {
                #Mise à jour du token
                updateTokenFCM($livreur->idlivreur,$request->tokenFCM);
            }
            return response()->json([
                                     'statusCode'=>200,
                                     'status'  => true,
                                     'message' => "connecté avec succès",
                                     'data'    => $user,
                                     'livreur' => $livreur,
                                     'token'   => $user->createToken("API TOKEN")->plainTextToken,
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
        $livreur = getLivreurInfo($user->id);
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
       
         $user = Auth::user();

         if ($request->name!='') {
                User::where('id', $user->id)
                     ->update(['name' => $request->name]);
         }

        //  if ($request->email!='') {
        //     //Validated
        //     $validateLivreur = Validator::make($request->all(),[
        //       'email' => 'email|unique:users',
        //     ]);
        //     if($validateLivreur->fails()) 
        //     {
        //             return response()->json(['statusCode'=>'404',
        //                                      'status'=>'false',
        //                                      'message'=>'Erreur de validation',
        //                                      'data'=> '',
        //                                      'error'=> $validateLivreur->errors(),
        //                                     ]);
        //     }
        //     User::where('id', $user->id)
        //          ->update(['email' => $request->email]);
        //  }

        //  if ($request->tel!='') {
           
        //     Validated
        //     $validateLivreur = Validator::make($request->all(),[
        //         'tel' => 'required|min:10|max:10|unique:users'
        //     ]);

        //     if($validateLivreur->fails()) 
        //     {
        //             return response()->json(['statusCode'=>'404',
        //                                      'status'=>'false',
        //                                      'message'=>'Erreur de validation',
        //                                      'data'=> '',
        //                                      'error'=> $validateLivreur->errors(),
        //                                     ]);
        //     }

        //     User::where('id', $user->id)
        //         ->update(['tel' => $request->tel]);
        //  }

        


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

         if ($request->local!='') {
            $local = $request->local;
            Livreur::where('id_user',$user->id)
                   ->update(['local' => $request->local]);
         }

         $livreur = getLivreurInfo($user->id);
         $user = User::where('id', $user->id)->first();
         return response()->json(['statusCode'=>'200',
                                 'status'  =>'true',
                                 'data'    => $user,
                                 'livreur' => $livreur,
                                 'message' =>'Modification effectuée avec succès',
                                ]);




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
                return response()->json(['statusCode'=>'404',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreur->errors(),
                                        ]);
            }
            $user = Auth::user();
            $livreur = getLivreurID($user->id);
            #Préparation du Guichet de paiement
            $transfert_id = date("YmdHis");
            $phone = $user->tel;
            $name  = $user->name;
            $email = $user->email ?? support();
            $type = 'livreur';
            $profil_id = $livreur->idlivreur;
            $montant = $request->montant;
            $payment_method = $request->payment_method;
            #Lancement du Guichet de paiement
            if ($livreur->solde > $montant ) {
                $res = GuichetPayOut($transfert_id,$phone,$montant,$name,$email,$type,$payment_method,$profil_id);
               if ($res->code==0) {
                    return response()->json(['statusCode'=>200,
                                            'status'=>true,
                                            'message'=>"Paiement effectué avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                        ],200);
               }
               if ($res->code==-1) {
                return response()->json(['statusCode'=>400,
                                        'status'=>false,
                                        'message'=>"Paiement refusé, veuillez ressayer plutard",
                                        'data'=> '',
                                        'error'=> '',
                                    ],400);
               }
               if ($res->code==602) {
                return response()->json(['statusCode'=>400,
                                         'status'=>false,
                                         'message'=>"Paiement non actif pour le moment",
                                         'data'=> '',
                                         'error'=> '',
                                        ],400);
               }
               if ($res->code==804) {
                return response()->json(['statusCode'=>400,
                                         'status'=>false,
                                         'message'=>"Transaction echoué, le moyen de paiement choisi est indisponible",
                                         'data'=> '',
                                         'error'=> '',
                                        ],400);
               }
               return response()->json(['statusCode'=>400,
                                        'status'=>false,
                                        'message'=>"Une erreur s'est produite, veuillez ressayer plutard",
                                        'data'=> '',
                                        'error'=> '',
                                    ],400);
            }else{
               
                return response()->json(['statusCode'=>404,
                                        'status'=>false,
                                        'message'=>"Votre solde est insuffisant",
                                        'data'=> '',
                                        'error'=> '',
                                      ],404);
            }
            
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
