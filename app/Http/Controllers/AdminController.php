<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Kutia\Larafirebase\Facades\Larafirebase;


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
                $prix = $request->prix; 
                $file = $request->file('image');
                $lien  = env('LIEN_FILE');
                if ($nom!='') {
                    //Traitement des images
                    $supplementimage ='';
                    if($file){
                        $path = $file->store('supplements','public');
                        $supplementimage = $lien.$path;
                    }
                   
                    //save
                    return CreateSupplement($nom,$prix,$supplementimage);
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
          //Supprimer un supplement
          function deleteSupplement(Request $request)
          {
             try {
                //code...
                $supplementID = $request->id;
                $res = DeleteSupplementID($supplementID);
                if ($res!=0) {
                    return response()->json([
                        'statuscode' =>200,
                        'status'     => true,
                        'message'    => 'supplement supprimé avec succès'
                     ],200);
                }else{
                    return response()->json([
                        'statuscode' => 404,
                        'status'     => false,
                        'message'    => "Ce supplement n'existe pas"
                     ],404);
                }
               
             } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'statuscode'=>500,
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
             }
          }
          //Changer le state d'un supplement
          function changeSupplementState(Request $request)
          {
            try {
                //code...
                $supplementID = $request->id;
                $state        = $request->state;
                $res = ChangeSupplementState($supplementID,$state);
                if ($res!=0) {
                    return response()->json([
                        'statuscode' =>200,
                        'status'     => true,
                        'message'    => 'Mise à jour du statut effectué avec succès'
                     ],200);
                }else{
                    return response()->json([
                        'statuscode' => 404,
                        'status'     => false,
                        'message'    => "Le supplement a déjà cette valeur"
                     ],404);
                }
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'statuscode'=>500,
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
            }
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
                    'photo' => 'required|image|mimes:jpeg,jpg,png|max:100000',
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
              try {
                return getAllCatg();   
              } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'statuscode'=>500,
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
              }
              
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
            //Changer le status d'une categorie
            function ChangeStat(Request $request)
            {
                try {
                    //code...
                    $status = $request->active;
                    $idcatg = $request->idcatg;
                    $res = changeState($idcatg,$status);
                    if ($res!=0) {
                        return response()->json([
                            'statuscode' =>200,
                            'status'     => true,
                            'message'    => 'Mise à jour du status de la categorie effectué avec succès'
                         ],200);
                    }else{
                        return response()->json([
                            'statuscode' =>404,
                            'status'     => false,
                            'message'    => "Déjà mise à jour"
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
        



        /*----------------
         GESTION RECETTES
        ----------------*/
            //Update galerie
            function updategalerie(Request $request)
            {
                $photo_g = $request->file('galerie');
                $path = $photo_g->store('galerie','public');
                $lien  = env('LIEN_FILE');
                $photo_f = $lien.$path;
                updateGalerie($request->id_galerie,$photo_f);
                return response()->json(['statusCode'=>200,
                                        'status'=>true,
                                        'message'=>"Mise à jour effectu avec succès",
                                     ]);
            }
            //Suppression de catégorie
            function deletegalerie(Request $request)
            {
               $galerie = $request->id_galerie;
              
               try {
                 deletGalerie($galerie);
                 return response()->json(['statusCode'=>200,
                                          'status' => true,
                                          'message' => "Suppression de galerie effectué avec succès"
                                        ], 500);

               } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'statusCode'=>500,
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
               }
              
            }
            //get recette by recommanded state
            function getRecetteRecomd(Request $request)
            {
               try {
                //code...
                return getRecetteRecomd($request->state);
               } catch (\Throwable $th) {
                //throw $th;
               }   
            }
            //get recette by pub state
            function getRecettePub(Request $request)
            {
                try {
                    //code...
                    return  getRecetteStat($request->state);
                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statuscode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
            }
            //Create recette
            function createRecette(Request $request)
            {
              try {
             
                //Contrôle des champs
                $validated = Validator::make($request->all(),[
                    'nomrecette' => 'required|min:4',
                    'photo' => 'mimes:jpeg,jpg,png|max:100000',
                    // 'galerie' => 'required|mimes:jpeg,jpg,png|max:100000 |unique:plats',
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
                $stock = $request->stock; 
                $photo = ''; 
                //Photo principale
                if($request->file('photo'))
                {
                    $photo_1 = $request->file('photo');
                    $path = $photo_1->store('recettes','public');
                    $photo = $lien.$path;
                }
                $res = createRecette($nomrecette,$description,$photo,$prix,$categorie,$recommand,$stock);
               
                //Galerie de recettes
                if ($request->file('galerie')) 
                {

                    // $photo_g = $request->file('galerie');
                    // $path = $photo_g->store('galerie','public');
                    // $photo_f = $lien.$path;
                    // recetteGalerie($res,$photo_f);

                    foreach ($request->file('galerie') as $key => $file) {
                        $photo_g = $file;
                        $path = $photo_g->store('galerie','public');
                        $photo_f = $lien.$path;
                        recetteGalerie($res,$photo_f);
                    }
                }

                //Supplement de recettes
                if ($request->supplement) {
                    $jsonString = $request->supplement;
                    $array = json_decode($jsonString);
                    
                    foreach ($array as $value) {
                        // Faites ce que vous voulez avec chaque élément ($value)
                        // Par exemple, vous pouvez l'afficher ou effectuer un traitement spécifique.
                        recetteSupplement($res,$value);
                    }
                   
                } 
             

               return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Nouvelle recette ajoutée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                     ]);
              
              //code...
              } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statusCode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
              }
             
            }
            //Get all recette
            function getAllRecette()
            {
               return getAllRecette();
            }
            //Get recette recommandées
            function getRecommandRecette(Request $request)
            {
                
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
                $stock          = $request->stock;
                //Traitement image
                $imageFile  = "";
                if ($request->file('image')) {
                    $image = $request->file('image');
                    $path = $image->store('recettes','public');
                    $imageFile = $lien.$path;
                }
                $res = updateRecette($nomrecette,$description,$categorie,$recommanded,$disponible,$stock,$prix,$imageFile,$recetteid);
                
                //Get recette galerie data
                $galerie = $request->file('galerie');
               
                // return $nb;
                if ($request->file('galerie')) {
                    foreach ($request->file('galerie') as $key => $file)
                    {
                         $path = $file->store('galeries','public');
                         $fileGalerie = $lien.$path;
                        //  dd($fileGalerie);
                         updateRecetteGalerie($recetteid,$fileGalerie);
                    }
                }
               
                //Get recette supplement data
                if ($request->supplement) {
                    deleteRecetteID($recetteid);
                    $jsonString = $request->supplement;
                    $array = json_decode($jsonString);
                    // return $array;
                    foreach ($array as $value) {
                        // Faites ce que vous voulez avec chaque élément ($value)
                        // Par exemple, vous pouvez l'afficher ou effectuer un traitement spécifique.
                        recetteSupplement($recetteid,$value);
                    }
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

            //Changer le statut de la recette
            function changeStateRecette(Request $request)
            {
                try {
                    $recetteId = $request->recetteid;
                    $state = $request->state_value;
                    changeStateRecette($recetteId,$state);
                    return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Effectuée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                      ]);

                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statuscode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
            }
            //update 

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
                $conditionCredit = $request->conditionCredit;
                $commandeAffilier = $request->commandeAffilier;
                return settingDidou($gainlivreur,$gainambassadeur,$promoComd,$creditDidou,$conditionCredit,$commandeAffilier);
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
               $password = rand (5, 15)."LD";
               return createLivreur($nom,$tel,$email,$local,$password);
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
                
                //Validate data
               if($request->tel)
               {
                    $validateTelLivreur = Validator::make($request->all(),[
                        'tel' => 'unique:users'
                    ]);
                    if($validateTelLivreur->fails()) 
                    {
                            return response()->json(['statusCode'=>'402',
                                                    'status'=>'false',
                                                    'message'=>'Tel existe déjà',
                                                    'data'=> '',
                                                    'error'=> $validateTelLivreur->errors(),
                                                    ]);
                    }
               }
    
                if ($request->email) {
                    $validateEmailLivreur = Validator::make($request->all(),[
                        'email' => 'unique:users'
                    ]);
                    if($validateEmailLivreur->fails()) 
                    {
                            return response()->json(['statusCode'=>'402',
                                                     'status'=>'false',
                                                     'message'=>'email existe déjà',
                                                     'data'=> '',
                                                     'error'=> $validateEmailLivreur->errors(),
                                                    ]);
                    }
                }
               $nom = $request->nom;
               $tel = $request->tel;
               $email = $request->email;
               $local = $request->local;
               $id = $request->id;
               $status = $request->status;
             
               return updatlivreur($nom,$tel,$email,$local,$status,$id); 
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
                // foreach ($data as $key => $value) 
                // {
                //     savecomprod($value['platId'],$value['qte'],$value['amount'],$numComd,$clientid);
                // }
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

            //Give commande to livreur
            function giveOrderToLivreur(Request $request)
            {
                
                try {
                        //validated
                        $valideClient = Validator::make($request->all(),[
                            'idcommandes' => 'required',
                            'idlivreur'   => 'required',
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
                    giveOrderToLivreur($request->idcommandes,$request->idlivreur);
                    return response()->json([
                        'statusCode'=>200,
                        'status' => true,
                        'message' => "commande affecté avec succès"
                       ], 200);
                } catch (\Throwable $th) {
                    return response()->json([
                        'statusCode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
            }

            //Get order by state
            function getOrderState(Request $request)
            {
                try {
                    //code...
                    $state = $request->state;
                    return  getallorderState($state);
                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statuscode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
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
               $client = $request->clientID;
               return getSinglambassad($client);
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
            //Générer OTP
            function generateAdminOTP(Request $request)
            {

            }
            //Create push
            function creatpush(Request $request)
            {
             try{
                  
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
                        // $imgPush = env('APP_URL').$pushImg;
                        $imgPush = $pushImg;
                    }else{
                        $imgPush = '';
                    }
                    //Enregistrement de la campange
                    creatpush($pushMsg,$imgPush,$pushTitre,$debut,$fin);
                    //Envoie de la campagne
                    $alltokenFCM = ClientToken();
                    foreach ($alltokenFCM as $tokenFCM) {
                       
                        //return $tokenFCM->tokenFCM;
                        if ( $tokenFCM->tokenFCM) {
                            $respush =  sendPush($tokenFCM->tokenFCM,$pushTitre,$pushMsg,$pushImg,'PUSH');
                        }
                    }
                 
                    return response()->json(['message' => 'Message envoyé', 'status' => true,'image'=>$imgPush,'respush'=>$respush]);
                } catch (\Throwable $th) {
                    //throw $th;
                    return response()->json([
                        'statuscode'=>500,
                        'status' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
                   
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
           //Supprimer une campagne push
           function deletepush(Request $request)
           {
             
             try {
                $push_id = $request->id_push;
               
                deletePush($push_id);
                return response()->json(['statusCode'=>200,
                                         'status' => true,
                                         'message' => "Campagne push supprimée avec succès",
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
           //Delivery days
           function getdeliverydays(Request $request)
           {
             $days =  getdeliverydays();
             if (count($days)!=0) {
                return response()->json(['statusCode'=>200,
                                            'status'=>true,
                                            'message'=>'horaire de livraison',
                                            'data'=> $days,
                                        ],200);
             }else{
                return response()->json(['statusCode'=>404,
                                         'status'=>true,
                                         'message'=>'horaire de livraison',
                                         'data'=> [],
                                        ],404);
             }
              
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
                    $msg = 'Votre mot de passe est: '.$request->password;
                    SendEmail($request->email,'COMPTE UTILISATEUR',$msg);
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
                //Validate data
                if ($request->email!='') {
                    $validateUser = Validator::make($request->all(),[
                        'email' => 'email'
                    ]);
                    if ($validateUser->fails()) 
                    {
                        return response()->json(['statusCode'=>'401',
                                                'status'=>'false',
                                                'message'=>'email incorrecte ou cet email existe déjà',
                                                'data'=> '',
                                                'error'=> $validateUser->errors(),
                                                ]);
                    }
                }
                $nom =  $request->nom;
                $email =  $request->email;
                $userid =  $request->userid;
                $pass =  Hash::make($request->password);
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
            $long =  $request->longitude;
            $larg =  $request->largitude;
            return creatzone($zone,$long,$larg);
          }
          //liste des zones
          function getallzone()
          {
             return getallzone();
          }
          //modifier une zone
          function updatezone(Request $request)
          {
            $zone   =  $request->zone;
            $zoneid =  $request->zoneid;
            $long   =  $request->longitude;
            $larg   =  $request->largitude;

            return updatezone($zone,$zoneid,$long,$larg);
          }
          //changer le status d'une zone
          function updstatuszone(Request $request)
          {
            $zoneid =  $request->zoneid;
            $status =  $request->status;
            // dd($request);
            return updstatuszone($status,$zoneid);
          }
          //commande par zone
          function getOrderzone(Request $request)
          {
           
            return getOrderzone();
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
