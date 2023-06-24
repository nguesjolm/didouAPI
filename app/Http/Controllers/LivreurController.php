<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class livreursController extends Controller
{
    /**
     * --------------------
     * COMMANDES livreurs 
     * --------------------
     */
      //Get all livreurs commndes
      function get_livreurs_comd_all(Request $request)
      {
         try {
            //Validated
            $validate_livreurs = Validator::make($request->all(),[
                'id_livreurs' => 'required'
            ]);
            if ($validate_livreurs->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreurs->errors(),
                                        ]);
            }
            return  get_livreurs_comd_all($request->id_livreurs);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }

      //Get livreurs commande by status
      function get_livreurs_comd_status(Request $request)
      {
        try {
            //Validated
            $validate_livreurs = Validator::make($request->all(),[
                'id_livreurs'      => 'required',
                'status_commande' => 'required'
            ]);
            if ($validate_livreurs->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreurs->errors(),
                                        ]);
            }
            return  get_livreurs_comd_status($request->id_livreurs,$request->status_commande);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }

      //Get livreurs commande today
      function  get_livreurs_today_command(Request $request)
      {
        try {
            //Validated
            $validate_livreurs = Validator::make($request->all(),[
                'livreurs_id' => 'required',
                'today_date' => 'required'
            ]);
            if ($validate_livreurs->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreurs->errors(),
                                        ]);
            }
            return  get_livreurs_today_command($request->livreurs_id,$request->today_date);
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
      function change_livreurs_comd_status(Request $request)
      {
        try {
            //Validated
            $validate_livreurs = Validator::make($request->all(),[
                'id_commande'    => 'required',
                'statut_commande' => 'required'
            ]);
            if ($validate_livreurs->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreurs->errors(),
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
     * NOTIFICATION livreurs 
     * --------------------
     */

    /**
     * --------------------
     * COMPTE livreurs 
     * --------------------
     */

    /**
     * --------------------
     * SOLDE livreurs 
     * --------------------
     */
       //Débiter le solde
       function  debiter_solde_livreurs(Request $request)
       {
        try {
            //Validated
            $validate_livreurs = Validator::make($request->all(),[
                'id_livreurs' => 'required',
                'montant'    => 'required'
            ]);
            if ($validate_livreurs->fails()) 
            {
                return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>'Erreur de validation',
                                         'data'=> '',
                                         'error'=> $validate_livreurs->errors(),
                                        ]);
            }
            return  debiter_solde_livreurs($request->id_livreurs,$request->montant);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       }

       //Get livreurs solde
       function get_livreurs_solde(Request $request)
       {
        try {
            $livreurs =  $request->id_livreurs;
            return getSinglelivreurs($livreurs);
        } catch (\Throwable $th) {
             //throw $th;
             return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       
       }

       //Get all livreurs transactions
       function get_livreurs_all_transactions(Request $request)
       {
         try {
            $livreurs =  $request->id_livreurs;
            return get_livreurs_all_transactions($livreurs); 
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
