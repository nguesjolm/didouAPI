<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LivreurController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*------------------
    API ADMIN
  ------------------
 */

  /* 
   --------------
    Statistiques
   --------------
  */
    //Total des commandes livrées
    Route::GET('OrderTotal',[AdminController::class, 'OrderTotal'])->middleware('auth:sanctum');
    //Total des clients
    Route::GET('ClientsTotal',[AdminController::class, 'ClientsTotal'])->middleware('auth:sanctum');
    //Totale du solde
    Route::GET('SoldeTotal',[AdminController::class, 'SoldeTotal'])->middleware('auth:sanctum');
    //Total des recettes
    Route::GET('RecetteTotal',[AdminController::class, 'RecetteTotal'])->middleware('auth:sanctum');
    //commande récentes
    Route::GET('OrderLast',[AdminController::class, 'OrderLast'])->middleware('auth:sanctum');
    //commande total par zone
    Route::GET('OrderByzone',[AdminController::class, 'OrderByzone'])->middleware('auth:sanctum');


  /*
    ------------------
    AVIS CLIENTS
    ------------------
  */
  //Create avis clients
  Route::POST('createAvis',[ClientController::class, 'createAvis']);
  //Get avis by Mentions
  Route::POST('getAvisByMentions',[AdminController::class, 'getAvisByMentions'])->middleware('auth:sanctum');
  //Get commentaire by mentions
  Route::POST('getCommentByMentions',[AdminController::class, 'getCommentByMentions'])->middleware('auth:sanctum');
  //Get commentaire mentions by date
  Route::POST('getCommentByDateMentions',[AdminController::class, 'getCommentByDateMentions'])->middleware('auth:sanctum');


  /*
    ------------------
    GESTION SUPPLEMENT
    ------------------
  */
  //Create supplements
  Route::POST('createSupplement',[AdminController::class, 'createSupplement'])->middleware('auth:sanctum');
  //Modifier supplements
  Route::PUT('updateSupplement',[AdminController::class, 'updateSupplement'])->middleware('auth:sanctum');
  //Historique supplements
  Route::GET('getSupplement',[AdminController::class, 'getSupplement'])->middleware('auth:sanctum');
  //Rechercher supplements
  Route::GET('searchSupplement',[AdminController::class, 'searchSupplement'])->middleware('auth:sanctum');
  //Recuper un supplement by ID
  Route::GET('getsingleSupplement',[AdminController::class, 'getsingleSupplement'])->middleware('auth:sanctum');




  /*
    ------------------
    GESTION CATEGORIES
    ------------------
  */
  //Create categories
  Route::POST('createCatg',[AdminController::class, 'createCatg'])->middleware('auth:sanctum');
  
  //Get categories All
  Route::GET('getAllCatg',[AdminController::class, 'getAllCatg'])->middleware('auth:sanctum');

  //Search categories
  Route::GET('searchCatg',[AdminController::class, 'searchCatg'])->middleware('auth:sanctum');

  //Get plats by categorie 
  Route::GET('getOrderCatg/{catgid}',[AdminController::class, 'getOrderCatg'])->middleware('auth:sanctum');

  //Update categories
  Route::POST('updateCatg',[AdminController::class, 'updateCatg'])->middleware('auth:sanctum');

  //Delete categories
  Route::DELETE('deleteCatg/{idcatg}',[AdminController::class, 'deleteCatg'])->middleware('auth:sanctum');
  //Changer le status d'une categorie
  Route::POST('ChangeStat',[AdminController::class, 'ChangeStat'])->middleware('auth:sanctum');

  


  /*----------------
  GESTION RECETTES
  ----------------*/
  //Create recette
  Route::POST('createRecette',[AdminController::class, 'createRecette'])->middleware('auth:sanctum');
  //Get all recette
  Route::get('getAllRecette',[AdminController::class, 'getAllRecette'])->middleware('auth:sanctum');
  //Get single recette
  Route::get('getSingleRecette/{recetteid}',[AdminController::class, 'getSingleRecette'])->middleware('auth:sanctum');
  //Get recette recommandée
  Route::get('getRecommandRecette',[AdminController::class, 'getRecommandRecette'])->middleware('auth:sanctum');
  //Update single recette
  Route::POST('updateRecette',[AdminController::class, 'updateRecette'])->middleware('auth:sanctum');
  //Delete single recette
  Route::DELETE('deleteRecette/{recetteid}',[AdminController::class, 'deleteRecette'])->middleware('auth:sanctum');
  //Get recette by state
  Route::GET('getRecettePub/{state}',[AdminController::class, 'getRecettePub'])->middleware('auth:sanctum');
  //Get recette by recommande
  Route::GET('getRecetteRecomd/{state}',[AdminController::class, 'getRecetteRecomd'])->middleware('auth:sanctum');
  //Supprimer la galerie d'une recette
  Route::DELETE('deletegalerie/{id_galerie}',[AdminController::class, 'deletegalerie'])->middleware('auth:sanctum');
  //update galerie d'une recette
  Route::POST('updategalerie',[AdminController::class, 'updategalerie'])->middleware('auth:sanctum');

  


  /*----------------
  GESTION CLIENTS
  ----------------*/
  //Create clients
  Route::POST('createClients',[AdminController::class, 'createClients'])->middleware('auth:sanctum');

  //Get  all clients
  Route::GET('getAllClients',[AdminController::class, 'getAllClients'])->middleware('auth:sanctum');

  //Get single clients
  Route::GET('getSingleClients/{clientsid}',[AdminController::class, 'getSingleClients'])->middleware('auth:sanctum');

  //Update single clients
  Route::PUT('updateClients',[AdminController::class, 'updateClients'])->middleware('auth:sanctum');

  //Delete single clients
  Route::DELETE('deleteClients/{clientid}',[AdminController::class, 'deleteClients'])->middleware('auth:sanctum');
  
  //Liaison avec ambassadeur-compte client
  Route::PUT('clientambassad',[AdminController::class, 'clientambassad'])->middleware('auth:sanctum');




  /*----------------
  GESTION LIVREURS
  ---------------- */
  //Create livreurs 
  Route::POST('createLivreur',[AdminController::class, 'createLivreur'])->middleware('auth:sanctum');

  //Get all livreurs
  Route::get('getAllLivreur',[AdminController::class, 'getAllLivreur'])->middleware('auth:sanctum');

  //Get single livreurs
  Route::get('getSingleLivreur/{livreur}',[AdminController::class, 'getSingleLivreur'])->middleware('auth:sanctum');

  //Update livreurs
  Route::POST('updatlivreur',[AdminController::class, 'updatlivreur'])->middleware('auth:sanctum');

  //Delete livreurs
  Route::DELETE('deleteLivreur',[AdminController::class, 'deleteLivreur'])->middleware('auth:sanctum');

  //Livreur shipping : enregistrer une livraison
  Route::POST('livreurLivraison',[AdminController::class, 'livreurLivraison'])->middleware('auth:sanctum');

  //Listes des commandes d'un livreur
  Route::GET('orderliv/{livreurid}',[AdminController::class, 'orderliv'])->middleware('auth:sanctum');

  //Liste des des commandes en fonction du status_livreur
  Route::GET('orderLivreurStat',[AdminController::class, 'orderLivreurStat'])->middleware('auth:sanctum');
  
  //Créditer du solde livreur
  Route::POST('crediterSoldeLiv',[LivreurController::class, 'crediterSoldeLiv'])->middleware('auth:sanctum');





  /* ---------------------
  GESTION DES COMMANDES
  ---------------------*/
    //Create commande
    Route::POST('creatorder',[AdminController::class, 'creatorder'])->middleware('auth:sanctum');
    //Get all commande
    Route::GET('getAllorder',[AdminController::class, 'getallorder'])->middleware('auth:sanctum');
    //Get single commande
    Route::GET('getsinglorder/{ordersid}',[AdminController::class, 'getsinglorder'])->middleware('auth:sanctum');
    //Update commande
    Route::PUT('updatorder',[AdminController::class, 'updatorder'])->middleware('auth:sanctum');
    //Delete commande
    Route::DELETE('deletOrder/{order}',[AdminController::class, 'deletOrder'])->middleware('auth:sanctum');
    //Details commande :: recupérer les plats d'une commande
    Route::GET('getOrderPlats/{numcomd}',[AdminController::class, 'getOrderPlats'])->middleware('auth:sanctum');
    //Mise à jour du statut de la commande :: statut_livreur
    Route::POST('UpdOrderstatusLivreur',[AdminController::class, 'UpdOrderstatusLivreur'])->middleware('auth:sanctum');
    //Mise à jour du statut :: statut_client
    Route::POST('UpdOrderstatusClient',[AdminController::class, 'UpdOrderstatusClient'])->middleware('auth:sanctum');
    //Affecter une commande au livreur
    Route::POST('giveOrderToLivreur',[AdminController::class, 'giveOrderToLivreur'])->middleware('auth:sanctum');
    //Get order by state
    Route::GET('getOrderState/{state}',[AdminController::class, 'getOrderState'])->middleware('auth:sanctum');

    


  /* -------------------------
    GESTION DES CREDITS DIDOU
  ----------------------------*/
  //Create crédit
  Route::POST('creatcredit',[AdminController::class, 'creatcredit'])->middleware('auth:sanctum');
  //Get All crédit client
  Route::get('getAllcredit',[AdminController::class, 'getAllcredit'])->middleware('auth:sanctum');



  /* ----------------------
    GESTION DES AMBASSADEURS
  ------------------------ */
  //soumettre une demande
  Route::POST('creatambassad',[AdminController::class, 'creatambassad'])->middleware('auth:sanctum');
  //modifier le status
  Route::PUT('updambassad',[AdminController::class, 'updambassad'])->middleware('auth:sanctum');
  //liste des ambassadeurs
  Route::GET('getAllambassad',[AdminController::class, 'getAllambassad'])->middleware('auth:sanctum');
  //recuperer un ambassadeur
  Route::GET('getSinglambassad/{clientID}',[AdminController::class, 'getSinglambassad'])->middleware('auth:sanctum');
  //crediter le solde
  Route::PATCH('creditersoldAmbasad',[AdminController::class, 'creditersoldAmbasad'])->middleware('auth:sanctum');
  //recuperer les commandes d'un ambassadeur
  Route::GET('getCommdAmbassad/{ambasdid}',[AdminController::class, 'getCommdAmbassad'])->middleware('auth:sanctum');

  /* ------------------------
    GESTION DES CAMPAGNES PUSH
  -------------------------- */
  //Enregistrer un push
  Route::POST('creatpush',[AdminController::class, 'creatpush'])->middleware('auth:sanctum');
  //recupérer les push
  Route::GET('getallpush',[AdminController::class, 'getallpush'])->middleware('auth:sanctum')->middleware('auth:sanctum');
  //Rechercher une push
  Route::GET('searchPush',[AdminController::class, 'searchPush'])->middleware('auth:sanctum');
  //Supprimer une campagne push
  Route::DELETE('deletepush/{id_push}',[AdminController::class, 'deletepush'])->middleware('auth:sanctum');
  //Delivery days
  Route::GET('getdeliverydays',[AdminController::class, 'getdeliverydays']);

  
  
 
  /* --------------------------
    GESTION DES COMPTES USERS
  -------------------------- */
  //Generer OTP 
  Route::POST('generateAdminOTP',[AdminController::class, 'generateAdminOTP']);
  //Create user
  Route::POST('creatuser',[AdminController::class, 'creatuser']);
  //Login user
  Route::POST('loginuser',[AdminController::class, 'loginuser']);
  //Get all user
  Route::GET('getalluser',[AdminController::class, 'getalluser'])->middleware('auth:sanctum');
  //Update user
  Route::PUT('updatuser',[AdminController::class, 'updatuser'])->middleware('auth:sanctum');
  //Delete single user
  Route::DELETE('deletuser/{userid}',[AdminController::class, 'deletuser'])->middleware('auth:sanctum');
  //Login auth user
  Route::POST('login', [AuthController::class, 'login'])->middleware('auth:sanctum');
  //Utilisateur connecté
  Route::GET('getuserauth',[AdminController::class, 'getuserauth'])->middleware('auth:sanctum');
  //Logout user
  Route::GET('logout',[AdminController::class, 'logout'])->middleware('auth:sanctum');
  
  
  /**
   * --------------
   *  SETTING DIDOU 
   * --------------
   * 
   */
    //paramétrer didou
    Route::PUT('setting',[AdminController::class, 'setting'])->middleware('auth:sanctum');
    //recuperer les paramètres Didou
    Route::GET('getsetting',[AdminController::class, 'getsetting'])->middleware('auth:sanctum');
  
  /**
   * --------------
   *  GESTION ZONE
   * --------------
   * 
   */
    //enregistrer une zone
    Route::POST('creatzone',[AdminController::class, 'creatzone'])->middleware('auth:sanctum');
    //liste des zones
    Route::GET('getallzone',[AdminController::class, 'getallzone'])->middleware('auth:sanctum');
    //modifier une zone
    Route::PUT('updatezone',[AdminController::class, 'updatezone'])->middleware('auth:sanctum');
    //changer le status d'une zone
    Route::PATCH('updstatuszone',[AdminController::class, 'updstatuszone'])->middleware('auth:sanctum');
    //Commande by zone
    Route::GET('getOrderzone',[AdminController::class, 'getOrderzone'])->middleware('auth:sanctum');
  
  /**
   * ---------------
   *  GESTION STATS
   * ---------------
   * 
   */
   //Total des commande par période
   //Total des clients par période
   //Meilleur ambassadeur
   //Recettes plus vendus
   //Commandes récentes
   //Solde CinetPay


