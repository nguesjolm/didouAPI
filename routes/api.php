<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\livreursController;




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
  Route::POST('createAvis',[ClientController::class, 'createAvis'])->middleware('auth:sanctum');
  //Get avis by Mentions
  Route::GET('getAvisByMentions',[AdminController::class, 'getAvisByMentions'])->middleware('auth:sanctum');
  //Get commentaire by mentions
  Route::GET('getCommentByMentions',[AdminController::class, 'getCommentByMentions'])->middleware('auth:sanctum');
  //Get commentaire mentions by date
  Route::GET('getCommentByDateMentions',[AdminController::class, 'getCommentByDateMentions'])->middleware('auth:sanctum');


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


  /*----------------
  GESTION RECETTES
  ----------------*/
  //Create recette
  Route::POST('createRecette',[AdminController::class, 'createRecette'])->middleware('auth:sanctum');
  //Get all recette
  Route::get('getAllRecette',[AdminController::class, 'getAllRecette'])->middleware('auth:sanctum');
  //Get single recette
  Route::get('getSingleRecette/{recetteid}',[AdminController::class, 'getSingleRecette'])->middleware('auth:sanctum');
  //Update single recette
  Route::POST('updateRecette',[AdminController::class, 'updateRecette'])->middleware('auth:sanctum');
  //Delete single recette
  Route::DELETE('deleteRecette/{recetteid}',[AdminController::class, 'deleteRecette'])->middleware('auth:sanctum');


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
  GESTION livreursS
  ---------------- */
  //Create livreurss 
  Route::POST('createlivreurs',[AdminController::class, 'createlivreurs'])->middleware('auth:sanctum');

  //Get all livreurss
  Route::get('getAlllivreurs',[AdminController::class, 'getAlllivreurs'])->middleware('auth:sanctum');

  //Get single livreurss
  Route::get('getSinglelivreurs/{livreurs}',[AdminController::class, 'getSinglelivreurs'])->middleware('auth:sanctum');

  //Update livreurss
  Route::POST('updatlivreurs',[AdminController::class, 'updatlivreurs'])->middleware('auth:sanctum');

  //Delete livreurss
  Route::DELETE('deletelivreurs',[AdminController::class, 'deletelivreurs'])->middleware('auth:sanctum');

  //livreurs shipping : enregistrer une livraison
  Route::POST('livreursLivraison',[AdminController::class, 'livreursLivraison'])->middleware('auth:sanctum');

  //Listes des commandes d'un livreurs
  Route::GET('orderliv/{livreursid}',[AdminController::class, 'orderliv'])->middleware('auth:sanctum');

  //Liste des des commandes en fonction du status_livreurs
  Route::GET('orderlivreursStat',[AdminController::class, 'orderlivreursStat'])->middleware('auth:sanctum');
  
  //Créditer du solde livreurs
  Route::POST('crediterSoldeLiv',[AdminController::class, 'crediterSoldeLiv'])->middleware('auth:sanctum');





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
    //Mise à jour du statut de la commande :: statut_livreurs
    Route::POST('UpdOrderstatuslivreurs',[AdminController::class, 'UpdOrderstatuslivreurs'])->middleware('auth:sanctum');
    //Mise à jour du statut :: statut_client
    Route::POST('UpdOrderstatusClient',[AdminController::class, 'UpdOrderstatusClient'])->middleware('auth:sanctum');

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
  Route::GET('getSinglambassad/{ambasdid}',[AdminController::class, 'getSinglambassad'])->middleware('auth:sanctum');
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


  /* --------------------------
    GESTION DES COMPTES USERS
  -------------------------- */
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
    Route::GET('getOrderzone/{zoneid}',[AdminController::class, 'getOrderzone'])->middleware('auth:sanctum');
  
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
        Route::POST('create_client_Count',[ClientController::class, 'create_client_Count']);
        //Login client
        Route::GET('loginUserCount',[ClientController::class, 'loginUserCount']);
        //Générer un code OTP
        Route::GET('generateOTP',[ClientController::class, 'generateOTP']);
        //Check OTP code
        Route::GET('checkOTP',[ClientController::class, 'checkOTP']);
        //resset password
        Route::GET('newpassword',[ClientController::class, 'newpassword']);

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
        Route::GET('getClientComdstatus',[ClientController::class, 'getClientComdstatus'])->middleware('auth:sanctum');
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
      //Traitemnet du notify
      Route::match(["get","post"],'return_pay',[ClientController::class,'return']);
      //Traitement du return
      Route::match(["get","post"],'notify_pay',[ClientController::class,'notify']);



        

        
        



/*------------------
    API livreurs
  ------------------
 */ 

    /**
     * ---------------
     * AUTHENTICATION
     * ---------------
     */

    /**
     * --------------------
     * COMMANDES livreursS
     * --------------------
     */
       //Get all livreurs commandes
       Route::GET('get_livreurs_comd_all',[livreursController::class, 'get_livreurs_comd_all']);
       //Get livreurs commande by status
       Route::GET('get_livreurs_comd_status',[livreursController::class, 'get_livreurs_comd_status']);
       //Get livreurs commande today
       Route::GET('get_livreurs_today_command',[livreursController::class, 'get_livreurs_today_command']);
       //Change commande status
       Route::GET('change_livreurs_comd_status',[livreursController::class, 'change_livreurs_comd_status']);


    /**
     * --------------------
     * SOLDE livreurs 
     * --------------------
     */
        //Débiter le solde
        Route::POST('debiter_solde_livreurs',[livreursController::class, 'debiter_solde_livreurs']);
        //Get livreurs solde
        Route::GET('get_livreurs_solde',[livreursController::class, 'get_livreurs_solde']);
        //Get all transactions
        Route::GET('get_livreurs_all_transactions',[livreursController::class, 'get_livreurs_all_transactions']);

        




