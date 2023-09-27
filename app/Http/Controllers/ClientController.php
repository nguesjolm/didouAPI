<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use App\Cinetpay\CinetPayService;
use App\Cinetpay\CinetPayService\CinetPayService as CinetPayServiceCinetPayService;

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
        function createClientCount(Request $request)
        {
            try {

                $validateTel = Validator::make($request->all(),['tel' => 'required|min:10|max:10|unique:users']);
                if ($validateTel->fails()) {
                    $errorMsg="";
                    if (strlen($request->tel)!=10) {
                        $errorMsg = "le numéro doit comporter 10 chiffre";
                    }else{
                        $errorMsg = "le numéro existe déjà";
                    }
                    return response()->json(['statusCode'=>'406',
                                                    'status'=>'false',
                                                    'message'=>$errorMsg
                                                    ],406);
                }

                $validateEmail = Validator::make($request->all(),['email' => 'email|unique:users']);
                if ($validateEmail->fails()) {
                    $errorMsg ='Cet email existe déjà';
                    return response()->json(['statusCode'=>'406',
                                                    'status'=>'false',
                                                    'message'=>$errorMsg
                                                    ],406);
                }

                $validateNom = Validator::make($request->all(),['nom' => 'required|min:4']);
                if ($validateNom->fails()) {
                    // $errorMsg ='Cet email existe déjà';
                    if (strlen($request->nom)<4) {
                        $errorMsg = "le nom doit comporter au moins 4 caractères";
                    }else{
                        $errorMsg = "le nom est obligatoire";
                    }
                    return response()->json(['statusCode'=>'406',
                                                    'status'=>'false',
                                                    'message'=> $errorMsg
                                                    ],406);
                }

                $validatePass = Validator::make($request->all(),['password' => 'required|min:8']);
                if ($validatePass->fails()) {
                    if (strlen($request->password)<8) {
                        $errorMsg = "le mot de passe doit comporter au moins 8 caractères";
                    }else{
                        $errorMsg = "le mot de passe est obligatoire";
                    }
                    return response()->json(['statusCode'=>'406',
                                            'status'=>'false',
                                            'message'=> $errorMsg
                                            ],406);
                }

                if($request->password != $request->password2)
                {
                    return response()->json(['statusCode'=>'406',
                                            'status'=>'false',
                                            'message'=> 'Mot de passe doit être identique'
                                            ],406);
                }

                $password = Hash::make($request->password);
                $user = User::create([
                    'name'     => $request->nom,
                    'tel'      => $request->tel,
                    'role'     => 'client',
                    'password' => $password
                ]);

                $client = Client::create(['status'        => 'inactif',
                                          'ambassadeur'   => $request->parain,
                                          'iduser'        => $user->id,
                                          'tokenFCM'      => '',
                                          'date_creation' => date('d/m/Y')
                                        ]);
                $otpmsg = generateOTP($request->tel).": Votre code de validation Didou";
                Sendsms($otpmsg,"225".$request->tel,"DIDOU");
                return response()->json([
                                         'statusCode' =>200,
                                         'status'     => true,
                                         'message'    => 'Ouverture de compte client effectué  avec succès '.$otpmsg,
                                         'user'       => $user,
                                         'infoclient' => $client,
                                         'otp'        => $otpmsg,
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

        //Update client TokenFCM
        function updateTokenFCM(Request $request)
        {
           try {
              updateFCM($request->idclient,$request->token);
              return response()->json([
                 'info'   => 'FCM TOKEN added',
                 'token'  => $request->token,
                 'status' => true
              ], 200);
            } catch (\Throwable $th) {
             //throw $th;
             return response()->json([
                'error' => 'Error adding FCM TOKEN',
                'message' => $th->getMessage()
             ], 500);
            }
        }

        //Login user
        function loginUserCount(Request $request)
        {
        
            try {
                //validated
                $validateTel = Validator::make($request->all(),['tel' => 'required|min:10|max:10']);
                if ($validateTel->fails()) {
                    $errorMsg="";
                    if (strlen($request->tel)!=10) {
                        $errorMsg = "le numéro doit comporter 10 chiffre";
                    }else{
                        $errorMsg = "le numéro existe déjà";
                    }
                    return response()->json(['statusCode'=>'406',
                                                    'status'=>'false',
                                                    'message'=>$errorMsg
                                                    ],406);
                }

                $validatePass = Validator::make($request->all(),['password' => 'required|min:8']);
                if ($validatePass->fails()) {
                    if (strlen($request->password)<8) {
                        $errorMsg = "le mot de passe doit comporter au moins 8 caractères";
                    }else{
                        $errorMsg = "le mot de passe est obligatoire";
                    }
                    return response()->json(['statusCode'=>'406',
                                            'status'=>'false',
                                            'message'=>$errorMsg
                                            ],406);
                }

                if (!Auth::attempt($request->only(['tel','password']))) 
                {
                        return response()->json([
                            'statusCode'=>404,
                            'status' => false,
                            'message' => "Le numéro ou le mot de passe ne correspondent pas à nos enregistrement",
                        ], 404);
                }

                $user = User::where('tel', $request->tel)->first();
                $client = Client::firstWhere('iduser',$user->id);
                if ($user->role=='client'&& $client->status=='actif') {
                    return response()->json([
                        'statusCode'     =>200,
                        'status'         => true,
                        'message'        => "Le client s'est connecté avec succès",
                        'data_user'      => $user,
                        'client'         => $client,
                        'token'          => $user->createToken("API TOKEN")->plainTextToken,
                       ], 200);
                }else{
                    return response()->json([
                        'statusCode'  =>402,
                        'status'      => true,
                        'message'     => "Compte inactif",
                        'data_client' => '',
                        'client'      => '',
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

        //Generate OTP code
        function generateOTP(Request $request)
        {
            try {
                $tel = $request->tel;
                $user = User::where('tel', $request->tel)->first();
                if ($user) {
                    # Send sms
                    $msg = generateOTP($request->tel).": Votre code de validation Didou";
                    Sendsms($msg,"225".$tel,"DIDOU");
                    return response()->json(['statusCode' => 200,
                                             'status'     => true,
                                             'message'    => $msg
                                            ], 200); 
                }else {
                    return response()->json([
                        'statusCode'=>404,
                        'status' => false,
                        'message' => "Le numéro de téléphone ne correspond pas à nos enregistrement",
                    ], 404);
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

        //Check OTP code
        function checkOTP(Request $request)
        {
           
           try {
                //Check
                $res = checkOTP($request->otp,$request->tel);
                if ($res) {
                    $livreur = '';
                    $client ='';
                    updateOTP($request->otp);
                    $user = User::where('tel', $request->tel)->first();
                    if($user->role=='client'){
                        Client::where('iduser', $user->id)->update(['status' => 'actif']);
                        $client =  client::firstWhere('iduser', $user->id);
                    }
                    if ($user->role=='livreur') {
                        $livreur = getLivreurInfo($user->id);
                    }
                    
                    return response()->json([
                        'statusCode' =>200,
                        'status'     => true,
                        'message'    => "Compte recupéré avec succès",
                        'data'       => $user,
                        'livreur'    => $livreur,
                        'client'     => $client,
                        'token'      => $user->createToken("API TOKEN")->plainTextToken,
                    ], 200);
                }else{
                    updateOTP($request->otp);
                    return response()->json([
                        'statusCode'=>404,
                        'status' => false,
                        'message' => "Code erroné",
                    ], 404);
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
                    'password' => 'required|min:8',
                ]);

                if ($validateUser->fails())
                {
                    return response()->json([
                        'statusCode'=>406,
                        'status' => false,
                        'message' => 'Erreur de validation',
                        'errors' =>'Le mot de passe est obligatoire'
                    ], 406);
                }

                  $user = Auth::user();
                  $pass_new = Hash::make($request->password);
                  #Comparer l'ancien mot de passe saisi au mot de passe existant
                  if (Hash::check($request->old_password, $user->password)) {
                    User::where('id', $user->id)
                        ->update(['password' => $pass_new]);
                    return response()->json(['statusCode'=>200,
                                             'status' => true,
                                             'message' => "Mise à jour effectuée avec succès, veuillez vous connectez avec votre nouveau mot de passe",
                                            ], 200);
                  }else{
                    return response()->json(['statusCode'=>406,
                                             'status' => false,
                                             'message' => "ancien mot de passe incorrecte",
                                           ], 406);
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
            //  $password = Hash::make($request->password);
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

            //  if ($request->password!='') {
            //     User::where('id', $user->id)
            //          ->update(['password' => $password]);
            //  }

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
            $user = Auth::user();
            $client = getSingleClientsUser($user->id);
            return getAllUSerCredit($client->idclient);
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

            /**
             * ----------------------
             *  LANCEMENT DE CINETPAY
             * ----------------------
             */
              # Etape 1: Préparation du Guichet de paiement
              $user = Auth::user();
              $client = getSingleClientsUser($user->id);
              $transaction_id = date("YmdHis");
              $description_trans = $request->code_credit;
              $client_name = $user->name;
              $client_surname =  $user->name;
              $client_phone = $user->tel;
              $client_email = $user->email?:support();
              $datePay = date("d-m-Y H:i:s");
              $notify_url = "notify_pay";
              $return_url = "return_pay";
              # Etape 2: Enregistrer la transaction et le paiement
              $type_paiement = "credit";
              $credit = CheckCredit($request->code_credit,$client->idclient);
              if ( $credit ) {
                 savePay($transaction_id,$type_paiement,$credit->montant,$description_trans,$client_name,$client_surname,$client_phone,$client_email,$client->iduser);
                 # Etape 3: Lancer le Guichet de paiement
                 return  Guichet($transaction_id,$credit->montant,$description_trans,$client_name,$client_surname,$client_phone,$client_email,$notify_url,$return_url);
              }else{
                return response()->json([
                    'statusCode'=>404,
                    'status' => false,
                    'message' =>"ce code crédit n'existe pas"
                ], 404);
              }
            
            // return rembourserCreditUser($request->code_credit);
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
                'montant' => 'required',
                'qte' => 'required|min:1',
                'zoneid' => 'required',
                'dateComd' => 'required',
                'statutClient' => 'required',
                'plats' => 'required'
            ]);

            if ($validateComd->fails())
            {
                return response()->json([
                    'statusCode'=>404,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validateComd->errors()
                ], 404);
            }
            $credit_didou     = $request->credit_didou;
            $montant          = $request->montant;
            $qte              = $request->qte;
            $longitude        = $request->longitude;
            $largitude        = $request->largitude;
            $zoneid           = $request->zoneid;
            $dateComd         = $request->dateComd;
            $statutClient     = $request->statutClient;
            $plats            = $request->plats;
            $montantPay       = $montant;
            $zoneprecise      = $request->zoneprecise;
           
            //infos setting
            $setting = getSettingIn();
            //Infos du client
            $user = Auth::user();
            $client = getSingleClientsUser($user->id);
            if ($client->status == 'inactif') {
                return response()->json([
                    'statusCode'=>401,
                    'status' => false,
                    'message' =>"votre compte est inactif"
                ], 401);
            }
           
            

            //Step 1 : vérifier le credit_didou + Faire une réduction du prix de la commande avec le montant de crédit paramétré et définir le nouveau montant
            if ($credit_didou!='') {
                $dataCredit = CheckCredit($credit_didou,$client->idclient);
                if ($dataCredit) {
                    //Check crédit non utilisé
                    if ($dataCredit->credit_used=="true") {
                        return response()->json([
                            'statusCode'=>404,
                            'status' => false,
                            'message' => "Crédit Didou invalide, le code a déjà été utilisé",
                            'errors' => $validateComd->errors()
                        ], 404);
                    }else{
                    
                        //Montant crédit
                        $montant_credit = $dataCredit->montant;
                        //Vérification du montant
                        if($montant_credit > $montant)
                        {
                            return response()->json([
                                'statusCode'=>404,
                                'status' => false,
                                'message' => "Le montant minimum de la commande doit être ".formatPrice($montant_credit)." Fcfa pour utiliser le crédit Didou",
                                'errors' => $validateComd->errors()
                            ], 404);
                        }
                        elseif ($montant_credit <= $montant)
                        {
                        
                            $montantPay = $montant - $montant_credit;                    
                        }
                    }
                   
                }else{
                    return response()->json([
                        'statusCode'=>404,
                        'status' => false,
                        'message' => "Ce code crédit n'existe pas",
                        'errors' => $validateComd->errors()
                    ], 404);
                }
            }

            //Step 2 : Vérification réduction en cas de compte client affilié et qu'un code crédit est actif
            if ($client->ambassadeur!='aucun' && $montantPay>0) {
                $montantPay = $montantPay - ($montantPay*$setting->commandeAffilier/100);
            }
            //Step 2: cas où le compte client est  affilié et qu'il n'y a aucun code crédit actif
            if ($client->ambassadeur!='aucun' && $credit_didou=='') {
               $montantPay = $montant - ($montant*$setting->commandeAffilier/100);
            }
            //Step 2: cas où le compte client n'est pas affilié et qu'il n'a pas de code crédit actif
            if ($client->ambassadeur=='aucun' && $credit_didou=='') {
                $montantPay = $montant;
            }

          

            if ($montantPay==0 && $credit_didou!=''  && $dataCredit->credit_used=="false") {
               
               
                    # Change credit_didou_status used on : true => utilisé
                    credit_used_status($credit_didou,"true");
                    # Enregistrer la commande
                    $numComd = NumComd();
                    saveCommand($client->idclient,$client->ambassadeur,$credit_didou,$montant,$montantPay,$qte,$zoneid,$dateComd,$statutClient,$numComd,$longitude,$largitude,$zoneprecise);
                    # Enregistrer chaque produit de la commande
                  
                    foreach ($plats as  $value) 
                    {   
                        //enregistrer le plat
                        $plat =  getplatbyID($value['id_plat']);
                        savecomprod($value['id_plat'],$value['qte'],$plat->prix*$value['qte'],$numComd,$client->idclient,$value['precision']);
                        //enregistre le supplement
                        foreach ($value['supplement'] as $supplement) {
                            saveCommandSupplement($numComd,$value['id_plat'],$supplement);
                        }
                    }
                    // # Créditer solde ambassadeur
                    if ($client->ambassadeur) 
                    {
                        creditersoldAmbasad($client->ambassadeur);
                        #Push à l'ambassadeur
                        sendPush($client->tokenFCM,'Ambassadeur','cher ambassadeur votre solde a été crédité','','AMBASSADOR_ACTION');
                    }
                    //push de conmmande validée au client
                    sendPush($client->tokenFCM,'Commande','votre commande a été validée, la livraison est en cours','','DELIVERING');
                    //Retour
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
                 $numComd = NumComd();
                 $dateComd = date("d-m-Y H:i:s");
                 #Save transaction :: save commande
                 saveTransaction($client->idclient,$numComd,$longitude,$largitude,$montantPay,$client->ambassadeur,$credit_didou,$montant,$qte,$zoneid,$dateComd,$statutClient,$transaction_id,$zoneprecise);
                 #Save commande details ::  Enregistrer chaque produit de la commande
                  foreach ($plats as  $value) 
                  {   
                      //enregistrer le plat
                      $plat =  getplatbyID($value['id_plat']);
                      savecomprod($value['id_plat'],$value['qte'],$plat->prix*$value['qte'],$numComd,$client->idclient,$value['precision']);
                      //enregistre le supplement
                      foreach ($value['supplement'] as $supplement) {
                          saveCommandSupplement($numComd,$value['id_plat'],$supplement);
                      }
                  }
                  #Save payment
                  savePay($transaction_id,$type_paiement,$montantPay,$description_trans,$client_name,$client_surname,$client_phone,$client_email,$client->iduser);
                 #Etape 3 : Lancer le Guichet de paiement
                 return  Guichet($transaction_id,$montantPay,$description_trans,$client_name,$client_surname,$client_phone,$client_email,$notify_url,$return_url);

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
                        'statusCode'=>404,
                        'status' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $validate_Comd->errors()
                    ], 404);
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
                        'statusCode'=>404,
                        'status'    => false,
                        'message'   => 'Erreur de validation',
                        'errors'    => $validate_avis->errors()
                    ], 404);
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
            $user = Auth::user();
            $client = getSingleClientsUser($user->id);
            return getClientComdstatus($client->idclient,$request->commande_status);
         } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['statusCode'=>500,
                                     'status' => false,
                                     'message' => $th->getMessage()
                                   ], 500);
         }
      }
      //Get client's all command
      function getClientComdAll(Request $request)
      {
        try {
             //Validated
             $user = Auth::user();
             $client = getSingleClientsUser($user->id);
             return getClientComdAll($client->idclient);

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
                    'statusCode'=>404,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Comd->errors()
                ], 404);
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
                'statusCode'=>404,
                'status' => false,
                'message' => 'Erreur de validation',
                'errors' => $validatedAmb->errors()
            ], 404);
        }
       
        return debiterSoldAmbasad($request->code_ambassadeur,$request->montant,$request->method);
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
                    'statusCode'=>404,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Push->errors()
                ], 404);
            }
            $titre   = $request->titre;
            $message = $request->message;
            $state   = $request->state;
            $id_user = $request->id_user;
          
            return addUserPush($titre,$message,$state,$request->status,$id_user);
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
            return getUserPush(Auth::id());
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
                    'statusCode'=>404,
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validate_Push->errors()
                ], 404);
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
       //notifypay :: PAY IN
       function notify_pay(Request $request)
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
          
            if ($pay) {
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
                    #Changer le statut de paiement :: en 1 pour paiement succès
                    $payment = getTransPay($id_transaction);
                    $user    = $payment->user_id;
                    $client = getSingleClientsUser($user);
                    updatePayState($id_transaction);
                    # Enregistrement de la commande
                    $commande = getTransComd($id_transaction);
                    
                    if ($payment->type_paiement=='commande') {
                        
                        saveCommand($commande->clientid,$commande->ambassadeur_code,$commande->credit_didou,$commande->montant,$commande->montantpay,$commande->qte,$commande->zoneid,$commande->dateComd,$commande->statutClient,$commande->numcomd,$commande->longitude,$commande->largitude,$commande->zoneprecise);
                        # Push au client
                        // sendPush($client->tokenFCM,'Commande','Paiement effectué votre commande a été validée vous serez livrer dans quelques mineutes','','DELIVERING');
                        # Email à l'administrateur
                        $etting = getSettingIn();
                        $msgG = "Infos Commande :
								- Nom:".$payment->client_name."
								- Phone :".$payment->client_phone."
								- N°de commande :".$commande->numcomd." 
								- Montant :".$commande->montant."
								- Date Commande :".date('d-m-Y');
                        try {
                         SendEmail($etting->email,'Nouvelle commande Didou',$msgG);
                        } catch (\Throwable $th) {
                          echo $th->getMessage();
                        }  
                        # Créditer le solde du compte ambassadeur
                        return $commande->ambassadeur_code;
                        if ($commande->ambassadeur_code!='aucun') 
                        {
                            creditersoldAmbasad($commande->ambassadeur_code);
                            #Push à l'ambassadeur
                            $ambassadeur = getAmb($commande->ambassadeur_code);
                            sendPush($ambassadeur->tokenFCM,'Ambassadeur','cher ambassadeur votre solde a été crédité','','AMBASSADOR_ACTION');
                        }
                    }
                    if ($payment->type_paiement=='credit') {
                        $payment = getTransPay($id_transaction);
                        $credit = $payment->description;
                        # Mise à jour du status remboursement en succès
                        rembourserCreditUser($credit);
                        #Push au client
                        sendPush($client->tokenFCM,'Crédit Didou','remboursement du crédit effectué avec succès','','CREDIT_DIDOU');
                    }
                    
                }
              }else{
                 #Changer le statut de paiement :: en 1 pour paiement succès
                 $payment = getTransPay($id_transaction);
                 $user    = $payment->user_id;
                 $client = getSingleClientsUser($user);
                 updatePayState($id_transaction);
                 # Enregistrement de la commande
                 $commande = getTransComd($id_transaction);
                 
                 if ($payment->type_paiement=='commande') {
                    //  return $commande;
                     saveCommand($commande->clientid,$commande->ambassadeur_code,$commande->credit_didou,$commande->montant,$commande->montantpay,$commande->qte,$commande->zoneid,$commande->dateComd,$commande->statutClient,$commande->numcomd,$commande->longitude,$commande->largitude,$commande->zoneprecise);
                     # Push au client
                     sendPush($client->tokenFCM,'Commande','Paiement effectué votre commande a été validée vous serez livrer dans quelques mineutes','','DELIVERING');
                     # Email à l'administrateur
                     $etting = getSettingIn();
                     $msgG = "Infos Commande :
                             - Nom:".$payment->client_name."
                             - Phone :".$payment->client_phone."
                             - N°de commande :".$commande->numcomd." 
                             - Montant :".$commande->montant."
                             - Date Commande :".date('d-m-Y');
                     try {
                      SendEmail($etting->email,'Nouvelle commande Didou',$msgG);
                     } catch (\Throwable $th) {
                       echo $th->getMessage();
                     }  
                     # Créditer le solde du compte ambassadeur
                     if ($commande->ambassadeur_code!='aucun') 
                     {
                         creditersoldAmbasad($commande->ambassadeur_code);
                         #Push à l'ambassadeur
                         $ambassadeur = getAmb($commande->ambassadeur_code);
                         sendPush($ambassadeur->tokenFCM,'Didou ambassadeur','cher ambassadeur votre solde a été crédité','','AMBASSADOR_ACTION');
                     }
                 }
                 if ($payment->type_paiement=='credit') {
                     $payment = getTransPay($id_transaction);
                     $credit = $payment->description;
                     # Mise à jour du status remboursement en succès
                     rembourserCreditUser($credit);
                     #Push au client
                     sendPush($client->tokenFCM,'Crédit Didou','remboursement du crédit effectué avec succès','','CREDIT_DIDOU');
                 }
              }
            }
       }
       //returnpay :: PAY IN
       function return_pay(Request $request)
       {
          $id_transaction = $request->transaction_id;
        
          #Verification du paiement
          $payment = getTransPay($id_transaction);
         
          if ($payment->type_paiement=='credit') {
            $msg = "paiement effectué avec succès, remboursement de crédit validé";
          }
          if ($payment->type_paiement=='commande') {
            $msg = "paiement effectué avec succès, commande validée";
          }
          if ($payment->state==1) {
            return response()->json(['statusCode'=>'200',
                                     'status'=>true,
                                     'message'=> $msg,
                                     'data'=> '',
                                     'error'=> '',
                                    ],200);
          }else{
           
              if ($payment->type_paiement=='credit') {
                $msg = "paiement echoué, crédit non remboursé";
              }
              if ($payment->type_paiement=='commande') {
                $msg = "paiement echoué, commande non validée";
                $commande = getTransComd($id_transaction);
                if($commande) {
                    //Suppression des dépendances de la commande
                      #suppression des plats de la commande :: panier
                      deletepanier($commande->numcomd);
                      #suppression des supplement :: panier_supplements
                      deletesup($commande->numcomd);
                      #suppression de la commande
                      deletecomd($commande->numcomd);
                }
               
              }
              return response()->json(['statusCode'=>'404',
                                       'status'=>false,
                                       'message'=>$msg,
                                       'data'=> '',
                                       'error'=> '',
                                    ],404);
          }

       }
    
    
}