/*------------------
    API CLIENT
  ------------------
 */
    /**
     * -------------------
     * USER AUTHENTICATION
     * -------------------
     */

        //Inscription client
        Route::POST('createClientCount',[ClientController::class, 'createClientCount']);
        //Login client
        Route::POST('loginUserCount',[ClientController::class, 'loginUserCount']);
        //Générer un code OTP
        Route::POST('generateOTP',[ClientController::class, 'generateOTP']);
        //Check OTP code
        Route::POST('checkOTP',[ClientController::class, 'checkOTP']);
        //resset password
        Route::POST('newpassword',[ClientController::class, 'newpassword'])->middleware('auth:sanctum');
        //update tokenFCM
        Route::PUT('updateTokenFCM',[ClientController::class, 'updateTokenFCM']);
        //change pass livreur
        Route::POST('changeUserPass',[ClientController::class, 'changeUserPass']);

        

    /**
     * -----------
     * PARAMETRES
     * -----------
     */
        //get user infos
        Route::GET('getclientInfos',[ClientController::class, 'getclientInfos'])->middleware('auth:sanctum');
        //update user infos
        Route::PUT('updateclientInfos',[ClientController::class, 'updateclientInfos'])->middleware('auth:sanctum');


    /**
     * ---------------
     * CREDITS DIDOU
     * ---------------
     */
        //Get all user crédit
        Route::GET('getAllcreditUser',[ClientController::class, 'getAllcreditUser'])->middleware('auth:sanctum');
        //Rembourser user crédit
        Route::PUT('rembourserCreditUser',[ClientController::class, 'rembourserCreditUser'])->middleware('auth:sanctum');
        //Credit didou utilisé
        Route::PUT('usedCreditUser',[ClientController::class, 'usedCreditUser'])->middleware('auth:sanctum');

         
    
    /**
     * ---------------
     * COMMANDES CLIENT
     * ---------------
     */
        //Add Client command
        Route::POST('addClientComd',[ClientController::class, 'addClientComd'])->middleware('auth:sanctum');
        //Cancel ou Annueler une commande du client 
        Route::PUT('cancelClientComd',[ClientController::class, 'cancelClientComd'])->middleware('auth:sanctum');
        //Note Client command
        Route::POST('noteClientComd',[ClientController::class, 'noteClientComd'])->middleware('auth:sanctum');
        //Get Client command status
        Route::GET('getClientComdstatus/{commande_status}',[ClientController::class, 'getClientComdstatus'])->middleware('auth:sanctum');
        //Get Client command by id
        Route::GET('getClientComdId',[ClientController::class, 'getClientComdId'])->middleware('auth:sanctum');
        //Get Client's all command
        Route::GET('getClientComdAll',[ClientController::class, 'getClientComdAll'])->middleware('auth:sanctum');
        //Change Client command status
        Route::PUT('updateClientComdstatus',[ClientController::class, 'updateClientComdstatus'])->middleware('auth:sanctum');

    /**
     * -------------------
     * AMBASSADEUR CLIENT
     * -------------------
     */
      //Débiter solde ambassadeur
      Route::PATCH('debiterAmbassadeur',[ClientController::class, 'debiterAmbassadeur'])->middleware('auth:sanctum');

    /**
     * -------------------------
     * NOTIFICATION PUSH CLIENT
     * -------------------------
     */
       //Add push
       Route::POST('addUserPush',[ClientController::class, 'addUserPush'])->middleware('auth:sanctum');
       //Get all push
       Route::GET('getUserPush',[ClientController::class, 'getUserPush'])->middleware('auth:sanctum');
       //Delete push
       Route::DELETE('deleteUserPush',[ClientController::class, 'deleteUserPush'])->middleware('auth:sanctum');
    
    /**
     * --------------------
     * PAIEMENT CINETPAY
     * --------------------
     */
      //Traitemnet du notify :: PAY IN
      Route::match(["get","post"],'notify_pay',[ClientController::class,'notify_pay']);
      //Traitement du return ::  PAY IN
      Route::match(["get","post"],'return_pay',[ClientController::class,'return_pay']);
      //Traitement du notify :: PAY OUT
      Route::match(["get","post"],'notify_transfert',[ClientController::class,'notify_transfert']);
      //Traitement du return :: PAY OUT
      Route::match(["get","post"],'return_transfert',[ClientController::class,'return_transfert']);





        

        
        



