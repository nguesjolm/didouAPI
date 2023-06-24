<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class ClientController extends Controller
{
    /**
     * --------------------
     * USER AUTHENTICATION 
     * --------------------
     */

        /**
         * Create User
         * @param Request $request
         * @return Client 
        */
        function create_client_Count(Request $request)
        {
            try {
                //validated
                $validateClient = Validator::make($request->all(),[
                    'nom' => 'required|min:4',
                    'email' => 'email|unique:clients',
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
                    'role'     => 'client',
                    'password' => $password
                ]);

                $client = Client::create(['status'        => 1,
                                          'ambassadeur'        => $request->parain,
                                          'iduser'        => $user->id,
                                          'date_creation' => $request->date_creation
                                        ]);
                
                return response()->json([
                                         'statusCode' =>200,
                                         'status'     => true,
                                         'message'    => 'Ouverture de compte client effectué  avec succès',
                                         'dataclient' => $user,
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

        //Login user
        function loginUserCount(Request $request)
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
                                         'message' => "Le client s'est connecté avec succès",
                                         'dataclient' => $user,
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

        //Generate OTP code
        function generateOTP(Request $request)
        {
            try {
                $tel = $request->tel;
                $user = User::where('tel', $request->tel)->first();
                if ($user) {
                    # Send sms
                    $msg = generateOTP().": Votre code de validation Didou";
                    Sendsms($msg,"225".$tel,"DIDOU");
                    return response()->json(['statusCode' => 200,
                                             'status'     => true,
                                             'data_user'  => $user,
                                             'message'    => $msg,
                                            ], 200); 
                }else {
                    return response()->json([
                        'statusCode'=>401,
                        'status' => false,
                        'message' => "Le numéro de téléphone ne correspond pas à nos enregistrement",
                    ], 401);
                }
            } catch (\Throwable $th) {
                //throw $th;
                //throw $th;
                return response()->json([
                    'statusCode'=>500,
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
            }
        }

        //Check OTP code
        function checkOTP(Request $request)
        {
           try {
                //Validated
                $valideOTP = Validator::make($request->all(),
                [
                    'OTP' => 'required'
                ]);
                if ($valideOTP->fails())
                {
                    return response()->json([
                        'statusCode'=>401,
                        'status' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $valideOTP->errors()
                    ], 401);
                }

                //Check
                $res = checkOTP($request->OTP);
                if ($res) {
                    updateOTP($request->OTP);
                    return response()->json([
                        'statusCode' =>200,
                        'status'     => true,
                        'datacode' => $res,
                        'message'    => "Réinitialiser votre mot de passe",
                    ], 200);
                }else{
                    updateOTP($request->OTP);
                    return response()->json([
                        'statusCode'=>401,
                        'status' => false,
                        'message' => "Code erroné, veuillez générer un nouveau code",
                    ], 401);
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

        //Resset password
        function newpassword(Request $request)
        {
            try {
                //validated
                $validateUser = Validator::make($request->all(),
                [
                    'iduser'   => 'required',
                    'password' => 'required|min:8',
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

                if ($request->password!='') 
                {
                    User::where('id', $request->iduser)
                         ->update(['password' => $request->password]);
                    
                    return response()->json(['statusCode'=>200,
                                             'status' => true,
                                             'message' => "Mise à jour effectuée avec succès, veuillez vous connectez avec votre nouveau mot de passe",
                                            ], 200);
    
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

    /**
     * ----------------
     *  PARAMETRES
     * --------------
     */
       //get user infos
       function getclientInfos()
       {
         $user = Auth::user();
         $client = getSingleClientsUser($user->id);
         return response()->json(['statusCode'=>'200',
                                  'status'=>'true',
                                  'message'=>"profil de l'utilisateur connecté",
                                  'data_user'=> $user,
                                  'data_client'=> $client,
                                  'error'=> '',
                                ]);
       }

       //Update user infos
       function updateclientInfos(Request $request)
       {
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

            return response()->json([
                                    'statusCode'=>200,
                                    'status' => true,
                                    'message' => "Mise à jour effectuée avec succès",
                                ], 200);
            
       }
    
    /**
     * ------------------
     *  CREDIT DIDOU
     * ------------------
     */
       //Get all user's credit
       function getAllcreditUser(Request $request)
       {
          try {
            return getAllUSerCredit($request->clientid);
          } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
          }
       }
       //Rembourser user's credit
       function rembourserCreditUser(Request $request)
       {
        try {
            return rembourserCreditUser($request->code_credit);
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
     * ----------------
     *  COMMANDE CLIENT
     * ----------------
     */
      //Add client command
      function addClientComd(Request $request)
      {
        try {
            //Validated
            $validateComd = Validator::make($request->all(),[
                'clientid'   => 'required',
                'montant' => 'required',
                'qte' => 'required|min:1',
                'gps' => 'required',
                'zoneid' => 'required',
                'dateComd' => 'required',
                'statutClient' => 'required',
                'data_recettes' => 'required|min:1'
            ]);

            if ($validateComd->fails())
            {
                return response()->json([
                    'statusCode'=>401,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validateComd->errors()
                ], 401);
            }
            $clientid         = $request->clientid;
            $ambassadeur_code = $request->ambassadeur_code;
            $credit_didou     = $request->credit_didou;
            $montant          = $request->montant;
            $precision_plats  = $request->precision_plats;
            $qte              = $request->qte;
            $gps              = $request->gps;
            $zoneid           = $request->zoneid;
            $dateComd         = $request->dateComd;
            $statutClient     = $request->statutClient;
            $data_recettes    = $request->data_recettes;
            $montantPay       = $montant;

            //Step 1 : vérifier le credit_didou + Faire une réduction du prix de la commande avec le montant de crédit paramétré et définir le nouveau montant
            if ($credit_didou!='') {
                $dataCredit = CheckCredit($credit_didou,$clientid);
                //Check crédit non utilisé
                if ($dataCredit->credit_used=="true") {
                    return response()->json([
                        'statusCode'=>401,
                        'status' => false,
                        'message' => "Crédit Didou invalide, le code a déjà été utilisé",
                        'errors' => $validateComd->errors()
                    ], 401);
                }else{
                   
                    //Montant crédit
                    $montant_credit = $dataCredit->montant;
                    //Vérification du montant
                    if($montant_credit > $montant)
                    {
                        return response()->json([
                            'statusCode'=>401,
                            'status' => false,
                            'message' => "Le montant minimum de la commande doit être :".$montant_credit." Fcfa pour utiliser le crédit Didou",
                            'errors' => $validateComd->errors()
                        ], 401);
                    }
                    elseif ($montant_credit <= $montant)
                    {
                        $montantPay = $montant - $montant_credit;
                        
                    }
                }
               
            }
            if ($montantPay==0 && $credit_didou!=''&&$dataCredit->credit_used=="false") {
               
            
                    # Change credit_didou_status used on : true => utilisé
                    credit_used_status($credit_didou,"true");
                    # Enregistrer la commande
                    $numComd = NumComd();
                    saveCommand($clientid,$ambassadeur_code,$credit_didou,$montant,$qte,$gps,$zoneid,$dateComd,$statutClient,$numComd,$precision_plats);
                    # Enregistrer chaque produit de la commande
                    foreach ($data_recettes as $key => $value) 
                    {
                         savecomprod($value['platId'],$value['qte'],$value['amount'],$numComd,$clientid);
                    }
                    # Créditer solde ambassadeur
                    if ($ambassadeur_code!='') 
                    {
                        creditersoldAmbasad($ambassadeur_code);
                    }
                     #Retour
                     return response()->json(['statusCode'=>'200',
                                         'status'=>'true',
                                         'message'=>"Commande validée avec succès",
                                         'data'=> '',
                                         'error'=> '',
                                        ]);
                
                
            }else{
                /*Lancer le paiement CinetPay*/
                 # Etape 1 : Prépartion des paramètres du Guichet de paiement
                 $user = Auth::user();
                 $client = getSingleClientsUser($user->id);
                 $transaction_id = date("YmdHis");
                 $description_trans="Paiement de commande Didou";
                 $client_name = $user->name;
                 $client_surname =  $user->name;
                 $client_phone = $user->tel;
                 $client_email = $user->email?:support();
                 $notify_url = "notify_pay";
                 $return_url = "return_pay";
                    # Etape 2 : Enregistrer la transaction et le paiement
                     $type_paiement = "commande";
                     #Save payment
                     savePay($transaction_id,$type_paiement,$montant,$description_trans,$client_name,$client_surname,$client_phone,$client_email);
                     #Save transaction
                     saveTransaction($clientid,$ambassadeur_code,$credit_didou,$montant,$qte,$gps,$zoneid,$dateComd,$statutClient,$data_recettes,$transaction_id);
                    # Etape 3 : Lancer le Guichet de paiement
                     return  Guichet($transaction_id,$montant,$description_trans,$client_name,$client_surname,$client_phone,$client_email,$notify_url,$return_url);

                 
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
      //Cancel client command
      function cancelClientComd(Request $request)
      {
        try {
                //validated
                $validate_Comd = Validator::make($request->all(),[
                    'statut_client' => 'required',
                    'id_commandes'   => 'required',
                ]);

                if ($validate_Comd->fails())
                {
                    return response()->json([
                        'statusCode'=>401,
                        'status' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $validate_Comd->errors()
                    ], 401);
                }

                return updateClientComdstatus($request->statut_client,$request->id_commandes);

        } catch (\Throwable $th) {
            //throw $th;
        }
      }
      //Note client command
      function createAvis(Request $request)
       {
            try {
                //Validated
                $validate_avis = Validator::make($request->all(),[
                    'mentions'     => 'required',
                    'commentaires' => 'required|min:8',
                ]);
                if ($validate_avis->fails())
                {
                    return response()->json([
                        'statusCode'=>401,
                        'status'    => false,
                        'message'   => 'Erreur de validation',
                        'errors'    => $validate_avis->errors()
                    ], 401);
                }
                $mentions = $request->mentions;
                $commentaires = $request->commentaires;
                $datecommantaires = date('d-m-Y');
                return createAvis($mentions,$commentaires,$datecommantaires);
            } catch (\Throwable $th) {
               //throw $th;
               return response()->json(['statusCode'=>500,
                                         'status' => false,
                                         'message' => $th->getMessage()
                                       ], 500);
            }
          
       }
      //Get client command status
      function getClientComdstatus(Request $request)
      {
         try {
            //validated
            $validate_Comd = Validator::make($request->all(),[
                'commande_status' => 'required',
                'client_id'   => 'required',
            ]);
            if ($validate_Comd->fails())
            {
                return response()->json(['statusCode'=>401,
                                         'status' => false,
                                         'message' => 'Erreur de validation',
                                         'errors' => $validate_Comd->errors()
                                       ], 401);
            }

            return getClientComdstatus($request->client_id,$request->commande_status);
         } catch (\Throwable $th) {
            //throw $th;
         }
      }
      //Get client's all command
      function getClientComdAll(Request $request)
      {
        try {
             //Validated
             $validate_client = Validator::make($request->all(),[
                'client_id'     => 'required'
             ]);
             if ($validate_client->fails())
             {
                return response()->json([
                    'statusCode'=>401,
                    'status'    =>false,
                    'message'   =>'Erreur de validation',
                    'errors'    =>$validate_client->errors()
                ], 401);
             }
             
             return getClientComdAll($request->client_id);

         } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['statusCode'=>500,
                                     'status' => false,
                                     'message' => $th->getMessage()
                                    ], 500);
         }
      }
      //Change client command status
      function updateClientComdstatus(Request $request)
      {
       
        try {
            //validated
            $validate_Comd = Validator::make($request->all(),[
                'statut_commande' => 'required',
                'id_commandes'   => 'required',
            ]);

            if ($validate_Comd->fails())
            {
                return response()->json([
                    'statusCode'=>401,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Comd->errors()
                ], 401);
            }

            return updateClientComdstatus($request->statut_commande,$request->id_commandes);

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
     * -------------
     *  AMBASSADEUR
     * -------------
     */
      //Débiter solde ambassadeur
      function debiterAmbassadeur(Request $request)
      {

        //Validated
        $validatedAmb = Validator::make($request->all(),[
            'code_ambassadeur' => 'required',
            'montant'          => 'required',
        ]);
        if ($validatedAmb->fails())
        {
            return response()->json([
                'statusCode'=>401,
                'status' => false,
                'message' => 'Erreur de validation',
                'errors' => $validatedAmb->errors()
            ], 401);
        }

        return debiterSoldAmbasad($request->code_ambassadeur,$request->montant);
      }
    
    /**
     * -------------------------
     *  NOTIFICATION PUSH CLIENT
     * -------------------------
     */
      //Add Push
      function addUserPush(Request $request)
      {
         try {
            $validate_Push = Validator::make($request->all(),[
                'titre'     => 'required',
                'message'   => 'required',
                'state'     => 'required',
                'id_user'   => 'required'
            ]);

            if ($validate_Push->fails())
            {
                return response()->json([
                    'statusCode'=>401,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Push->errors()
                ], 401);
            }
            $titre   = $request->titre;
            $message = $request->message;
            $state   = $request->state;
            $id_user = $request->id_user;
          
            return addUserPush($titre,$message,$state,$id_user);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }
      //Get all Push
      function getUserPush(Request $request)
      {
         try {
            $validate_Push = Validator::make($request->all(),[
                'id_user' => 'required'
            ]);

            if ($validate_Push->fails())
            {
                return response()->json([
                    'statusCode'=>401,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Push->errors()
                ], 401);
            }

            return getUserPush($request->id_user);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode'=>500,
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
         }
      }
      //Delete Push
      function deleteUserPush(Request $request)
      {
        try {
            $validate_Push = Validator::make($request->all(),[
                'id_push' => 'required'
            ]);

            if ($validate_Push->fails())
            {
                return response()->json([
                    'statusCode'=>401,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Push->errors()
                ], 401);
            }
            
            return deleteUserPush($request->id_push);
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
     * -------------------------
     *  PAIEMENT SYSTEM CONTROLE
     * -------------------------
     */
       //notify
       function notify(Request $request)
       {
            //Id transaction
             $id_transaction = $request->cpm_trans_id; 	
            //apiKey
             $apikey = apikey();
            //Veuillez entrer votre siteId
             $site_id = siteID();
            //Version
             $version = "V2";
            //Verification du paiement
             $pay = checkPayment($id_transaction);
            
            if ($pay->state!=1) {
              //Nouveau paiement
              $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment/check',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    #curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0),
                    #curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>     '{
                        "transaction_id":"'.$id_transaction.'",
                        "site_id": "'.$site_id.'",
                        "apikey" : "'.$apikey.'"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
             
                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response);

                #Traitement en cas de paiement succès
                if ($result->{'code'}=='00') {
                    # Cas 1 : Paiement de commande
                    
                }
            }
       }

       //return
       function return_pay()
       {

       }
    
}
