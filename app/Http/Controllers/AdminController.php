<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    /*------------------------------------------------------
        API DIDOU
        API DE GESTION DE L'APPS WEB ET L'APPS MOBILE
        CETTE API COMPORTE TOUTES LES ACTIONS A EFFECTUER
        ENTRE LA BASE DE DONNEES,LE CLIENT ET L'ADMINISTRATEUR
     ---------------------------------------------------------
    */
        /*-------------------
          STATISTIQUE
        ---------------------*/
            //Total des commandes livrées
            function OrderTotal(Request $request)
            {
                return OrderTotal();
            }
            //Total des clients
            function ClientsTotal(Request $request)
            {
                return ClientsTotal();
            }
            //Total du solde
            function SoldeTotal(Request $request)
            {
                return soldeTotal();
            }
            //Total des recettes
            function RecetteTotal(Request $request)
            {
                return RecetteTotal();
            }
            //Commandes recentes
            function OrderLast(Request $request)
            {
                return  OrderLast();
            }
            //Commandes totale par zone
            function OrderByzone(Request $request)
            {
                $zone = $request->zoneid;
                return OrderByzone($zone);
            }





        /*-------------------
          AVIS CLIENTS
        ---------------------*/
      
        //Get avis by Mentions
        function getAvisByMentions(Request $request)
        {
            return getAvisByMentions($request->mentions);
        }
        //Get Commentaires by mentions
        function getCommentByMentions(Request $request)
        {
            return getCommentByMentions($request->mentions);
        }
        //Get commentaires mentions by date
        function getCommentByDateMentions(Request $request)
        {
            return getCommentByDateMentions($request->mentions,$request->dateComment);
        }

        /*-------------------
          GESTION SUPPLEMENT
        ---------------------*/
          //Create supplements
          function createSupplement(Request $request)
          {
                //Get inputs
                $nom = $request->nom; 
                $file = $request->file('image');
                $lien  = env('LIEN_FILE');
                if ($nom!='' &&  $file!='') {
                    //Traitement des images
                    $path = $file->store('supplements','public');
                    $supplementimage = $lien.$path;
                    //save
                    return CreateSupplement($nom,$supplementimage,0);
                }else{
                    return response()->json(['statusCode'=>'400',
                                            'status'=>'false',
                                            'message'=>"Veuillez sélectionner une image et insérer un nom",
                                            'data'=> '',
                                            'error'=> '',
                                            ]);
                }
          }
          //Modifier suppléments
          function updateSupplement(Request $request)
          {
             //Get inputs
             $nom = $request->nom; 
             $file = $request->file('image');
             $status = $request->status; 
             $supplementID = $request->supID; 
             $supplementimage = '';
             $lien  = env('LIEN_FILE');
             if ($file!='') 
             {
               //Traitement des imagesss
               $path = $file->store('supplements','public');
               $supplementimage = $lien.$path;
               //save
               return UpdateSupplement($nom,$supplementimage,$status,$supplementID);
             }
             if($file=='')
             {
                return UpdateSupplement($nom,$supplementimage,$status,$supplementID);
             }
          }
          //Historique supplement
          function getSupplement()
          {
            return getSupplement();
          }
          //Rechercher un supplement
          function searchSupplement(Request $request)
          {
             $supl = $request->supplement;
             return searchSupplement($supl);
          }
          //Recuperer un supplement by id
          function getsingleSupplement(Request $request)
          {
            $supl = $request->supplement;
            return getsingleSupplement($supl);
             
          }

    
        
        /*------------------
          GESTION CATEGORIES
        ---------------------*/
        
            //Create categories
            function createCatg(Request $request)
            {   
              
                //Validate
                $validateCatg = Validator::make($request->all(),[
                    'nomcatg' => 'required|min:4|unique:categorie,nomcateg',
                    'photo' => 'required|mimes:jpeg,jpg,png|max:100000',
                ]);
                if ($validateCatg->fails())
                {
                    return response()->json(['statusCode'=>'401',
                                                'status'=>'false',
                                                'message'=>'Erreur de validation',
                                                'data'=> '',
                                                'error'=> $validateCatg->errors(),
                                                ]);
                }
                //Traitement
                $catg = $request->nomcatg;
                $file = $request->file('photo');
                $lien  = env('LIEN_FILE');
                $path = $file->store('categories','public');
                $categimage = $lien.$path;
                $categorie = ucwords($catg);
                return saveCatg($categorie,$categimage);
            }

            //Get categories All
            function getAllCatg()
            {
                return getAllCatg();   
            }

            //Search categorie
            function searchCatg(Request $request)
            {
               $catg = $request->nomcatg;
               return searchCatg($catg);
            }

            //Get plat by categorie
            function getOrderCatg(Request $request)
            {
                $catg = $request->catgid;
                return getOrderCatg($catg);
            }

            //Update categories
            function updateCatg(Request $request)
            {
               
                //Data
                $catgImg = '';
                $nomcatg = $request->nomcatg;
                $idcatg = $request->idcatg;
                $file = $request->file('photo');
                $lien  = env('LIEN_FILE');
                if ($file!='') 
                {
                    $path = $file->store('categories','public');
                    $catgImg = $lien.$path;
                }

                //Update
                return updateCatg($nomcatg,$catgImg,$idcatg);
            }

            //Delete categories
            function deleteCatg(Request $request)
            {
               $idcatg = $request->idcatg;
               return deleteCatg($idcatg);
            }
        



        /*----------------
         GESTION RECETTES
        ----------------*/
           
            //Create recette
            function createRecette(Request $request)
            {
             
              //Contrôle des champs
              $validated = Validator::make($request->all(),[
                'nomrecette' => 'required|min:4|unique:plats',
                'image' => 'required|mimes:jpeg,jpg,png|max:100000',
                'galerie' => 'required|mimes:jpeg,jpg,png|max:100000',
                'description' => 'required|min:4',
                'categorie' => 'required',
                'prix' => 'required',
              ]);
              if ($validated->fails()) 
              {
                return response()->json(['statusCode'=>'401',
                                        'status'=>'false',
                                        'message'=>'Erreur de validation',
                                        'data'=> '',
                                        'error'=> $validated->errors(),
                                    ]);
              }

              //Get data
              $lien  = env('LIEN_FILE');
              $nomrecette = $request->nomrecette; 
              $description = $request->description;     
              $categorie = $request->categorie; 
              $recommand = $request->recommanded; 
              $prix = $request->prix; 
              $image = $request->file('image');    

              //Traitement
              $fileImage = "";
              if ($image!='') {
                 $path = $image->store('recettes','public');
                 $fileImage = $lien.$path;
              } 
              $recetteId =  createRecette($nomrecette,$description,$fileImage,$prix,$categorie,$recommand);
           
               //save galerie
               recetteGalerie($recetteId,$request->file('galerie'));
               /*foreach ($request->file('galerie') as $key => $file)
               {
                 $path = $file->store('galeries','public');
                 $fileGalerie = $lien.$path;
                 recetteGalerie($recetteId,$fileGalerie);
               }*/
               //save suppléments
               recetteSupplement($recetteId,$request->supplement);

               /*foreach ($request->supplement as $value) 
               {
                 recetteSupplement($recetteId,$value);
               }*/
               return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Nouvelle recette ajoutée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                     ]);
              
              
             
            }
            //Get all recette
            function getAllRecette()
            {
               return getAllRecette();
            }
            //Get single recette
            function getSingleRecette(Request $request)
            {              
                $recetteid = $request->recetteid;
                return getSingleRecette($recetteid);
            }
            //Update single 
            function updateRecette(Request $request)
            {
                //Get recettes data
                $lien  = env('LIEN_FILE');
                $nomrecette     = $request->nomrecette;
                $description    = $request->description;
                $categorie      = $request->categorie;
                $recommanded    = $request->recommanded;
                $disponible     = $request->disponible;
                $prix           = $request->prix;
                $recetteid      = $request->recetteid;
                //Traitement image
                $imageFile  = "";
                $image = $request->file('image');
                if ($image!='') {
                    $path = $image->store('recettes','public');
                    $imageFile = $lien.$path;
                }
                updateRecette($nomrecette,$description,$categorie,$recommanded,$disponible,$prix,$imageFile,$recetteid);

                //Get recette galerie data
                if ($request->file('galerie')!='') {
                    updateRecetteGalerie($recetteid,$request->file('galerie'));
                    /*foreach ($request->file('galerie') as $key => $file)
                    {
                         $path = $file->store('galeries','public');
                         $fileGalerie = $lien.$path;
                         updateRecetteGalerie($recetteid,$fileGalerie);
                    }*/
                }
               
                //Get recette supplement data
                if ($request->supplement!='') {
                    updateRecetteSupplement($recetteid,$request->supplement);
                   /* foreach ($request->supplement as $value) 
                    {
                        updateRecetteSupplement($recetteid,$value);
                    }*/
                }

                return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Mise à jour effectuée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                    ]);
               
              
            }
            //Delete single recette
            function deleteRecette(Request $request)
            {
                $recetteid  = $request->recetteid;
                return deleteRecette($recetteid);
            }

        /*----------------
          GESTION CLIENTS
        ----------------*/

            //Create clients
            function createClients(Request $request)
            {
                $nom = $request->nom;
                $email = $request->email;
                $tel = $request->tel;
                $parain = $request->parain;
                return createClients($nom,$email,$tel,$parain);
            }
            //Get  all clients
            function getAllClients()
            {
                return getAllClients();
            }
            //Get single clients
            function getSingleClients($clientsid)
            {
               return getSingleClients($clientsid);
            }   
            //Update single recette
            function updateClients(Request $request)
            {
                $nom = $request->nom;
                $email = $request->email;
                $tel = $request->tel;
                $id = $request->id;
                return updateClients($nom,$email,$tel,$id);
            }
            //Delete single clients
            function deleteClients(Request $request)
            {
                $clientsid = $request->clientid;
                return deleteClients($clientsid);
            }
            //Liaison client-ambassadeur
            function clientambassad(Request $request)
            {
                $idclient = $request->clientid;
                $idambassad = $request->codeambassad;
                return clientambassad($idclient,$idambassad);
            }
        /**
         * ---------------------------
         *  SETTING DiDOU & ZONE
         * ---------------------------
         */
            //setting didou
            function setting(Request $request)
            {
                $gainlivreur = $request->gainlivreur;
                $gainambassadeur = $request->gainambassadeur;
                $promoComd = $request->promoComd;
                $creditDidou = $request->creditDidou;
                return settingDidou($gainlivreur,$gainambassadeur,$promoComd,$creditDidou);
            }
            //recuperer les paramètres didou
            function getsetting()
            {
                return getsetting();
            }

        /*----------------
         GESTION LIVREURS
        ---------------- */
            //Create livreurs
            function createLivreur(Request $request)
            {
               $nom = $request->nom;
               $tel = $request->tel;
               $email = $request->email;
               $local = $request->local;
               return createLivreur($nom,$tel,$email,$local);
            }
            //Get all livreurs
            function getAllLivreur()
            {
               return getAllLivreur();
            } 
            //Get single livreurs
            function getSingleLivreur(Request $request)
            {
               $livreur = $request->livreur;
               return getSingleLivreur($livreur);
            }
            //Update livreurs
            function updatlivreur(Request $request)
            {
               $nom = $request->nom;
               $tel = $request->tel;
               $email = $request->email;
               $local = $request->local;
               $id = $request->id;
               $status = $request->status;
               $photo = "";
               //Traitemenet image
                $file = $request->file('photo');
                $lien = env('LIEN_FILE');
                if ($file!='') {
                 $path = $file->store('categories','public');
                 $photo = $lien.$path;
                }
               return updatlivreur($nom,$tel,$email,$local,$status, $photo,$id); 
            }
            //Delete livreurs
            function deleteLivreur(Request $request)
            {
                $livreur = $request->livreurid;
                return deleteLivreur($livreur);
            }
            //Livreur shipping : enregistrer une livraison
            function livreurLivraison(Request $request)
            {
               $orderid =  $request->orderid;
               $livreur =  $request->livreur;
               return livreurLivraison($orderid,$livreur);
            }
            //Commande d'un livreur
            function orderliv(Request $request)
            {
                $livreur =  $request->livreurid;
                return orderOfLivreur($livreur);
            }

            //Liste des des commandes en fonction du status_livreur
            function orderLivreurStat(Request $request)
            {
                $status = $request->statut;
                $livreurid = $request->livreurid;
                return orderLivreurStat($livreurid,$status);
            }

                


        /* ---------------------
          GESTION DES COMMANDES
        ------------------------*/
            //Create commande
            function creatorder(Request $request)
            {
                //Recuperer la data
                $numComd = NumComd();
                $clientid = $request->clientid;
                $ambasd = $request->ambasd;
                $amountComd = $request->amountComd;
                $qteComd = $request->qteComd;
                $gps = $request->gps;
                $zoneid = $request->zoneid;
                $dateComd = date('d-m-Y');
                $statutClient = $request->statutClient;
                //Etape 1 : Enregistrer la commande
                savecomd($numComd,$clientid,$amountComd,$qteComd,$gps,$zoneid,$dateComd,$statutClient,$ambasd);

                //Etape 2 : Enregistrer chaque produit
                $data = $request->data;
                foreach ($data as $key => $value) 
                {
                    savecomprod($value['platId'],$value['qte'],$value['amount'],$numComd,$clientid);
                }
                //Retour
                return response()->json(['statusCode'=>'200',
                                         'status'=>'true',
                                         'message'=>"Commande validée avec succès",
                                         'data'=> '',
                                         'error'=> '',
                                        ]);
                

            }
            //Get all commande
            function getAllorder(Request $request)
            {
                return getAllorder();
            }
            //Get single commande
            function getsinglorder(Request $request)
            {
                $ordersid = $request->ordersid;
                return getsinglorder($ordersid);
            }
            //Update commande
            function updatorder(Request $request)
            {
                //Recuperer la data
                $numComd = $request->NumComd;
                $idcommandes = $request->idcommandes;
                $clientid = $request->clientid;
                $ambasd = $request->ambasd;
                $amountComd = $request->amountComd;
                $qteComd = $request->qteComd;
                $gps = $request->gps;
                $zoneid = $request->zoneid;
                $dateComd = $request->dateComd;
                $statutClient = $request->statutClient;

                return updatorder($numComd,$idcommandes,$clientid,$ambasd,$amountComd,$qteComd,$gps,$zoneid,$dateComd,$statutClient);
            }
            //Delete commande
            function deletOrder(Request $request)
            {
                $idcomd = $request->order;
                return deletOrder($idcomd);
            }
            //Détails d'une commande :: recupérer les plats
            function getOrderPlats(Request $request)
            {
                $numcomd = $request->numcomd;
                return getOrderPlats($numcomd);
            }
            //Mise à jour du statut de la commande :: statut_livreur
            function UpdOrderstatusLivreur(Request $request)
            { 
                $orderid = $request->orderid;
                $statutlivreur = $request->statutlivreur;
                return UpdOrderstatusLivreur($orderid,$statutlivreur);
            }

            //Mise à jour du statut de la commande :: statut_client
            function UpdOrderstatusClient(Request $request)
            { 
                $orderid = $request->orderid;
                $statutClient = $request->statutClient;
                return UpdOrderstatusClient($orderid,$statutClient);

            }


        
        /* -------------------------
          GESTION DES CREDITS DIDOU
        --------------------------- */
            //Create crédit
            function creatcredit(Request $request)
            {
                try {
                    $user = Auth::user();
                    $client = getSingleClientsUser($user->id);
                    return creatcredit($client->idclient);
                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statusCode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
              
               
            }
            //Get All crédit
            function getAllcredit()
            {
                return getAllcredit();
            }
           
        

        /* ----------------------
         GESTION DES AMBASSADEURS
        ------------------------ */
            //Create ambassadeurs
            function creatambassad(Request $request)
            {
                $clientid = $request->clientid;
                $client = getSingleClientsUser(Auth::id());
                return creatambassad($client->idclient);
            }
            //Modifier le statut
            function updambassad(Request $request)
            {
                $ambid = $request->ambid;
                $status = $request->status;
                return updambassad($ambid,$status);
            }
            //Get all ambassadeurs
            function getAllambassad()
            {
               return getAllambassad();
            }
            //Get single ambassadeurs
            function getsinglambassad(Request $request)
            {
               $ambassad = $request->ambasdid;
               return getSinglambassad($ambassad);
            }
            //Créditer solde ambassadeur
            function creditersoldAmbasad(Request $request)
            {
                try {
                 //code...
                 $amb = $request->ambcode;
                 return creditersoldAmbasad($amb);
                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statusCode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
                
            }
            //Get all commandes by ambassadeurs
            function getCommdAmbassad(Request $request)
            {
               $ambasd = $request->ambasdid;            
               return getCommdAmbassad($ambasd);
            }

        
        /* ------------------------
            GESTION DES CAMPAGNES PUSH
        -------------------------- */
            //Create push
            function creatpush(Request $request)
            {
                $pushMsg = $request->message;
                $pushTitre = $request->titre;
                $debut = $request->debut;
                $fin = $request->fin;
                $pushImg = '';
                $file = $request->file('img');
                $lien  = env('LIEN_FILE');
                if ($file!='') 
                {
                    //Traitement d'image
                    $path = $file->store('push','public');
                    $pushImg = $lien.$path;
                }
                return creatpush($pushMsg,$pushImg,$pushTitre,$debut,$fin);
            }
            //Get all push
            function getallpush()
            {
                return getallpush();
            }
            //Search push
           function searchPush(Request $request)
           {
              return searchPush($request->push);
           }



        
        /* --------------------------
            GESTION DES COMPTES USERS
        ----------------------------- */
            /**
             * Create User
             * @param Request $request
             * @return User 
             */
            function creatuser(Request $request)
            {
                try {
                    //Validated
                    $validateUser = Validator::make($request->all(),[
                        'name' => 'required|min:4',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|min:8',

                    ]);

                    if ($validateUser->fails()) 
                    {
                        return response()->json(['statusCode'=>'401',
                                                'status'=>'false',
                                                'message'=>'Erreur de validation',
                                                'data'=> '',
                                                'error'=> $validateUser->errors(),
                                                ]);
                    }

                    $user = User::create([
                                            'name' => $request->name,
                                            'email' => $request->email,
                                            'password' => Hash::make($request->password)
                                        ]);

                    return response()->json([
                                             'statusCode'=>200,
                                             'status' => true,
                                             'message' => 'Utilisateur crée avec succès',
                                             'token' => $user->createToken("API TOKEN")->plainTextToken,
                                             
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
            //Get all user
            function getalluser()
            {
                return getalluser();
            }
            //Get utilisateur connecté
            function getuserauth(Request $request)
            {
                $user = Auth::user();
                return response()->json(['statusCode'=>'200',
                                         'status'=>'true',
                                         'message'=>"profil de l'utilisateur connecté",
                                         'data'=> $user,
                                         'error'=> '',
                                        ]);
            }
            //Update user
            function updatuser(Request $request)
            {
                $nom =  $request->nom;
                $email =  $request->email;
                $pass =  Hash::make($request->pass);
                $userid =  $request->userid;
                return updatuser($nom,$email,$pass,$userid);
            }
            //Delete single user
            function deletuser(Request $request)
            {
                $userid =  $request->userid;
                return deleteuser($userid);
            }
            //Login user
            function loginuser(Request $request)
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

                    if (!Auth::attempt($request->only(['email','password']))) 
                    {
                        return response()->json([
                            'statusCode'=>401,
                            'status' => false,
                            'message' => "L'email et le mot de passe ne correspondent pas à nos enregistrement",
                        ], 401);
                    }

                    $user = User::where('email', $request->email)->first();
                    return response()->json([
                        'statusCode'=>200,
                        'status' => true,
                        'message' => "L'utilisateur s'est connecté avec succès",
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


            function logout(Request $request)
            {
                try {
                    $user = $request->user()->currentAccessToken()->delete();
                    return response()->json([
                        'statusCode'=>200,
                        'status' => false,
                        'message' => "L'utilisateur est deconnecté avec succès",
                    ], 401);
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
         * -------------------
         *  GESTION DE ZONE
         * -------------------
         */
          //enregistrer une zone
          function creatzone(Request $request)
          {
            $zone =  $request->zone;
            return creatzone($zone);
          }
          //liste des zones
          function getallzone()
          {
             return getallzone();
          }
          //modifier une zone
          function updatezone(Request $request)
          {
            $zone =  $request->zone;
            $zoneid =  $request->zoneid;
            return updatezone($zone,$zoneid);
          }
          //changer le status d'une zone
          function updstatuszone(Request $request)
          {
            $zoneid =  $request->zoneid;
            $status =  $request->status;
            return updstatuszone($status,$zoneid);
          }
          //commande par zone
          function getOrderzone(Request $request)
          {
            $zoneid =  $request->zoneid;
            return getOrderzone($zoneid);
          }
        
        /* --------------------
         GESTION DASHBOARD
        -------------------- */
            //Gains total stats
            function statsgains()
            {
                $res = "Gains total";
                return $res;
            }
            //Commandes stats
            function statsorder()
            {
                $res = "Commandes total";
                return $res;
            }
            //Clients stats
            function statsclients()
            {
                $res = "clients Total";
                return $res;
            }
            //Solde stats
            function statsolde()
            {
                $res = "Solde CinetPay";
                return $res;
            }
            //Revenu stats
            function statsrevenu()
            {
                $res = "Revenu des commandes";
                return $res;
            }
            //Commande par zone stats
            function statsorderzone()
            {
                $res = "Nombre de commandes de chaque zone";
                return $res;
            }
            //Recettes plus vendus
            function statsrecettes()
            {
                $res = "recette les plus vendus";
                return $res;
            }
            //Meilleurs ambassadeurs
            function statsambassd()
            {
                $res = "Ambassadeur avec le plus de commandes";
                return $res;
            }
            //Commandes recentes
            function statordercurrent()
            {
                $res = "Commandes récentes";
                return $res;
            }

       

        


}