/*------------------
    API LIVREUR
  ------------------
 */ 

    /**
     * ---------------
     * AUTHENTICATION
     * ---------------
     */
       //Inscription 
       Route::POST('inscription_livreur',[LivreurController::class, 'inscription_livreur']);
       //Connection
       Route::POST('login_livreur',[LivreurController::class, 'login_livreur']);
       //Get livreur info
       Route::GET('get_livreur_info',[LivreurController::class, 'get_livreur_info'])->middleware('auth:sanctum');
       //Update livreur info
       Route::POST('update_livreur_info',[LivreurController::class, 'update_livreur_info'])->middleware('auth:sanctum');

       

       

    /**
     * --------------------
     * COMMANDES LIVREURS
     * --------------------
     */
       //Get all livreur commandes
       Route::GET('get_livreur_comd_all',[LivreurController::class, 'get_livreur_comd_all'])->middleware('auth:sanctum');
       //Get livreur commande by status
       Route::GET('get_livreur_comd_status/{status_commande}',[LivreurController::class, 'get_livreur_comd_status'])->middleware('auth:sanctum');
       //Get livreur commande today by status
       Route::GET('get_livreur_today_command_status/{status}',[LivreurController::class, 'get_livreur_today_command_status'])->middleware('auth:sanctum');
       //Get livreur commande today
       Route::GET('get_livreur_today_command',[LivreurController::class, 'get_livreur_today_command'])->middleware('auth:sanctum');
       //Change commande status
       Route::PUT('change_livreur_comd_status',[LivreurController::class, 'change_livreur_comd_status'])->middleware('auth:sanctum');


    /**
     * --------------------
     * SOLDE LIVREUR 
     * --------------------
     */
        //Débiter le solde
        Route::POST('debiter_solde_livreur',[LivreurController::class, 'debiter_solde_livreur'])->middleware('auth:sanctum');
        //Get livreur solde
        Route::GET('get_livreur_solde',[LivreurController::class, 'get_livreur_solde'])->middleware('auth:sanctum');
        //Get all transactions
        Route::GET('get_livreur_all_transactions',[LivreurController::class, 'get_livreur_all_transactions'])->middleware('auth:sanctum');

        




