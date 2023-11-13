<?php

use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Kutia\Larafirebase\Facades\Larafirebase;



/*----------------------
  INTEGRATION CINETPAY
-----------------------*/
use App\Cinetpay\Cinetpay;
use App\Cinetpay\CinetPayService;

/**
 *  ----------------------------
 *  SYSTEME DE GESTION DE API
 *          API ADMIN
 *  ----------------------------
 */
        /*--------------------
         STATISTIQUE ImmOver
        ----------------------*/
            //Total des commandes livrées
            function OrderTotal()
            {
              $orderAll = DB::table('commandes')->where('statut_client','=',"success")->get();
              $nb = count($orderAll);
              $data = ['orderTotal' => $nb];
              return response()->json(['statusCode'=>'200',
                                      'status'=>'true',
                                      'message'=>'Total des commandes',
                                      'data'=> $data,
                                      'error'=> '',
                                    ]);
            }
            //Total des clients
            function ClientsTotal()
            {
              $clientAll = DB::table('clients')->get();
              $nb = count($clientAll);
              $data = ['clientsTotal' => $nb];
              return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>'Total des clients',
                                        'data'=> $data,
                                        'error'=> '',
                                     ]);
            }
            //Total solde
            function soldeTotal()
            {
              $orderAll = DB::table('commandes')->where('statut_client','=',"success")->get();
              $nb = count($orderAll);
              if ($nb==0) {
                $data = ['soldeTotal' => 00];
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'Solde Total',
                                          'data'=> $data,
                                          'error'=> '',
                                       ]);
              }else{
                $amount = 0;
                foreach ($orderAll as $order)
                {
                  $amount = $amount+$order->montant;
                }
                $data = ['soldeTotal' => $amount];
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Solde Total',
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
              }
             
            }
            //Total des recettes
            function RecetteTotal()
            {
              $plattsAll = DB::table('plats')->where('disponible','=',"1")->get();
              $nb = count($plattsAll);
              $data = ['orderTotal' => $nb];
              return response()->json(['statusCode'=>'200',
                                      'status'=>'false',
                                      'message'=>'Total des recettes',
                                      'data'=> $data,
                                      'error'=> '',
                                    ]);
            }
            //Total commandes livrées par zone
            function OrderByzone($zoneid)
            {
              $orderAll = DB::table('commandes')->where('statut_client','=',"success")->get();
              $orderZone = DB::table('commandes')->where('statut_client','=',"success")->where('zone_idzone','=',$zoneid)->get();
              $nbZone = count($orderZone);
              $nbAll = count($orderAll);
              if ($nbAll==0) {
                $totalZone = 0;
              }else{
                $totalZone = (100*$nbZone)/$nbAll;
              }
              
              $data = ['orderTotal' => $totalZone.'%'];
              return response()->json(['statusCode'=>'200',
                                      'status'=>'false',
                                      'message'=>'Total des commandes livrée dans la zone',
                                      'data'=> $data,
                                      'error'=> '',
                                    ]);
            }
            //Commande récente
            function OrderLast()
            {
              $orderAll = DB::table('commandes')->where('statut_client','=',"init")->latest('idcommandes')->take(5)->get();
              $nb = count($orderAll);
              if ($nb!=0)
              {
                     $data  = [];
                     foreach ($orderAll as $comd)
                     {
                        $data  [] = [
                          'id'             => $comd->idcommandes,
                          'numComd'        => $comd->numComd,
                          'livreur'        => $comd->livreur,
                          'statut_livreur' => $comd->statut_livreur,
                          'statut_client'  => $comd->statut_client,
                          'ambassadeur'    => $comd->ambassadeur,
                          'code_gps'       => $comd->code_gps,
                          'idclients'      => $comd->idclients,
                          'zone_idzone'    => $comd->zone_idzone,
                          'qte'            => $comd->qte,
                          'montant'        => $comd->montant,
                          'dateComd'       => $comd->dateComd,
                        ];
                     }
                     return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Les 5 dernières commandes livrées de la zone',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
              }
              else{
                      return response()->json(['statusCode'=>'404',
                                                'status'=>'false',
                                                'message'=>'Aucune commande trouvé',
                                                'data'=> '',
                                                'error'=> '',
                                              ]);
              }
            }




        /*--------------------
         GESTION AVIS CLIENTS
        ----------------------*/
              //Enregistrer
              function createAvis($mentions,$comment,$datecomm)
              {
                //Data
                $data = ['mentions'=>$mentions,
                        'commentaires'=>$comment,
                        'datecommentaires'=>$datecomm,
                      ];
                //Check
                if ($mentions=='') {
                  return response()->json(
                    ['statusCode'=>'422',
                      'status'=>'false',
                      'message'=>"Veuillez préciser votre mention",
                      'data'=> '',
                      'error'=> '',
                    ]
                  );
                }else{
                  DB::table('avis_clients')->insert($data);
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Merci pour votre avis",
                                          'data'=> $data,
                                          'error'=> '',
                                          ]);
                }

              }
              //Get avis by mentions
              function getAvisByMentions($mentions)
              {
                $avisAll = DB::table('avis_clients')->where('mentions','=',$mentions)->get();
                $nb = count($avisAll);
                $data[] = ['avis' => $nb];
                return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>'Avis total',
                                        'data'=> $data,
                                        'error'=> '',
                                        ]);
              
              }
              //Get commentaire by mentions
              function getCommentByMentions($mentions)
              {
                $avisAll = DB::table('avis_clients')->where('mentions','=',$mentions)->get();
                $nb = count($avisAll);
                if ($nb!=0)
                {
                  $data[] = [];
                  foreach ($avisAll as $avis) 
                  {
                    $data[] = [
                      'id'=> $avis->idavis,
                      'mentions'=> $avis->mentions,
                      'commentaires'=> $avis->commentaires,
                      'datecommentaires'=> $avis->datecommentaires,
                    ];
                  }
                  return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Commentaires des avis clients',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                }
                else
                {
                  return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucun commentaire trouvé',
                                          'data'=> [],
                                          'error'=> '',
                                        ]);
                }
              }
              //Get commentaire mention by date
              function getCommentByDateMentions($mentions,$dateCommentaires)
              {
                $avisAll = DB::table('avis_clients')->where('mentions','=',$mentions)->where('datecommentaires','=',$dateCommentaires)->get();
                $nb = count($avisAll);
                if ($nb!=0)
                {
                  $data[] = [];
                  foreach ($avisAll as $avis) 
                  {
                    $data[] = [
                      'id'=> $avis->idavis,
                      'mentions'=> $avis->mentions,
                      'commentaires'=> $avis->commentaires,
                      'datecommentaires'=> $avis->datecommentaires,
                    ];
                  }
                  return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Commentaires des avis clients',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                }
                else
                {
                  return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucun commentaire trouvé',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
                }
              }

        /*--------------------
          GESTION SUPPLEMENT
        ----------------------*/
          //Delete supplement
          function DeleteSupplementID($id)
          {
            $res = DB::table('supplements')->where('idsupplements', '=', $id)->delete();
            return $res;
          }
          //Update supplement state
          function ChangeSupplementState($id,$state)
          {
            $res =  DB::table('supplements')->where('idsupplements','=',$id)
                                            ->update(['status'=>$state]);
            return $res;
          }
          //Create supplement
          function CreateSupplement($nom,$prix,$photo)
          {
              //Data
              $data = ['nom'=>ucfirst($nom),
                        'image'  =>$photo,
                        'prix'   =>$prix,
                        'status' =>'true',
                      ];
              //Check existing
              $res = DB::table('supplements')->where('nom','=',$nom)->first();
              //result
              if ($res) {
                  return response()->json(
                    ['statusCode'=>'422',
                     'status'=>'false',
                     'message'=>"ce supplément existe déjà",
                     'data'=> '',
                     'error'=> '',
                    ]
                  );
              }
              else
              {
                DB::table('supplements')->insert($data);
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"nouveau complément ajouté avec succès",
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
              }  
              
          }
          //Modifier supplement
          function UpdateSupplement($nom,$image,$status,$idsupplement)
          {
            $resImg = false;
            $resNom = false;
            $resStatus = false;
            if($image!='')
            {
              DB::table('supplements')->where('idsupplements','=',$idsupplement)
                                      ->update(['image'=>$image]);
              $resImg = true;
            }
            if ($nom!='') 
            {
              DB::table('supplements')->where('idsupplements','=',$idsupplement)
                                      ->update(['nom'=>$nom]);
              $resNom = true;
            }
            if ($status!='')
            {
              DB::table('supplements')->where('idsupplements','=',$idsupplement)
                                      ->update(['status'=>$status]);
              $resStatus = true;
            }
            if ($resNom==true || $resStatus==true || $resImg==true)
            {
              return response()->json(
                                    ['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=>"Mise à jour du supplément effectuée avec succès",
                                     'data'=> '',
                                     'error'=> '',
                                    ]
                              );
            }
            else
            {
              return response()->json(
                                      ['statusCode'=>'422',
                                      'status'=>'false',
                                      'message'=>"Mise à jour échoué, vos champs sont vides",
                                      'data'=> '',
                                      'error'=> '',
                                      ]
                                    );
            }

          }
          //Historique supplement
          function getSupplement()
          {
            $supall = DB::table('supplements')->get();
            $nb = count($supall);
            if ($nb!=0) {
              $data = [];
              foreach ($supall as $sup) 
              {
                $data[] = [
                  'id'  => $sup->idsupplements,
                  'nom' => $sup->nom,
                  'image' => env('APP_URL').$sup->image,
                  'status' => $sup->status
                ];
              }
              return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>'Liste des suppléments',
                                        'data'=> $data,
                                        'error'=> '',
                                       ]);
            }
            else 
            {
              return response()->json(['statusCode'=>'404',
                                        'status'=>'false',
                                        'message'=>'Aucun supplément trouvé',
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
            }
          }
          //Rechercher supplement
          function searchSupplement($nom)
          {
            $supplemments = DB::table('supplements')->where('nom','LIKE','%'.$nom.'%')->get();
            $nb = count($supplemments);
            if ($nb!=0) 
            {
              $data = [];
              foreach ($supplemments as $sup) 
              {
                $data[] = [
                  'id'  => $sup->idsupplements,
                  'nom' => $sup->nom,
                  'image' => $sup->image,
                  'status' => $sup->status
                ];
              }
              return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>'supplement trouvé',
                                        'data'=> $data,
                                        'error'=> '',
                                      ]);
            }else{
              return response()->json(['statusCode'=>'404',
                                        'status'=>'false',
                                        'message'=>'Aucun supplément trouvé',
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
            }
          }
          //Recuperer un supplement by ID
          function getsingleSupplement($suplID)
          {
            $supplement = DB::table('supplements')->where('idsupplements','=',$suplID)->first();
            return $supplement;
          }
          //Get recettes's supplement
          function getRecetteSupplement($recetteid)
          {
            $recetteSupplement= DB::table('plats_supplements')->where('recettes','=',$recetteid)->get();
            $dataSupplement = [];
            foreach ($recetteSupplement as $supplement)
            {
               $dataSupplement[] = ['id'=> $supplement->id,
                                    'recettes'=> $supplement->recettes,
                                    'supplements'=> getsingleSupplement($supplement->supplements) 
                                  ];
            }
            return $dataSupplement;
          }
          //Get recette's galerie
          function getRecetteGalerie($recetteid)
          {
            //Get recette categorie
            $recetteGalerie = DB::table('plats_galeries')->where('recettes','=',$recetteid)->get();
            $dataGalerie = [];
            foreach ($recetteGalerie as $galerie)
            {
               $dataGalerie[] = ['id'=> $galerie->id,
                                 'recettes'=> $galerie->recettes,
                                 'images'=> env('APP_URL').$galerie->images
                               ];
            }
            return  $dataGalerie;
          }


        /*--------------------------
          GESTION CATEGORIES
          - 200 : succès
          - 404 : non trouvé
          - 422 : erreur de validation
          - 500 : erreur du serveur
        ----------------------------*/

            //Create categories
            function saveCatg($catg,$photo)
            {
                //Data
                 $data = ['nomcateg'=>ucfirst($catg),'photo'=>$photo];
                //Check
                $res = $res = DB::table('categorie')->where('nomcateg','=',$catg)->first();
                //Result
                if ($res) 
                {
                  return response()->json(['statusCode'=>'422',
                                            'status'=>'false',
                                            'message'=>"cette catégorie existe déjà",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
                }
                else
                {
                  DB::table('categorie')->insert($data);
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"nouvelle catégorie ajoutée avec succès",
                                            'data'=> $data,
                                            'error'=> '',
                                          ]);
                }
            }
            
            //Get All categories
            function getAllCatg()
            {
              $catgall = DB::table('categorie')->get();
              $nb = count($catgall);
              if($nb!=0)
              {
                  $data          = [];
                  foreach ($catgall as $catg)
                  {
                    $data[] = [
                      'id'            => $catg->idcategorie,
                      'nomcateg'      => $catg->nomcateg,
                      'photo'         => env('APP_URL').$catg->photo,
                      'active'        => $catg->active,
                      'created_at'    => $catg->created_at,
                      'updated_at'    => $catg->updated_at
                    ];
                  }
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'Liste des catégories',
                                            'data'=> $data,
                                            'error'=> '',
                                          ]);
              }
              else
              {
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucune categorie trouvé',
                                          'data'=> '',
                                          'error'=> '',
                                         ]);
              }
              
            } 

            //Search categories
            function searchCatg($catg)
            {
              $catgAll = DB::table('categorie')->where('nomcateg','LIKE','%'.$catg.'%')->get();
              $nb = count($catgAll);
              if ($nb!=0)
              {
                $data = [];
                foreach ($catgAll as $catg) {
                  $data[] = [
                    'id'  => $catg->idcategorie,
                    'categorie' => $catg->nomcateg,
                    'image' => $catg->photo,
                  ];
                }
                return response()->json(['statusCode'=>'200',
                                         'status'=>'true',
                                         'message'=>'catégorie trouvé',
                                         'data'=> $data,
                                         'error'=> '',
                                       ]);
              }
              else
              {
                return response()->json(['statusCode'=>'404',
                                        'status'=>'false',
                                        'message'=>'Aucune categorie  trouvée!',
                                        'data'=> '',
                                        'error'=> '',
                                  ]);
              }
            }


            //Get single categories 
            function getOrderCatg($catgid)
            {
              $catgdata = DB::table('plats')->where('categorie_idcategorie','=',$catgid)->get();
              $nb = count($catgdata);
              if ($nb!=0) 
              {
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"récuperer les plats d'une catégorie",
                                          'data'=> $catgdata,
                                          'error'=> '',
                                        ]);
              }
              else 
              {
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucun plat trouvé pour cette catégorie',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
            }

            //Update categories
            function updateCatg($nomcatg,$photocatg,$idcatg)
            {
              if ($nomcatg!='') 
              {
                DB::table('categorie')->where('idcategorie','=',$idcatg)
                                    ->update(['nomcateg'=>$nomcatg]);
              }
              if ($photocatg!='') {
                DB::table('categorie')->where('idcategorie','=',$idcatg)
                                      ->update(['photo'=>$photocatg]);
              }
              return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Mise à jour de la cateogorie effectuée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
            }

            //Delete categorie
            function  deleteCatg($catgid)
            {
               $catplats = DB::table('plats')->where('categorie_idcategorie','=',$catgid)->get();
               foreach ($catplats as $plat) 
               {
                 DB::table('panier')->where('plats_idplats','=',$plat->idplats)->delete();
               }
               DB::table('plats')->where('categorie_idcategorie','=',$catgid)->delete();
               DB::table('categorie')->where('idcategorie','=',$catgid)->delete();
               return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"categorie supprimée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
            }

            //Change status
            function changeState($catg,$status)
            {
              $res = DB::table('categorie')
                             ->where('idcategorie',$catg)
                             ->update(['active' => $status]);
              return $res;
            }

        


        /*------------------
        GESTION DES RECETTES
        --------------------*/
            //Get recette by recomanded state
            function getRecetteRecomd($state)
            {
              $recetteall = DB::table('plats')->where('recommanded','=',$state)->get();
              $nb = count($recetteall);
              if ($nb!=0) 
              {
                $data  = [];
                foreach ($recetteall as $recette) 
                {
                  $data[] = [
                    'id'           => $recette->idplats,
                    'nomrecette'   => $recette->nomrecette,
                    'description'  => $recette->description,
                    'image'        => env('APP_URL').$recette->image,
                    'prix'         => $recette->prix,
                    'stock'        => $recette->stock,
                    'disponible'   => $recette->disponible,
                    'recommanded'  => $recette->recommanded,
                    'supplement'   => getRecetteSupplement($recette->idplats),
                    'galerie'      => getRecetteGalerie($recette->idplats),
                    'categorie'    => $recette->categorie_idcategorie,
                  ];
                }
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'Liste des recettes',
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
              }
              else
              {
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucune recette trouvée',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
              
            }

            //Get recette by state
            function getRecetteStat($state)
            {
              $recetteall = DB::table('plats')->where('disponible','=',$state)->get();
              $nb = count($recetteall);
              if ($nb!=0) 
              {
                $data  = [];
                foreach ($recetteall as $recette) 
                {
                  $data[] = [
                    'id'           => $recette->idplats,
                    'nomrecette'   => $recette->nomrecette,
                    'description'  => $recette->description,
                    'image'        => env('APP_URL').$recette->image,
                    'prix'         => $recette->prix,
                    'stock'        => $recette->stock,
                    'disponible'   => $recette->disponible,
                    'recommanded'  => $recette->recommanded,
                    'supplement'   => getRecetteSupplement($recette->idplats),
                    'galerie'      => getRecetteGalerie($recette->idplats),
                    'categorie'    => $recette->categorie_idcategorie,
                  ];
                }
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'Liste des recettes',
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
              }
              else
              {
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucune recette trouvée',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
              
            }

            //Create recette
            function createRecette($nomrecette,$description,$image,$prix,$categorie,$recommanded,$stock)
            {
              $data = ['nomrecette'=>$nomrecette,
                       'description'=>$description,
                       'image'=>$image,
                       'prix'=>$prix,
                       'stock'=>$stock,
                       'recommanded'=>$recommanded,
                       'disponible'=>true,
                       'categorie_idcategorie'=>$categorie,
                      ];
               //Check
               $res = DB::table('plats')->where('nomrecette','=',$nomrecette)->first();
               //result
               if ($res) 
               {
                return response()->json(['statusCode'=>'422',
                                          'status'=>'false',
                                          'message'=>"cette recette existe déjà",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
               }
              else 
              {
                DB::table('plats')->insert($data);
                $res = DB::table('plats')->where('nomrecette','=',$nomrecette)->first();
                return $res->idplats;
              }
               
            }

            //save recettes galerie
            function recetteGalerie($recette,$image)
            {
               DB::table('plats_galeries')->insert(['recettes'=>$recette,'images'=>$image]);
            }
            //save recettes supplements
            function recetteSupplement($recette,$supplement)
            {
              DB::table('plats_supplements')->insert(['recettes'=>$recette,'supplements'=>$supplement]);
            }


            //Get All recettes
            function getAllRecette()
            {
              $recetteall = DB::table('plats')->get();
              $nb = count($recetteall);
              if ($nb!=0) 
              {
                $data  = [];
                foreach ($recetteall as $recette) 
                {
                  $data[] = [
                    'id'           => $recette->idplats,
                    'nomrecette'   => $recette->nomrecette,
                    'description'  => $recette->description,
                    'image'        => env('APP_URL').$recette->image,
                    'prix'         => $recette->prix,
                    'stock'        => $recette->stock,
                    'disponible'   => $recette->disponible,
                    'recommanded'  => $recette->recommanded,
                    'supplement'   => getRecetteSupplement($recette->idplats),
                    'galerie'      => getRecetteGalerie($recette->idplats),
                    'categorie'    => $recette->categorie_idcategorie,
                  ];
                }
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'Liste des recettes',
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
              }
              else
              {
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucune recette trouvée',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
              
            }
            //Get one recette in by id
            function getplatbyID($id)
            {
               $recettedata = DB::table('plats')->where('idplats','=',$id)->first();
               return $recettedata;
            }
            //Get single recette
            function getSingleRecette($recetteid)
            {
              $recettedata = DB::table('plats')->where('idplats','=',$recetteid)->first();
              if ($recettedata) 
              {
                 $data[] = [
                    'id'           => $recettedata->idplats,
                    'nomrecette'   => $recettedata->nomrecette,
                    'description'  => $recettedata->description,
                    'image'        => env('APP_URL').$recettedata->image,
                    'prix'         => $recettedata->prix,
                    'disponible'   => $recettedata->disponible,
                    'stock'        => $recettedata->stock,
                    'recommanded'  => $recettedata->recommanded,
                    'categorie'    => $recettedata->categorie_idcategorie,
                  ];
                  //Get recette categorie
                  $recetteGalerie = DB::table('plats_galeries')->where('recettes','=',$recetteid)->get();
                  $dataGalerie = [];
                  foreach ($recetteGalerie as $galerie)
                  {
                     $dataGalerie[] = ['id'=> $galerie->id,
                                       'recettes'=> $galerie->recettes,
                                       'images'=> $galerie->images
                                     ];
                  }
                  //Get recette supplement
                  $recetteSupplement= DB::table('plats_supplements')->where('recettes','=',$recetteid)->get();
                  $dataSupplement = [];
                  foreach ($recetteSupplement as $supplement)
                  {
                     $dataSupplement[] = ['id'=> $supplement->id,
                                          'recettes'=> $supplement->recettes,
                                          'supplements'=> getsingleSupplement($supplement->supplements) 
                                        ];
                  }

                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'recuperer une recette',
                                          'data'=> $data,
                                          'galerie'=> $dataGalerie,
                                          'supplement'=>$dataSupplement,
                                          'error'=> '',
                                        ]);
              } 
              else 
              {
                return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'aucune recette trouvée',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
              
            }

            //Update recette
            function updateRecette($nomrecette,$description,$categorie,$recommanded,$disponible,$stock,$prix,$image,$recetteid)
            {
              if ($stock!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['stock'=>$stock]);
              }
              if ($nomrecette!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['nomrecette'=>$nomrecette]);
              }

              if ($description!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['description'=>$description]);
              }

              if ($categorie!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['categorie_idcategorie'=>$categorie]);
              }

              if ($disponible!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['disponible'=>$disponible]);
              }

              if ($recommanded!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['recommanded'=>$recommanded]);
              }

              if ($prix!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['prix'=>$prix]);
              }

              if ($image!='') 
              {
                DB::table('plats')->where('idplats','=',$recetteid)
                                  ->update(['image'=>$image]);
              }
            
              return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Mise à jour de la recette effectuée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                    ]);
            }

            //Update recette galerie
            function updateRecetteGalerie($recetteid,$image)
            {
              if ($image!='') 
              {
                DB::table('plats_galeries')->insert(['recettes'=>$recetteid,'images'=>$image]);
              }
            }
            //Vider la galerie en fonction de la recette
            function DelGalerieRecette($recetteid)
            {
              DB::table('plats_galeries')->where('recettes','=',$recetteid)->delete();
            }

            //Update recette supplement
            function updateRecetteSupplement($id,$supplement)
            {
              if ($supplement!='') 
              {
                DB::table('plats_supplements')->where('id','=',$id)
                                              ->update(['supplements'=>$supplement]);
              }
            }

            //Get supplement all recette
            function getAllSupRecette($recette)
            {
               $suppleID =  DB::table('plats_supplements')->where('recettes','=',$recette)->get();
               return $suppleID;
            }

            
            //Delete supplemet recettes by recettes id
            function deleteRecetteID($recette)
            {
              DB::table('plats_supplements')->where('recettes','=',$recette)->delete();
            }

            //Delete recette
            function deleteRecette($recetteid)
            {
               $res = DB::table('plats')->where('idplats','=',$recetteid)->delete();
               
               return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"recette supprimée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
            }

            //Changer le state
            function changeStateRecette($recette,$state)
            {
               DB::table('plats')->where('idplats','=',$recette)
                                ->update(['disponible'=>$state]);
            }

            /*--------------------
              GESTION DES CLIENTS
            ----------------------*/

              //Create client
              function createClients($nom,$email,$tel,$parain)
              {
                $data = ['nom'=>$nom,
                         'email'=>$email,
                         'tel'=>$tel,
                         'status'=>1,
                         'datecreat'=>date('d-m-Y'),
                         'parain'=>$parain
                       ];
                //Check
                $restel = DB::table('client')->where('tel','=',$tel)->first();
                $resmail = DB::table('client')->where('email','=',$email)->first();
                //result
                if ($restel == null && $resmail == null) 
                {
                   DB::table('client')->insert($data);
                   return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'compte client crée avec succès',
                                            'data'=> '',
                                            'error'=> '',
                                           ]);
                } 
                else 
                {
                      return response()->json(['statusCode'=>'422',
                                                'status'=>'false',
                                                'message'=>'Ce compte client existe déjà',
                                                'data'=> '',
                                                'error'=> '',
                                              ]);
                }
                
              }

              //Get All clients
              function getAllClients()
              {
                 $clientall = DB::table('clients')->where('status','=','actif')->get();
                 $nb = count($clientall);
                  if ($nb!=0)
                  {
                    $data  = [];
                    foreach ($clientall as $client) 
                    {
                      $data[] = [
                        'id'          => $client->idclient,
                        'id_user'     => $client->iduser,
                        'user'        => User::firstwhere('id',$client->iduser),
                        'status'      => $client->status,
                        'ambassadeur' => $client->ambassadeur,
                        'status'      => $client->status,
                        'created_at'  => Carbon::parse($client->created_at)->format('d-m-Y'),
                        
                      ];
                    }
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des comptes client',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);

                  } 
                  else
                  {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucun compte client trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
                 
              }

              //Get all client token
              function ClientToken()
              {
                $clientall = DB::table('clients')->get();
                return $clientall;
              }
              
              //Get client infos by id
              function getClientById($client)
              {
                 $clientdata = DB::table('clients')->where('idclient','=',$client)->first();
                 $user = $clientdata->iduser;
                 $userdata =  DB::table('users')->where('id','=',$user)->first();
                 return $userdata;
              }

              //Get single client
              function getSingleClients($clientsid)
              {
                  $clientdata = DB::table('clients')->where('idclient','=',$clientsid)->first();
                  if ($clientdata) {
                    $data[] = [
                      'status'        => $clientdata->status,
                      'parain'        => $clientdata->parain,
                      'iduser'        => $clientdata->iduser,
                      'datecreat'     => $clientdata->datecreat
                    ];
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Afficher un compte client',
                                              'data'=> $data,
                                              'error'=> '',
                                          ]);
                  } else {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'aucun compte client trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
                 
              }

              //Get single client by user
              function getSingleClientsUser($userid)
              {
                  $clientdata = DB::table('clients')->where('iduser','=',$userid)->first();
                  return $clientdata;
                
              }


              //Update client
              function updateClients($nom,$email,$tel,$id)
              {
                  $data = ['nom'=>$nom,
                           'email'=>$email,
                           'tel'=>$tel
                          ];
                  DB::table('client')->where('idclient','=',$id)
                                     ->update($data);
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"Mise à jour du compte client effectuée avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //Delete client
              function deleteClients($clientsid)
              {
                DB::table('clients')->where('idclient','=',$clientsid)->update(["status"=>'inactif']);;
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"compte client supprimé avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }

              //Liaison ambassadeur-client :: ajouter l'id de l'ambassadeur
              function clientambassad($idclient,$codeambassad)
              {
                $data = ['parain'=>$codeambassad];
                DB::table('client')->where('idclient','=',$idclient)
                                   ->update($data);
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Compte client parrainé avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
            
            /*---------------------
              GESTION DES LIVREURS
            ----------------------*/
              //Update livreur tokenFCM
              function updateTokenFCM($livreur,$tokenFCM)
              {
                DB::table('livreurs')->where('idlivreur','=',$livreur)
                                     ->update(['tokenFCM'=>$tokenFCM]);
              }
              //Update solde
              function updateLivreurSolde($livreur,$solde)
              {
                DB::table('livreurs')->where('idlivreur','=',$livreur)
                                     ->update(['solde'=>$solde]);
              }
              //Create livreur
              function createLivreur($nom,$tel,$email,$local,$pass)
              {
               
                $data = ['name'=>$nom,
                         'tel'=>$tel,
                         'email'=>$email,
                         'password'=> Hash::make($pass),
                         'role'=>'livreur'
                        ];
                //Check
                $restel = DB::table('users')->where('tel', $tel)->first();
                $resmail = DB::table('users')->where('email', $email)->first();
                //result
                if($restel == null && $resmail == null)
                {
                   DB::table('users')->insert($data);
                   $user = DB::table('users')->where('tel', $tel)->first();
                   $dataLivreur = ['local'=>$local,'id_user'=>$user->id,'photo'=>'storage/app/public/livreur/hot_delivery.png'];
                   DB::table('livreurs')->insert($dataLivreur);
                   //Send SMS
                   $msg = 'Votre mot de passe livreur est : '.$pass;
                  //  Sendsms($msg,$tel,"DIDOU");
                  SendEmail($email,'Ouverture de compte livreur',$msg);
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'livreur ajouté avec succès',
                                            'data'=> '',
                                            'error'=> '',
                                           ]);
                } 
                else
                {
                  return response()->json(['statusCode'=>'422',
                                           'status'=>'false',
                                           'message'=>"le numéro de téléphone ou l'email  existe déjà",
                                           'data'=> '',
                                           'error'=> '',
                                          ]);
                }   
              
              }


              //Get all livreurs
              function getAllLivreur()
              {
                  $livreurall = DB::table('livreurs')->get();
                
                  $nb = count($livreurall);
                  if ($nb!=0)
                  {
                     $data  = [];
                     foreach ($livreurall as $livreur) 
                     {
                        $livraison =  DB::table('commandes')
                                        ->where('statut_livreur', '=', 'success')
                                        ->where('livreur', '=', $livreur->idlivreur)
                                        ->get();
                        $data  [] = [
                          'id'       => $livreur->idlivreur,
                         'local'    => $livreur->local,
                          'user'     => User::firstwhere('id',$livreur->id_user),
                          'status'   => $livreur->status,
                          'solde'    => formatPrice($livreur->solde),
                          'photo'    => env('APP_URL').$livreur->photo,
                          'livraison'=> count($livraison),
                        ];
                     }
                     
                     return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des livreurs',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                   
                  }       
                  else
                  {
                      return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucun livreur trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
                  
              }

              //Function getLivreur info
              function getLivreurInfo($livreur)
              {
                $livreurdata = DB::table('livreurs')->where('id_user','=',$livreur)->first();                
                return $livreurdata;
              }

              //Get single livreurs
              function getSingleLivreur($user)
              {
                  $livreurdata = DB::table('livreurs')->where('id_user','=',$user)->first();
                  if ($livreurdata)
                  {
                    $data[] = [
                      'id'     => $livreurdata->idlivreur,
                      'local'  => $livreurdata->local,
                      'solde'  => $livreurdata->solde,
                      'photo'  => $livreurdata->photo,
                      'status' => $livreurdata->status,
                      'user'   => DB::table('users')->where('id',$livreurdata->id_user)->first(),
                      'commandes' => LivreurOrder($livreurdata->idlivreur)
                    ];
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Afficher un livreur',
                                              'data'=> $data,
                                              'error'=> '',
                                          ]);
                  }
                  else 
                  {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'aucun livreur trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                          ]);
                  }
                 
              }

              //Get livreur id
              function getLivreurID($userid)
              {
                $livreurdata = DB::table('livreurs')->where('id_user','=',$userid)->first();
                return $livreurdata;
              }

              //get livreur with his id
              function getLivreurById($id_livreur)
              {
                $livreurdata = DB::table('livreurs')->where('idlivreur','=',$id_livreur)->first();
                if ($livreurdata) {
                  $user = DB::table('users')->where('id',$livreurdata->id_user)->first();
                  return $user;
                }else{
                  return "";
                }
               
              }


              //Update livreur
              function  updatlivreur($nom,$tel,$email,$local,$status,$id)
              { 
                  $data = ['nom'=>$nom,
                           'tel'=>$tel,
                           'email'=>$email,
                           'local'=>$local,
                           'status'=>$status,
                           'id'=>$id 
                          ];
              
                  //Check livreur data
                  $livreur = DB::table('livreurs')->where('idlivreur','=',$id)->first();
               
                  if ($nom!='' && $livreur) {
                    DB::table('users')->where('id','=',$livreur->id_user)
                                      ->update(['name'=>$nom]);
                  }
                  
                  if ($tel!='' && $livreur) {
                    DB::table('users')->where('id','=',$livreur->id_user)
                                        ->update(['tel'=>$tel]);
                  }

                  if ($email!='' && $livreur) {
                    DB::table('users')->where('id','=',$livreur->id_user)
                                        ->update(['email'=>$email]);
                  }

                  if ($local!='' && $livreur) {
                    DB::table('livreurs')->where('idlivreur','=',$id)
                                        ->update(['local'=>$local]);
                  }

                  if ($status!='' && $livreur) {
                    DB::table('livreurs')->where('idlivreur','=',$id)
                                        ->update(['status'=>$status]);
                  }

                
                 

                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"Mise à jour effectuée avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //Delete livreur
              function deleteLivreur($livreur)
              {
                  DB::table('livreurs')->where('idlivreur','=',$livreur)->delete();
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"Livreur supprimé avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //Enregistrer une livraison
              function livreurLivraison($orderid,$livreur)
              {
                  $data = ['livreur'=>$livreur];
                  DB::table('commandes')->where('idcommandes','=',$orderid)
                                               ->update($data);
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Livraison de la commande attribuée avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }

              //Livraison des livreurs
              function orderOfLivreur($livreur)
              {
                 $livraisondata = DB::table('commandes')->where('livreur','=',$livreur)->get();
                 $nb = count($livraisondata);
                 if ($nb!=0) 
                 {
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'recupérer les commande du livreur',
                                              'data'=> $livraisondata,
                                              'error'=> '',
                                            ]);
                 }
                 else
                 {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucune commande trouvée',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                 }
              }

              //Commande des livreurs
              function LivreurOrder($livreur)
              {
                 $livraisondata = DB::table('commandes')->where('livreur','=',$livreur)->get();
                 $nb = count($livraisondata);
                 if ($nb!=0) {
                   $data = [];
                   foreach ($livraisondata as $order) {

                      $client = DB::table('clients')->where('idclient',$order->idclients)->first();
                      $user = DB::table('users')->where('id',$client->iduser)->first();
                      $data[] = [
                       'numcommd'  => $order->numComd,
                       'qte'       => $order->qte,
                       'montant'   => $order->montant,
                       'client'    => $user->name.' - Tel: '.$user->tel,
                       'date'      => $order->created_at,
                       'status'    => $order->statut_livreur,

                     ];
                   }
                   return $data;
                 }else{
                   return "";
                 }
              }

              //Liste des commandes en fonction du staut_livreur
              function orderLivreurStat($livreurid,$status)
              {
                  $orderdata = DB::table('commandes')->where('livreur','=',$livreurid)
                                                     ->where('statut_livreur','=',$status)
                                                     ->get();
                  $nb = count($orderdata);
                  if ($nb!=0) 
                  {
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'commandes du livreur en fonction du statut_livreur',
                                              'data'=> $orderdata,
                                              'error'=> '',
                                            ]);
                  }
                  else
                  {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucune commande trouvée',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
              }

              //Créditer le solde d'un livreur
              function crediterSoldeLiv($livreurid)
              {
                 $livreurdata = DB::table('livreurs')->where('idlivreur','=',$livreurid)->first();
                 $solde = $livreurdata->solde+setting()->gainlivreur;
                 $data = ['solde'=>$solde];
                 DB::table('livreurs')->where('idlivreur','=',$livreurid)
                                     ->update($data);
                 #save transaction
                 DB::table('livreur_pay')->insert(['montant'=>setting()->gainlivreur,
                                                   'date'=>date('j F Y, H:i'),
                                                   'livreur_idlivreur'=>$livreurid,
                                                   "type"=>"dépôt",
                                                  ]);

                 return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Votre solde a été crédit de ".setting()->gainlivreur." fcfa, vous avez ".$solde." Fcfa sur votre solde",
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
              }

             


            /*---------------------
              GESTION DES COMMANDES
            ----------------------*/

              //Generer le numéro de commande
              function NumComd()
              {
                do{
                  $numCom = "D".rand(0,99999).random_1(4);
                  $numComd = strtoupper($numCom);
                  $comd = DB::table('commandes')->where('numComd','=',$numComd)->first();
                }
                while ($comd!=null);
                return $numComd;
              }

              //Save commande
              function  savecomd($numComd,$clientid,$amountComd,$qteComd,$gps,$zoneid,$dateComd,$statutClient,$ambasd)
              {
                $data = ['numComd'=>$numComd,
                        'idclients'=>$clientid,
                        'ambassadeur'=>$ambasd,
                        'montant'=>$amountComd,
                        'qte'=>$qteComd,
                        'code_gps'=>$gps,
                        'zone_idzone'=>$zoneid,
                        'dateComd'=>$dateComd,
                        'statut_client'=>$statutClient
                      ];
                DB::table('commandes')->insert($data);
              }

              //Save commande details :: save plats's commmande
              function savecomprod($platId,$qte,$amount,$numComd,$client,$precision_plats)
              {
                $data = ['client_idclient'=>$client,
                        'plats_idplats'=>$platId,
                        'qte'=>$qte,
                        'numComd'=>$numComd,
                        'montant'=>$amount,
                        'precision_plats'=>$precision_plats
                      ];
                DB::table('panier')->insert($data);
              }

              //save commande panier supplements
              function saveCommandSupplement($numComd,$plat_id,$supplement)
              {
                  $data = ['numComd'=>$numComd,
                           'plats_id'=>$plat_id,
                           'supplement_id'=>$supplement,
                  ];
                  DB::table('panier_supplements')->insert($data);
              }

              //Get all order by state
              function getallorderState($state)
              {
                $commdall = DB::table('commandes')->where('statut_client','=',$state)->get();
                $nb = count($commdall);
                if ($nb!=0)
                 {
                    $data  = [];
                    foreach ($commdall as $comd)
                    {
                       $data  [] = [
                         'id'             => $comd->idcommandes,
                         'numComd'        => $comd->numComd,
                         'livreur'        => $comd->livreur,
                         'statut_livreur' => $comd->statut_livreur,
                         'statut_client'  => $comd->statut_client,
                         'ambassadeur'    => $comd->ambassadeur_code,
                         'code_gps'       => $comd->code_gps,
                         'idclients'      => $comd->idclients,
                         'zone_idzone'    => $comd->zone_idzone,
                         'qte'            => $comd->qte,
                         'montant'        => $comd->montant,
                         'dateComd'       => $comd->dateComd,
                       ];
                    }
                    return response()->json(['statusCode'=>'200',
                                             'status'=>'true',
                                             'message'=>'Liste des commandes',
                                             'data'=> $data,
                                             'error'=> '',
                                           ]);
                 }
                 else
                 {
                     return response()->json(['statusCode'=>'404',
                                               'status'=>'false',
                                               'message'=>'Aucune commande trouvé',
                                               'data'=> '',
                                               'error'=> '',
                                             ]);
                 }
                
              }
              //Update client TokenFCM
              function updateFCM($client,$token)
              {
                DB::table('clients')->where('idclient','=',$client)
                                    ->update(['tokenFCM'=>$token]);
              }
              //Get client infos
              function ClientID($client)
              {
                $client = DB::table('clients')->where('idclient',$client)->first();
                $data[] = [
                  'user' => DB::table('users')->where('id',$client->iduser)->first(),
                  'client'  => $client,
                ];
                return $data;

              }
              //Get livreur infos
              function LivreurID($livreur)
              {
                $livreur = DB::table('livreurs')->where('idlivreur',$livreur)->first();
                $data[] = [
                  'user' => DB::table('users')->where('id',$livreur->id_user)->first(),
                  'livreur'  => $livreur,
                ];
                return $data;

              }

              //Get all order
              function getallorder()
              {
                 $commdall = DB::table('commandes')->get();
                 $nb = count($commdall);
                 if ($nb!=0)
                  {
                     $data  = [];
                     foreach ($commdall as $comd)
                     {
                        $livreur = '';
                        if ($comd->livreur) {
                          $livreur = LivreurID($comd->livreur);
                        }
                        $data  [] = [
                          'id'             => $comd->idcommandes,
                          'numComd'        => $comd->numComd,
                          'livreur'        => $livreur,
                          'statut_livreur' => $comd->statut_livreur,
                          'statut_client'  => $comd->statut_client,
                          'ambassadeur'    => $comd->ambassadeur_code,
                          'code_gps'       => $comd->code_gps,
                          'client'         => ClientID($comd->idclients),
                          'zone_idzone'    => DB::table('zone')->where('idzone',$comd->zone_idzone)->first(),
                          'qte'            => $comd->qte,
                          'montant'        => $comd->montant,
                          'dateComd'       => $comd->dateComd,
                          
                        ];
                     }
                     return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des commandes',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                  }
                  else
                  {
                      return response()->json(['statusCode'=>'404',
                                                'status'=>'false',
                                                'message'=>'Aucune commande trouvé',
                                                'data'=> '',
                                                'error'=> '',
                                              ]);
                  }
                 
              }

              //Get order by id
              function getsinglorder($orderid)
              {
                 $orderdata = DB::table('commandes')->where('idcommandes','=',$orderid)->first();
                 if ($orderdata) 
                 {
                    $livreur = '';
                    if ($orderdata->livreur) {
                      $livreur = LivreurID($orderdata->livreur);
                    }
                    $data  [] = [
                      'id'               => $orderdata->idcommandes,
                      'numComd'          => $orderdata->numComd,
                      'plats'            => getPlatComd($orderdata->numComd),
                      'livreur'          => $livreur,
                      'statut_livreur'   => $orderdata->statut_livreur,
                      'statut_client'    => $orderdata->statut_client,
                      'ambassadeur_code' => $orderdata->ambassadeur_code,
                      'code_gps'         => $orderdata->code_gps,
                      'clients'          => getClientById($orderdata->idclients),
                      'lieu'             => getZone_id($orderdata->zone_idzone)->nom,
                      'qte'              => $orderdata->qte,
                      'montant'          => $orderdata->montant,
                      'datecomd'         => $orderdata->dateComd,
                    ];
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'récuperer une commande',
                                              'data'=> $data,
                                              'error'=> '',
                                          ]);
                 } 
                 else
                 {
                      return response()->json(['statusCode'=>'404',
                                                'status'=>'false',
                                                'message'=>'Aucune commande trouvée',
                                                'data'=> '',
                                                'error'=> '',
                                              ]);
                 }
                 
              }

              //Update order
              function updatorder($numComd,$idcommandes,$clientid,$ambasd,$amountComd,$qteComd,$gps,$zoneid,$dateComd,$statutClient)
              {
                $data = ['numComd'=>$numComd,
                          'idclients'=>$clientid,
                          'ambassadeur'=>$ambasd,
                          'montant'=>$amountComd,
                          'qte'=>$qteComd,
                          'code_gps'=>$gps,
                          'zone_idzone'=>$zoneid,
                          'dateComd'=>$dateComd,
                          'statut_client'=>$statutClient
                        ];
                DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                      ->update($data);
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Mise à jour effectuée avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }

              //Delete order
              function deletOrder($orderid)
              {
                $orderdata = DB::table('commandes')->where('idcommandes','=',$orderid)->first();
                if ($orderdata) 
                { 
                    DB::table('commandes')->where('idcommandes','=',$orderid)->delete();
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>"commande supprimée avec succès",
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                }
                else 
                {
                  return response()->json(['statusCode'=>'404',
                                           'status'=>'false',
                                           'message'=>"Cette commande n'existe pas",
                                           'data'=> '',
                                           'error'=> '',
                                          ]);
                }
                  
              }

              //Get supplement commande plats
              function getSupplementPlat($numComd,$plat_id)
              {
                $orderdata = DB::table('panier_supplements')->where('numComd','=',$numComd)->where('plats_id','=',$plat_id)->get();
                $nb = count($orderdata);
                $data=[];
                if ($nb!=0) {
                  foreach ($orderdata as $order) {
                    $data [] = getsingleSupplement($order->supplement_id);
                
                  }
                }
                return $data;
              }

              //Get commande plats
              function getPlatComd($numComd)
              {
                $orderdata = DB::table('panier')->where('numComd','=',$numComd)->get();
                $data  = [];
                foreach ($orderdata as $order) 
                {
                  $data  [] = ['id'         => DB::table('plats')->where('idplats','=',$order->plats_idplats)->first()->idplats,
                               'nom'        => DB::table('plats')->where('idplats','=',$order->plats_idplats)->first()->nomrecette,
                               'image'      => DB::table('plats')->where('idplats','=',$order->plats_idplats)->first()->image,
                               'montant'    => $order->montant,
                               'qte'        => $order->qte,
                               'precision'  => $order->precision_plats,
                               'supplement' => getSupplementPlat($numComd,$order->plats_idplats),
                              ];
                }
                return $data;
              }

              //Get recettes by commande
              function getOrderRecette($numComd)
              {
                $orderdata = DB::table('panier')->where('numComd','=',$numComd)->get();
                $nb = count($orderdata);
                if ($nb!=0)
                {
                   $data  = [];
                   foreach ($orderdata as $order) 
                   {
                     $data  [] = [ 'numComd'         => $order->numComd,
                                   'montant'         => $order->montant,
                                   'qte'             => $order->qte,
                                   'client_idclient' => $order->client_idclient, 
                                   'plats_idplats'   => DB::table('plats')->where('idplats','=',$order->plats_idplats)->first(),
                                 ];
                   }
                } 
                return $data;
              }

              //Get commande supplement
              function getOrderSupplement($numComd)
              {
                 $supplement = DB::table('panier_supplements')->where('numComd','=',$numComd)->get();
                 $nb = count($supplement);
                 if ($nb!=0) {
                   $data  = [];
                   foreach ($supplement as $sup) {
                     $data [] = [
                       'supplement' => DB::table('supplements')->where('idsupplements','=', $sup->supplement_id)->first(),
                     ];
                   }
                   return $data;
                 }else{
                  return "";
                 }
              }

              //Get commande details :: recupérer les plats d'une commande
              function getOrderPlats($numComd)
              {
                 $orderdata = DB::table('panier')->where('numComd','=',$numComd)->get();
                 $nb = count($orderdata);
                 if ($nb!=0)
                 {
                    $data  = [];
                    foreach ($orderdata as $order) 
                    {
                      $data  [] = [ 'numComd'         => $order->numComd,
                                    'montant'         => $order->montant,
                                    'qte'             => $order->qte,
                                    'client_idclient' => $order->client_idclient,
                                    'précision'       => $order->precision_plats,
                                    'recettes'        => DB::table('plats')->where('idplats','=', $order->plats_idplats)->first(),
                                    'supplements'     => getOrderSupplement($order->numComd)
                                  ];
                    }

                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'recupérer les produits de la commande',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                 } 
                 else 
                 {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucune commande trouvée',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                 }
              }

              //Mise à jour du statu de la commande :: statut_livreur
              function UpdOrderstatusLivreur($orderid,$statutlivreur)
              {
                 $data = ['statut_livreur'=>$statutlivreur];
                 DB::table('commandes')->where('idcommandes','=',$orderid)
                                       ->update($data);
                 return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Statut_livreur mise à jour avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                       ]);
              }

              //Mise à jour du statu de la commande :: statut_client
              function UpdOrderstatusClient($orderid,$statutClient)
              {
                  $data = ['statut_client'=>$statutClient];
                  DB::table('commandes')->where('idcommandes','=',$orderid)
                                        ->update($data);
                  return response()->json(['statusCode'=>'200',
                                           'status'=>'true',
                                           'message'=>"Status_client mise à jour avec succès",
                                           'data'=> '',
                                           'error'=> '',
                                        ]);
              }

              //Give commande to livreur
              function giveOrderToLivreur($idcommandes,$idlivreur)
              {
                $livreur = getLivreurById($idlivreur);
                // if ($livreur->tokenFCM) {
                //   sendPush($livreur->tokenFCM,'Livraison','une livraison vous a été affectée','','LIVREUR_ACTION');
                // }
                // sendPush($livreur->tokenFCM,'Livraison','une livraison vous a été affectée','','LIVREUR_ACTION');
                SendEmail($livreur->email,'Afffectation de commande','une livraison vous a été affectée');
                DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                      ->update(['livreur'=>$idlivreur,'statut_livreur'=>'pending','statut_client'=>'pending']);
              }

              //Suppression de galerie
              function deletGalerie($id_galerie)
              {
                DB::table('plats_galeries')->where('id', '=', $id_galerie)->delete();
              }
              //update galerie
              function updateGalerie($id,$galerie)
              {
                DB::table('plats_galeries')->where('id','=',$id)
                                           ->update(['images'=>$galerie]);
              }
            
            /**
               * --------------------------
               * GESTION DE CREDIT DIDOU
               * --------------------------
            */
               
              //Générer un crédit
              function creatcredit($clientid)
              {
                
                  $data = DB::table('credit_didou')->where('client_idclient','=',$clientid)
                                                   ->where('statutCredit','=','false') 
                                                   ->first();
                  
                  $setting = DB::table('settings')->where('idsettings','=',1)->first();

                  if($data=='')
                  {
                    //Vérifier si la valeur totale des commandes du client équivaut au minimum  à la valeur paramétré
                    $commande = DB::table('commandes')->where('idclients','=',$clientid)
                                                      ->where('statut_client','=','success') 
                                                      ->get();
                    //Montant total des commandes
                    $montant_total = 0;
                    foreach ($commande as $value) {
                      $montant_total = $montant_total+$value->montant;
                    }
                  
                    if ($montant_total >= $setting->conditionCredit) {
                        $data = ['client_idclient'=>$clientid,
                                 'creditDidou'=>generatecredit(),
                                 'montant'=>setting()->creditDidou,
                                 'dateCredit'=>date('d-m-Y'),
                                ];
                        DB::table('credit_didou')->insert($data);
                        return response()->json(['statusCode'=>'200',
                                       'status'=>'true',
                                       'titre'=>'crédit accordé',
                                       'message'=>"Vous avez ".formatPrice($setting->creditDidou)." fcfa",
                                       'data'=> $data,
                                       'error'=> '',
                                     ]);
                    }else {
                      return response()->json(['statusCode'=>'423',
                                                'status'=>'false',
                                                'titre' =>'Crédit refusé',
                                                'message'=>"Le montant total de toutes vos commandes précédentes doivent être au minimum ".formatPrice($setting->conditionCredit)." fcfa avant d'être éligible",
                                                'data'=> $data,
                                                'error'=> '',
                                              ]);   
                    }
                                 
                  }
                  else
                  {
                    return response()->json(['statusCode'=>'423',
                                              'status'=>'false',
                                              'titre'=>'Crédit refusé',
                                              'message'=>"Vous devez ".$data->montant." fcfa",
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);    
                      
                  }
              }
              
              //Listes des crédits
              function getAllcredit()
              {
                  $creditdata = DB::table('credit_didou')->get();
                  $nb = count($creditdata);
                  if ($nb!=0) 
                  {
                    $data  = [];
                    $creditTotal = 0;
                    $creditPending = 0;
                    $creditPay = 0;
                    foreach ($creditdata as $credit) 
                    {
                        $client = DB::table('clients')->where('idclient','=',$credit->client_idclient)->first();       
                        $data  [] = [ 'id'             =>$credit->idcredit,
                                      'client_idclient'=>$credit->client_idclient,
                                      'user'           => User::firstwhere('id',$client->iduser),
                                      'statutCredit'   =>$credit->statutCredit,
                                      'creditDidou'    =>$credit->creditDidou,
                                      'montant'        =>$credit->montant,
                                      'dateCredit'     =>$credit->dateCredit,
                                      'dateRembourse'  =>$credit->dateRembourse,
                                    ];
                        $creditTotal = $creditTotal+$credit->montant;
                        if ($credit->statutCredit=="false") {
                          $creditPending = $creditPending+$credit->montant;
                        }
                        if ($credit->statutCredit=="true") {
                          $creditPay = $creditPay+$credit->montant;
                        }
                     }
                     return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des crédits',
                                              'creditTotal'=>$creditTotal,
                                              'creditPending'=>$creditPending,
                                              'creditPay'=>$creditPay,
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                  } 
                  else
                  {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucun crédit trouvé',
                                              'data'=> [],
                                              'error'=> '',
                                            ]);
                  }
                  
              }
              //Recupérer les crédit d'un client
              function getAllUSerCredit($clientID)
              {
                $creditdata = DB::table('credit_didou')->where('client_idclient','=',$clientID)->orderByDesc('idcredit')->get();
                $nb = count($creditdata);
                if ($nb!=0) 
                {
                  $data  = [];
                  $creditTotal = 0;
                  $creditPending = 0;
                  $creditPay = 0;
                  foreach ($creditdata as $credit) 
                  {
                      $data  [] = ['id'              =>$credit->idcredit,
                                    'client_idclient'=>$credit->client_idclient,
                                    'statutCredit'   =>$credit->statutCredit,
                                    'credit_used'    =>$credit->credit_used,
                                    'creditDidou'    =>$credit->creditDidou,
                                    'montant'        =>$credit->montant,
                                    'dateCredit'     =>$credit->dateCredit,
                                    'dateRembourse'  =>$credit->dateRembourse,
                                  ];
                      $creditTotal = $creditTotal+$credit->montant;
                      if ($credit->statutCredit=="false") {
                        $creditPending = $creditPending+$credit->montant;
                      }
                      if ($credit->statutCredit=="true") {
                        $creditPay = $creditPay+$credit->montant;
                      }
                   }
                   return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'Liste des crédits',
                                            'creditTotal'=>$creditTotal,
                                            'creditPending'=>$creditPending,
                                            'creditPay'=>$creditPay,
                                            'data'=> $data,
                                            'error'=> '',
                                          ]);
                } 
                else
                {
                  return response()->json(['statusCode'=>'404',
                                            'status'=>'false',
                                            'message'=>'Aucun crédit trouvé',
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
                }
              }

              //Rembourser user's credit
              function rembourserCreditUser($code_didou)
              {
                $creditdata = DB::table('credit_didou')->where('creditDidou','=',$code_didou)->first();
                if ($creditdata)
                {
                  DB::table('credit_didou')->where('creditDidou','=',$code_didou)->update(['statutCredit'=>"true"]);
                }
              }

              //Verifier un code crédit didou
              function CheckCredit($credit_didou,$client)
              {
                $dataCredit = DB::table('credit_didou')->where('creditDidou','=',$credit_didou)
                                                       ->where('client_idclient','=',$client) 
                                                       ->first();
                return $dataCredit;
              }

              //Change credit_didou used status
              function credit_used_status($credit,$status)
              {
                $res = DB::table('credit_didou')->where('creditDidou','=',$credit)->update(['credit_used'=>$status]);
                return $res;
              }

            /**
             * -----------------------
             * GESTION DES AMBASSADEUR
             * -----------------------
             * */  
              
              //Soumettre une demande
              function creatambassad($clientid)
              {
                    $data = ['code'=>codeAmbassad(),
                             'client_idclient'=>$clientid,
                            ];
                    //Check
                    $resambd = DB::table('ambassadeur')->where('client_idclient', $clientid)->first();
                    if($resambd)
                    {
                      return response()->json(['statusCode'=>'422',
                                                'status'=>'false',
                                                'message'=>'Vous avez déjà une demande en cours de traitement, veuillez patienter svp',
                                                'data'=> '',
                                                'error'=> '',
                                            ]);
                    }

                    if ($resambd == null) 
                    {
                        DB::table('ambassadeur')->insert($data);
                        return response()->json(['statusCode'=>'200',
                                                'status'=>'true',
                                                'message'=>"profil ambassadeur soumis avec succès",
                                                'data'=> $resambd,
                                                'error'=> '',
                                                ]);
                    } 
                    else 
                    {
                      return response()->json(['statusCode'=>'422',
                                              'status'=>'false',
                                              'message'=>'Cet ambassadeur existe déjà',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                    }
                    
              }

              //Modifier le status ambassadeur
              function updambassad($ambid,$status)
              {
                  $data = ['statut_ambassadeur'=>$status];
                  DB::table('ambassadeur')->where('idambassadeur','=',$ambid)
                                          ->update($data);

                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"statut ambassadeur modifier avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //Liste des ambassadeur
              function getAllambassad()
              {
                 $ambAll = DB::table('ambassadeur')->get();
                 $nb = count($ambAll);
                 if ($nb!=0) {
                   $data  = [];
                   foreach ($ambAll as $amb)
                   {
                      $client = DB::table('clients')->where('idclient',$amb->client_idclient)->first();
                      $user = DB::table('users')->where('id',$client->iduser)->first();
                      $orderdata = DB::table('commandes')->where('ambassadeur_code','=',$amb->code)->get();
                      $data  [] = [
                        'id'                    => $amb->idambassadeur ,
                        'code'                  => $amb->code,
                        'client_idclient'       => $user->name.' - '.$user->tel,
                        'statut_ambassadeur'    => $amb->statut_ambassadeur,
                        'solde'                 => $amb->solde,
                        'commandes'             => count($orderdata),
                        'date'                  => $amb->created_at
                      ];
                   }
                   return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des ambassadeur',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                 } 
                 else 
                 {
                      return response()->json(['statusCode'=>'404',
                                                'status'=>'false',
                                                'message'=>'Aucun ambassadeur trouvé',
                                                'data'=> '',
                                                'error'=> '',
                                               ]);
                 }
                 
              }
             
              //recuperer un ambassadeur
              function getSinglambassad($clientID)
              {
                $ambData = DB::table('ambassadeur')->where('client_idclient','=',$clientID)->first();
                if ($ambData) 
                {
                  $data[] = [
                    'id'                 => $ambData->idambassadeur ,
                    'code'               => $ambData->code,
                    'client_idclient'    => $ambData->client_idclient ,
                    'statut_ambassadeur' => $ambData->statut_ambassadeur,
                    'solde'              => $ambData->solde,
                    'appLink'            => setting()->applink
                  ];
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'recuper un ambassadeur',
                                            'data'=> $data,
                                            'error'=> '',
                                         ]);
                } 
                else 
                {
                  return response()->json(['statusCode'=>'404',
                                            'status'=>'false',
                                            'message'=>'ambassadeur non trouvé',
                                            'data'=> [],
                                            'error'=> '',
                                         ]);
                }
                
              }
               //get infos client by ambassadeur code
               function getAmb($code)
               {
                  $ambData = DB::table('ambassadeur')->where('code','=',$code)->first();
                  $clientdata = DB::table('clients')->where('idclient','=',$ambData->client_idclient)->first();
                  return $clientdata;
               }

              //Crediter le solde
              function creditersoldAmbasad($amb)
              {
                 $ambData = DB::table('ambassadeur')->where('code','=',$amb)->first();
                //  return $ambData;
                 if ($ambData) {
                    $solde = $ambData->solde+setting()->gainambassadeur;
                    $data = ['solde'=>$solde];
                    DB::table('ambassadeur')->where('code','=',$amb)
                                            ->update($data);
                 }
                 
              }

              //Débiter un solde
              function debiterSoldAmbasad($amb,$montant,$payment_method)
              {
                $ambData = DB::table('ambassadeur')->where('code','=',$amb)->first();
                if ($ambData) {
                 $client = getClientById($ambData->client_idclient);
                 if ($ambData) 
                 {
                  if ($montant <= $ambData->solde)
                  {
                    $data = ['montant'=>$montant,
                             'date'=>date('d-m-Y'),
                             'type'=>"Paiemnet Mobile money",
                             'client_idclient'=>$ambData->client_idclient,
                            ];     
                    //Lancement de CinetPay pour le transfert
                    $transfert_id = date("YmdHis");
                    $phone = $client->tel;
                    $name = $client->name;
                    $email = $client->email ?? support();
                    $type = 'ambassadeur';
                    $profil_id = $ambData->client_idclient;
                    #Calcul de frais
                    $frais = ($montant*2)/100;
                    $total_ttc = $montant+$frais;
                    $solde = $ambData->solde-$total_ttc;    
                    if ($solde < 0) {
                      return response()->json(['statusCode'=>404,
                                              'status'=>false,
                                              'message'=>"Votre solde est insuffisant pour le transfert, frais de retrait : ".$frais." Fcfa",
                                              'data'=> '',
                                              'error'=> '',
                                          ],404);
                    }
                    $res = GuichetPayOut($transfert_id,$phone,$montant,$name,$email,$type,$payment_method,$profil_id);
                    if ($res->code==0) {
                      return response()->json(['statusCode'=>200,
                                              'status'=>false,
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
                  }
                  else
                  {
                    return response()->json(['statusCode'=>404,
                                             'status'=>'false',
                                             'message'=>'Le montant dépasse le solde de votre compte ambassadeur',
                                             'data'=> '',
                                             'error'=> '',
                                            ],404);
                  }
                 }else{
                  return response()->json(['statusCode'=>'404',
                                           'status'=>'false',
                                           'message'=>'Code ambassadeur invalide',
                                           'data'=> '',
                                           'error'=> '',
                                          ]);
                 }
                }else{
                  return response()->json([
                    'statusCode'=>404,
                    'status' => false,
                    'message' => "Ce code ambassadeur n'existe pas",
                    'errors' => ''
                  ], 404);
                }
               
              }

              //get ambassadeur infos single
              function getSingleAmbassadeur($code_amb)
              {
                $amb = DB::table('ambassadeur')->where('code','=',$code_amb)->first();
                return $amb;
              }
              //Insertion dans la table ambassadeur_pay
              function AmbassadeurPay($amount,$profil_id)
              {
                //Calcul des fraiss
                $ambData = DB::table('ambassadeur')->where('client_idclient','=',$profil_id)->first();
                $frais = ($amount*2)/100;
                $total_ttc = $amount+$frais;
                $solde = $ambData->solde-$total_ttc;
                //Mise à jour du solde
                DB::table('ambassadeur')->where('client_idclient','=',$profil_id)
                                        ->update(['solde'=>$solde]);
                //Enregistrement de la transaction
                $data = ['montant'=>$total_ttc,
                         'date'=>date('d-m-Y'),
                         'type'=>"Paiemnet Mobile money",
                         'client_idclient'=>$profil_id,
                ];
                 DB::table('ambassadeur_pay')->insert($data);
              }

              //Recupérer les commandes d'un ambassadeur
              function getCommdAmbassad($ambasd)
              {
                 $orderdata = DB::table('commandes')->where('ambassadeur','=',$ambasd)->get();
                 $nb = count($orderdata);
                 if ($nb!=0) 
                 {
                    return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'recupérer les commande du livreur',
                                            'data'=> $orderdata,
                                            'error'=> '',
                                          ]);
                 } 
                 else 
                 {
                      return response()->json(['statusCode'=>'404',
                                                'status'=>'false',
                                                'message'=>'Aucune commande trouvée',
                                                'data'=> '',
                                                'error'=> '',
                                             ]);
                 }
                 
              }
            
            /**
               * -------------------
               *  GESTION DES PUSH
               * -------------------
            */
              //Enregistrer un push
              function creatpush($pushMsg,$pushImg,$pushTitre,$debut,$fin)
              {
                  $data = ['img'=>$pushImg,
                           'titre'=>$pushTitre,
                           'message'=>$pushMsg,
                           'debut'=>$debut,
                           'fin'=>$fin,
                          ];
                  DB::table('push')->insert($data);
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"campagne push enregistré avec succès",
                                            'data'=> $data,
                                            'error'=> '',
                                          ]);
              }

              //Recuperer les campagnes push
              function getallpush()
              {
                $pushdata = DB::table('push')->get();
                $nb = count($pushdata);
                if ($nb!=0)
                {
                  $data  = [];
                  foreach ($pushdata as $push) 
                  {
                      $data[] = ['id'=>$push->idpush,
                                 'img'=>env('APP_URL').$push->img,
                                 'titre'=>$push->titre,
                                 'message'=>$push->message,
                                 'debut'=>$push->debut,
                                 'fin'=>$push->fin
                                ];
                  }
                  return response()->json(['statusCode'=>'404',
                                            'status'=>'false',
                                            'message'=>'liste des campagnes push',
                                            'data'=> $data,
                                            'error'=> '',
                                          ]);
                } 
                else
                {
                  return response()->json(['statusCode'=>'404',
                                           'status'=>'false',
                                           'message'=>'aucune campagne push trouvée',
                                           'data'=> '',
                                           'error'=> '',
                                        ]);
                }
                
              }
              //Search push
              function searchPush($push)
              {
                  $pushAll = DB::table('push')->where('titre','LIKE','%'.$push.'%')->get();
                  $nb = count($pushAll);
                  if ($nb!=0)
                  {
                    $data = [];
                    foreach ($pushAll as $push) {
                      $data[] = [
                        'id'  => $push->idpush,
                        'img' => $push->img,
                        'titre' => $push->titre,
                        'message' => $push->message,
                        'created_at' => $push->created_at,
                      ];
                    }
                    return response()->json(['statusCode'=>'200',
                                             'status'=>'true',
                                             'message'=>'supplement trouvé',
                                             'data'=> $data,
                                             'error'=> '',
                                           ]);
                  }
                  else
                  {
                    return response()->json(['statusCode'=>'404',
                                            'status'=>'false',
                                            'message'=>'Aucune campagne push trouvée!',
                                            'data'=> '',
                                            'error'=> '',
                                      ]);
                  }
              }

              //Suppression de push
              function deletePush($push_id)
              {
                //Suppression de push
                DB::table('push')->where('idpush', '=', $push_id)->delete();
              }
            
            /**
             * ------------------
             *  COMPTE USER
             * ------------------
             */
              //créer un compte user
              function creatuser($nom,$email,$pass)
              {
                $data = ['email'=>$email,
                         'nam'=>$nom,
                         'password'=>$pass
                        ];
                //Check user
                $dataUser = DB::table('users')->where('email','=',$email)
                                             ->where('password','=',$pass) 
                                             ->first();
                //result
                if ($dataUser)
                {
                    return response()->json(['statusCode'=>'423',
                                              'status'=>'false',
                                              'message'=>"Ce compte existe déjà",
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                } 
                else
                {
                  DB::table('users')->insert($data);
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"compte user crée avec succès",
                                            'data'=> $data,
                                            'error'=> '',
                                          ]);
                }
                
              }

              //recupérer les comptes user
              function getalluser()
              { 
                  $userdata = DB::table('users')->where('role','=','admin')->get();
                  $nb = count($userdata);
                  if ($nb!=0) 
                  {
                     $data  = [];
                     foreach ($userdata as $user) 
                     {
                          $data[] = [ 'id'       =>$user->id,
                                    'email'    =>$user->email,
                                    'name'      =>$user->name,
                                    'password' =>$user->password
                                  ];
                     }
                     return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'recupérer les utilisateurs',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                  } 
                  else 
                  {
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Aucun utilisateur trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
                  
              }

              //modifier un compte user
              function updatuser($nom,$email,$pass,$userid)
              {
                  if ($email!='') {
                    DB::table('users')->where('id','=',$userid)
                                      ->update(['email'=>$email]);
                  }
                  if ($nom!='') {
                    DB::table('users')->where('id','=',$userid)
                                      ->update(['name'=>$nom]);
                  }
                  if ($pass!='') {
                    DB::table('users')->where('id','=',$userid)
                                      ->update(['password'=>$pass]);
                  }
                  
                  
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"Mise à jour effectuée avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //supprimer un user
              function deleteuser($userid)
              {
                 DB::table('users')->where('id','=',$userid)->delete();
                 return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"compte supprimée avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
                 
              }

              //login user
              function loginuser($email,$pass)
              {
                $dataUser = DB::table('users')->where('email','=',$email)
                                              ->where('password','=',$pass) 
                                              ->first();
                if ($dataUser) 
                {
                  //Ouverture de la session
                  $_SESSION['userid'] = $dataUser->iduser;
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"user connecté avec succès",
                                          'data'=> $dataUser,
                                          'error'=> '',
                                        ]);
                } 
                else
                {
                  return response()->json(['statusCode'=>'404',
                                            'status'=>'false',
                                            'message'=>"aucun compte trouvé",
                                            'data'=> $dataUser,
                                            'error'=> '',
                                          ]);
                }
                
              }

              //Deconnection user
              function logout()
              {
                session_destroy();
                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"deconnection effectué avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
            
            /**
             * ----------------
             *  GESTION DE ZONE
             * ----------------
             */
              //enregistrer une zone
              function creatzone($zone,$long,$larg)
              {
                 $data = ['nom'=>ucfirst($zone),'longitude'=>$long,'largitude'=>$larg];  
                 //check
                 $zonedata = DB::table('zone')->where('nom','=',$zone)->first();
                 //result
                 if ($zonedata) {
                  return response()->json(['statusCode'=>'422',
                                              'status'=>'false',
                                              'message'=>'cette zone existe déjà',
                                              'data'=> $data,
                                              'error'=> '',
                                          ]);
                 } else {
                   DB::table('zone')->insert($data);
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'nouvelle zone ajoutée avec succès',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                 }
                 


              }
              //get all zone
              function getallzone()
              {
                 $zonedata = DB::table('zone')->get();
                 $nb = count($zonedata);
                 if ($nb!=0) {
                    $data  = [];
                    foreach ($zonedata as $zone)
                    {
                      $data [] = ['id'=>$zone->idzone,
                                  'nom'=>$zone->nom,
                                  'statut'=>$zone->statut,
                                  'longitude'=>$zone->longitude,
                                  'largitude'=>$zone->largitude
                                 ];  
                    }
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des zones de livraison',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                 } else {
                    return response()->json(['statusCode'=>'404',
                                            'status'=>'false',
                                            'message'=>'Aucune zone trouvée',
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
                 }
                 

              }
              //modifier zone
              function updatezone($zone,$zoneid,$long,$larg)
              {
                DB::table('zone')->where('idzone','=',$zoneid)
                                 ->update(['nom'=>ucfirst($zone),'longitude'=>$long,'largitude'=>$larg]);
                
                return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"Mise à jour de la zone effectuée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
              }

              //changer le status d'une zone
              function updstatuszone($status,$zoneid)
              {
                DB::table('zone')->where('idzone','=',$zoneid)
                                 ->update(['statut'=>$status]);

                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"changement du status de la zone effectuée avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }
              //Commande d'une zone
              function getOrderzone()
              {
                //Get all zones
                $zonedata = DB::table('zone')->get();
                $data = [];
                foreach ($zonedata as $zone) 
                {
                  $zoneid = $zone->idzone;
                  $orderdata = DB::table('commandes')->where('statut_client','=',"success")->get();
                  $orderdatabyzone = DB::table('commandes')->where('zone_idzone','=',$zoneid)
                                                          ->where('statut_client','=',"success")
                                                          ->get();
                  $nborder = count($orderdata);
                  $nbzone = count($orderdatabyzone);
                  if ($nbzone!=0) {
                    $zonepourcenage = (100*$nbzone)/$nborder;
                  }else{
                    $zonepourcenage = 0;
                  }
                    $data [] = [
                       'zone'        =>$zone->nom,
                       'pourcentage' =>$zonepourcenage,
                       'unite'       => '%'
                    ];
                   
                  
                }

                return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'pourcentage de commandes par zone',
                                          'data'=> $data,
                                          'error'=> '',
                                        ]);
                
              }

              //Get zone by id
              function getZone_id($zone_id)
              {
                $zone = DB::table('zone')->where('idzone','=',$zone_id)->first();
                return $zone;
              }


            
            /**
               * ----------------------
               * FONCTION SYSTEM GLOBAL
               * -----------------------
            */
                //recuperer les paramètres didou
                function getSettingIn()
                {
                  $setting = DB::table('settings')->where('idsettings','=',1)->first();
                  return $setting;
                }
                function getsetting()
                {
                  $setting = DB::table('settings')->where('idsettings','=',1)->first();
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"paramètres de Didou",
                                          'data'=> $setting,
                                          'error'=> '',
                                        ]);
                }
                //paramétrer didou
                function settingDidou($gainlivreur,$gainambassadeur,$promoComd,$creditDidou,$conditionCredit,$commandeAffilier)
                {
                    $data = ['gainlivreur'     =>$gainlivreur,
                             'gainambassadeur' =>$gainambassadeur, 
                             'promoComd'       =>$promoComd,
                             'creditDidou'     =>$creditDidou,
                             'conditionCredit' =>$conditionCredit,
                             'commandeAffilier'=>$commandeAffilier
                            ];
                    
                    if ($gainlivreur!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)
                                           ->update(['gainlivreur'=>$gainlivreur]);
                    }
                    if ($gainambassadeur!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)
                                           ->update(['gainambassadeur'=>$gainambassadeur]);
                    }
                    if ($promoComd!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)
                                           ->update(['promoComd'=>$promoComd]);
                    }

                    if ($creditDidou!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)
                                           ->update(['creditDidou'=>$creditDidou]);
                    }

                    if ($creditDidou!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)
                                           ->update(['creditDidou'=>$creditDidou]);
                    }

                    if ($conditionCredit!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)->update(['conditionCredit'=>$conditionCredit]);
                    }

                    if ($commandeAffilier!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)->update(['commandeAffilier'=>$commandeAffilier]);
                    }
                    
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>"Paramétrage didou effectué avec succès",
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                }
                //Générer une chaîne de caractère aléatoire
                function random_1($car) 
                {
                    $string = "";
                    $chaine = "abcdefghijklmnpqrstuvwxy";
                    srand((double)microtime()*1000000);
                    for($i=0; $i<$car; $i++) {
                      $string .= $chaine[rand()%strlen($chaine)];
                    }
                    return $string;
                }
                
                //Setting data
                function setting()
                {
                  $setting = DB::table('settings')->where('idsettings','=',1)->first();
                  return $setting;
                }

                //Générer code ambassadeur
                function codeAmbassad()
                {
                    do{
                      $code = "AMB".rand(0,99999).random_1(1);
                      $codeambassad =  strtoupper($code);
                      $comd = DB::table('ambassadeur')->where('code','=',$codeambassad)->first();
                    } while ($comd!=null);
                    return $codeambassad;
                }
                
                //Générer credit didou
                function generatecredit()
                {
                    do{
                      $code = "CRD".rand(0,99999).random_1(2);
                      $codecredit =  strtoupper($code);
                      $comd = DB::table('credit_didou')->where('creditDidou','=',$codecredit)->first();
                    } while ($comd!=null);
                    return $codecredit;
                }

/**
 *  ----------------------------
 *  SYSTEME DE GESTION DE API
 *          API CLIENT
 *  ----------------------------
 */
    /**
    * --------------
    * AUTHENTICATION
    * --------------
    */
      //Get client
      function getClient($tel,$pass)
      {
        $res = DB::table('clients')->where('tel','=',$tel)->where('password','=',$pass)->first();
        return $res;
      }
    
    /**
    * --------------------
    * GESTION DE COMMANDES
    * --------------------
    */
      //update payement state
      function updatePayState($trans_id)
      {
        DB::table('paiement')->where('transaction_id','=',$trans_id)->update(['state'=>1]);
        DB::table('transaction_commandes')->where('id_transaction','=',$trans_id)->update(['status_transaction'=>1]);
      }
      //Get transactions paiement by transaction_id
      function getTransPay($trans_id)
      {
        $pay = DB::table('paiement')->where('transaction_id','=',$trans_id)->first();
        return $pay;
      }
      //Get transactions commande by transaction_id
      function getTransComd($trans_id)
      {
        $commande = DB::table('transaction_commandes')->where('id_transaction','=',$trans_id)->first();
        return $commande;
      }
      //Suppression panier commande
      function deletepanier($comd)
      {
        DB::table('panier')->where('numComd','=',$comd)->delete();
      }
      //suppression supplement commande
      function deletesup($comd)
      {
        DB::table('panier_supplements')->where('numComd','=',$comd)->delete();
      }
      //suppression commande
      function deletecomd($comd)
      {
        DB::table('transaction_commandes')->where('numcomd','=',$comd)->delete();
      }
      //Save transactions
      function saveTransaction($clientid,$numcomd,$longitude,$largitude,$montantpay,$ambassadeur,$credit_didou,$montant,$qte,$zoneid,$dateComd,$statutClient,$id_transaction,$zoneprecise)
      {
        $data = ['clientid'          => $clientid,
                 'numcomd'           => $numcomd,
                 'longitude'         => $longitude,
                 'largitude'         => $largitude,
                 'montantpay'        => $montantpay,
                 'ambassadeur_code'  => $ambassadeur,
                 'credit_didou'      => $credit_didou,
                 'montant'           => $montant,
                 'qte'               => $qte,
                 'zoneid'            => $zoneid,
                 'zoneprecise'       => $zoneprecise,
                 'dateComd'          => $dateComd,
                 'statutClient'      => $statutClient,
                 'id_transaction'    => $id_transaction,
                ];
        DB::table('transaction_commandes')->insert($data);
      }
      
      //Save commande
      function saveCommand($clientid,$ambassadeur,$credit_didou,$montant,$montantpay,$qte,$zoneid,$dateComd,$statutClient,$numComd,$longitude,$largitude,$zoneprecise)
      {
        $data = ['idclients'         => $clientid,
                 'ambassadeur_code'  => $ambassadeur,
                 'code_credit'       => $credit_didou,
                 'montant'           => $montant,
                 'montantpay'        => $montantpay,
                 'qte'               => $qte,
                 'zone_idzone'       => $zoneid,
                 'zoneprecise'       => $zoneprecise,
                  'longitude'        => $longitude,
                  'largitude'        => $largitude,
                 'dateComd'          => $dateComd,
                 'statut_client'     => $statutClient,
                 'statut_livreur'    => $statutClient,
                 'numComd'           => $numComd
                ];
        DB::table('commandes')->insert($data);
      }

      //Update client commande status
      function updateClientComdstatus($statut_client,$idcommandes)
      {

        $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
        if ($commande) 
        {
    
          if ($statut_client=="pending")
          {
            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client,'statut_livreur'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande en cours de livraison";

          }elseif ($statut_client=="success"){
            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client,'statut_livreur'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande livrée avec succès";

          }elseif ($statut_client=="fail"){
            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client,'statut_livreur'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande annulée avec succès";

          }elseif ($statut_client=="init") {

            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client,'statut_livreur'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande reçu avec succès, vous serez livrée dans 10 minutes";

          }else{
            
            $message = "Status de la commande incorrecte";
            return response()->json(['statusCode' =>'404',
                                      'status'    =>false,
                                      'message'   => $message,
                                      'error'     => '',
                                    ]);
          }
          return response()->json(['statusCode' =>'200',
                                   'status'  =>'true',
                                   'message'=>$message,
                                   'data'   => $commande,
                                   'error'  => '',
                                 ]);
          
        }else{
          return response()->json(['statusCode'=>404,
                                   'status' => false,
                                   'message' => "Cette commande n'existe pas",
                                   'error' => ''
                                 ], 404);
        }
       
      }

      //Get client command status
      function getClientComdstatus($client_id,$status_command)
      {
        $commande = DB::table('commandes')->where('statut_client','=',$status_command)->where('idclients','=',$client_id)->get();
        $nb = count($commande);

        if ($status_command=="init") {
          $state = "nouvelles commandes";
        }elseif ($status_command=="success") {
          $state = "commandes livrées";
        }elseif ($status_command=="fail") {
          $state = "commandes annulées";
        }else {
          $state = "Status de commande inconnu";
        }
      
        if ($nb!=0)
        {
          return response()->json(['statusCode'=>'200',
                                  'status'=>'true',
                                  'message'=> $state,
                                  'data'=> $commande,
                                  'error'=> '',
                                ]);
        }
        else{
          return response()->json(['statusCode'=>404,
                                   'status' => false,
                                   'message' =>  "aucune ".$state,
                                   'error' => ''
                                 ], 404);
        }
      }

      //Get client all command
      function getClientComdAll($client_id)
      {
        $commande = DB::table('commandes')->where('idclients','=',$client_id)->orderBy('idcommandes', 'desc')->get();
        $nb = count($commande);
        if ($nb!=0) {
          $data=[];
          foreach ($commande as $orderdata) 
          {
            $livreur = '';
            if ($orderdata->livreur) {
              $livreur = getLivreurById($orderdata->livreur);
            }
            $data [] = [
              'id'               => $orderdata->idcommandes,
              'clients'          => getClientById($orderdata->idclients),
              'lieu'             => getZone_id($orderdata->zone_idzone)->nom,
              'livreur'          => $livreur,
              'numComd'          => $orderdata->numComd,
              'plats'            => getPlatComd($orderdata->numComd),
              'statut_livreur'   => $orderdata->statut_livreur,
              'statut_client'    => $orderdata->statut_client,
              'ambassadeur_code' => $orderdata->ambassadeur_code,
              'code_gps'         => $orderdata->code_gps,
              'qte'              => $orderdata->qte,
              'montant'          => $orderdata->montant,
              'datecomd'         => $orderdata->created_at,
              'longitude'        => $orderdata->longitude,
              'largitude'        => $orderdata->largitude,
            ];
          }

          return response()->json(['statusCode'=>'200',
                                    'status'=>'true',
                                    'message'=> "Historique des commandes du client",
                                    'data'=> $data,
                                    'error'=> '',
                                  ]);
        }else{
          return response()->json(['statusCode'=>404,
                                   'status'    => false,
                                   'message'   => "Aucune commande trouvée pour ce client",
                                   'error'     => ''
                                  ], 404);
        }
      }

    
    /**
    * ------------------------
    * NOTIFICATION PUSH CLIENT
    * ------------------------
    */
      //Add Push
      function addUserPush($titre,$message,$state,$status,$id_user)
      {
        $data = ['titre'    => $titre,
                 'message' => $message,
                 'state'   => $state,
                 'status'   => $status,
                 'date_add'=> date('d-m-Y H:i'),
                 'id_user' => $id_user,
                ];
       
        DB::table('notifications')->insert($data);

        return response()->json(['statusCode'=>'200',
                                    'status'=>'true',
                                    'message'=> "Notification push envoyé avec succès",
                                    'data'=> $data,
                                    'error'=> '',
                                  ]);
      }
      //Get all push
      function getUserPush($id_user)
      {
        $push = DB::table('notifications')->where('id_user','=',$id_user)->get();
        return $push;
        $nb = count($push);
        if ($nb!=0) {
          return response()->json(['statusCode'=>'200',
                                    'status'=>'true',
                                    'message'=> "Notification push du client",
                                    'data'=> $push,
                                    'error'=> '',
                                  ]);
        }else{
          return response()->json(['statusCode'=>404,
                                   'status'    => false,
                                   'message'   => "Aucune notification push trouvée pour ce client",
                                   'error'     => ''
                                  ], 404);
        }
      }
      //Delete all push
      function deleteUserPush($id)
      {
         $res =  DB::table('notifications')->where('id_push','=',$id)->delete();
         if ($res==0) 
         {
           return response()->json(['statusCode'=>404,
                                    'status'    => false,
                                    'message'   => "Aucune notification push trouvée pour ce compte",
                                    'error'     => ''
                                ], 404);
         }else {
            return response()->json(['statusCode'=>'200',
                                    'status'=>'true',
                                    'message'=> "Notification push supprimée avec succès",
                                    'data'=> '',
                                    'error'=> '',
                                  ]);
         }
         
      
         

        
      }

/**
 *  ----------------------------
 *  SYSTEME DE GESTION DE API
 *          API LIVREUR
 *  ----------------------------
 */
    
    /**
     * -----------------
     * COMMANDE LIVREUR
     * -----------------
     */
       //Get all livreur commandes
       function get_livreur_comd_all($livreur_id)
       {
         $comd_All = DB::table('commandes')->where('livreur','=',$livreur_id)->orderBy('idcommandes','desc')->get();
         $nb = count($comd_All);
         if ($nb!=0) {
            $data = [];
            foreach ($comd_All as $cmd) {
              $data [] = [
                  'id'                => $cmd->idcommandes,
                  'numComd'           => $cmd->numComd,
                  'clients'           => getClientById($cmd->idclients),
                  'zone_livraison'    => getZone_id($cmd->zone_idzone)->nom,
                  'plats'             => getPlatComd($cmd->numComd),
                  'livreur'           => $cmd->livreur,
                  'statut_livreur'    => $cmd->statut_livreur,
                  'statut_client  '   => $cmd->statut_client,
                  'ambassadeur_code'  => $cmd->ambassadeur_code,
                  'code_gps  '        => $cmd->code_gps,
                  'idclients'         => $cmd->idclients,
                  'qte'               => $cmd->qte,
                  'montant'           => $cmd->montant,
                  'code_credit'       => $cmd->code_credit,
                  'dateComd'          => $cmd->dateComd,
                  'created_at'        => $cmd->created_at
              ];
            }
            return response()->json(['statusCode'=>'200',
                                      'status'=>'true',
                                      'message'=> 'commandes du livreur',
                                      'data'=> $data,
                                      'error'=> '',
                                    ]);
         }else{
            return response()->json(['statusCode'=>404,
                                     'status' => false,
                                     'message' =>  "Aucune commande trouvée pour ce livreur",
                                     'error' => ''
                                    ], 404);
         }
         
       }

       //Get livreur commande by status
       function get_livreur_comd_status($livreur_id,$commande_status)
       {
          $comd_All = DB::table('commandes')->where('livreur','=',$livreur_id)->where('statut_livreur','=',$commande_status)->orderBy('idcommandes', 'desc')->get();
          $nb = count($comd_All);
          if ($nb!=0) {
            $data = [];
            foreach ($comd_All as $cmd) {
              $data [] = [
                  'id'                => $cmd->idcommandes,
                  'numComd'           => $cmd->numComd,
                  'clients'           => getClientById($cmd->idclients),
                  'lieu'              => getZone_id($cmd->zone_idzone)->nom,
                  'plats'             => getPlatComd($cmd->numComd),
                  'livreur'           => $cmd->livreur,
                  'statut_livreur'    => $cmd->statut_livreur,
                  'statut_client  '   => $cmd->statut_client,
                  'ambassadeur_code'  => $cmd->ambassadeur_code,
                  'code_gps  '        => $cmd->code_gps,
                  'idclients'         => $cmd->idclients,
                  'qte'               => $cmd->qte,
                  'montant'           => $cmd->montant,
                  'code_credit'       => $cmd->code_credit,
                  'dateComd'          => $cmd->dateComd,
                  'created_at'        => $cmd->created_at
              ];
            }
              return response()->json(['statusCode'=>'200',
                                       'status'=>'true',
                                       'message'=> 'commandes du livreur',
                                       'data'=> $data,
                                       'error'=> '',
                                      ]);
          }else{
           return response()->json(['statusCode'=>404,
                                    'status' => false,
                                    'message' =>  "Aucune commande trouvée pour ce livreur",
                                    'error' => ''
                                   ], 404);
          }
       }

       //Get livreur commande today by status
       function get_livreur_today_command_status($livreur_id,$today_date,$status)
       {
        $comd_All = DB::table('commandes')->where('livreur','=',$livreur_id)->where('dateComd','=',$today_date)->where('statut_livreur','=',$status)->orderBy('idcommandes', 'desc')->get();
        $nb = count($comd_All);
        if ($nb!=0) {
          $data = [];
          foreach ($comd_All as $cmd) {
            $data [] = [
                'id'                => $cmd->idcommandes,
                'numComd'           => $cmd->numComd,
                'clients'           => getClientById($cmd->idclients),
                'lieu'              => getZone_id($cmd->zone_idzone)->nom,
                'plats'             => getPlatComd($cmd->numComd),
                'livreur'           => $cmd->livreur,
                'statut_livreur'    => $cmd->statut_livreur,
                'statut_client  '   => $cmd->statut_client,
                'ambassadeur_code'  => $cmd->ambassadeur_code,
                'code_gps  '        => $cmd->code_gps,
                'idclients'         => $cmd->idclients,
                'qte'               => $cmd->qte,
                'montant_total'     => $cmd->montant,
                'code_credit'       => $cmd->code_credit,
                'dateComd'          => $cmd->dateComd,
                'created_at'        => $cmd->created_at
            ];
          }
            return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=> 'commandes du livreur',
                                     'data'=> $data,
                                     'error'=> '',
                                    ]);
        }else{
         return response()->json(['statusCode'=>404,
                                  'status' => false,
                                  'message' =>  "Aucune commande trouvée pour ce livreur",
                                  'error' => ''
                                 ], 404);
        }
       }


       //Get livreur commande by today
       function get_livreur_today_command($livreur_id,$today_date)
       {
        $comd_All = DB::table('commandes')->where('livreur','=',$livreur_id)->where('dateComd','=',$today_date)->get();
        $nb = count($comd_All);
        if ($nb!=0) {
          $data = [];
          foreach ($comd_All as $cmd) {
            $data [] = [
                'id'                => $cmd->idcommandes,
                'numComd'           => $cmd->numComd,
                'clients'           => getClientById($cmd->idclients),
                'lieu'              => getZone_id($cmd->zone_idzone)->nom,
                'plats'             => getPlatComd($cmd->numComd),
                'livreur'           => $cmd->livreur,
                'statut_livreur'    => $cmd->statut_livreur,
                'statut_client  '   => $cmd->statut_client,
                'ambassadeur_code'  => $cmd->ambassadeur_code,
                'code_gps  '        => $cmd->code_gps,
                'idclients'         => $cmd->idclients,
                'qte'               => $cmd->qte,
                'montant'           => $cmd->montant,
                'code_credit'       => $cmd->code_credit,
                'dateComd'          => $cmd->dateComd,
                'created_at'        => $cmd->created_at
            ];
          }
            return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=> 'commandes du livreur',
                                     'data'=> $data,
                                     'error'=> '',
                                    ]);
        }else{
         return response()->json(['statusCode'=>404,
                                  'status' => false,
                                  'message' =>  "Aucune commande trouvée pour ce livreur",
                                  'error' => ''
                                 ], 404);
        }
       }
      
    /**
     * ------------------
     *   SOLDE LIVREUR
     * ------------------
     */
       //Debiter le solde
        function debiter_solde_livreur($livreurid,$montant)
        {
            $livreurdata = DB::table('livreurs')->where('idlivreur','=',$livreurid)->first();
            if ($livreurdata=="") {
              return response()->json(['statusCode'=>'404',
                                         'status'=>'false',
                                         'message'=>"Ce livreur n'existe pas",
                                         'data'=> $livreurdata,
                                         'error'=> "",
                                        ]);
            }
            if($livreurdata->solde > $montant){
               #Calcul de frais
               $frais = ($montant*2)/100;
               $total_ttc = $montant+$frais;
               $solde = $livreurdata->solde-$total_ttc;
               $data = ['solde'=>$solde];
               DB::table('livreurs')->where('idlivreur','=',$livreurid)
                                    ->update($data);
                #save transaction
                DB::table('livreur_pay')->insert(['montant'=>$total_ttc,
                                                  'date'=>date('j F Y, H:i'),
                                                  'livreur_idlivreur'=>$livreurid,
                                                  "type"=>"retrait",
                                                  ]);

             #valeur retournée
             return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=>"Votre solde a été débité de ".$montant." fcfa il vous reste ".$solde." Fcfa",
                                     'data'=> $livreurdata,
                                     'error'=> '',
                                   ]);
            }
        }
        //Save livreur payment
        function LivreurPay($montant,$livreur_id)
        {
            #save transaction
            DB::table('livreur_pay')->insert(['montant'=>$montant,
                                              'date'=>date('j F Y, H:i'),
                                              'livreur_idlivreur'=>$livreur_id,
                                              "type"=>"retrait",
                                            ]);
        }
       //Get all transaction
       function get_livreur_all_transactions($id_livreur)
       {
          $livreurdata = DB::table('livreur_pay')->where('livreur_idlivreur','=',$id_livreur)->orderBy('idlivreur_pay','desc')->orderBy('idlivreur_pay', 'desc')->get();
          
          $nb = count($livreurdata);
          if ($nb==0) {
            return response()->json(['statusCode'=>'404',
                                       'status'=>'false',
                                       'message'=>"Aucune transactions",
                                       'data'=> $livreurdata,
                                       'error'=> "",
                                      ]);
          }else{
            return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=>"Historique des transactions",
                                     'data'=> $livreurdata,
                                     'error'=> '',
                                   ]);
          }
       }

       //Get livreur solde
       function getLivreurSolde($livreurid)
       {
         $livreurdata = DB::table('livreurs')->where('id_user','=',$livreurid)->first();
       }
     





/**
 *  ----------------------------
 *  SYSTEME DE NOTIFICATION
 *          GLOBAL
 *  ----------------------------
 */
    /**
     * -----------------
     *  NOTIFICATION SMS
     * -----------------
     */
        //Format price
        function formatPrice($price)
        {
          return number_format($price, 0,',', '.');
        }

        //Send SMS V2
        function sendSMS_V2($msg,$tel,$sender)
        {
          $prodUrl = "https://apis.letexto.com/";
          $token = "2701cf6c9ba227f87af1f0e5ededd3";
          $from = $sender;
          $to = $tel;
          $content = $msg;
          $dlrUrl = "https://mydomain.com:4444/dlr";
          $customData = "customData";
          $sendAt = date('j-m-Y h:i:s');

          $client = new Client();
          $url = $prodUrl.'V1/messages/send?from=' . urlencode($from) . '&to=' . $to . '&content=' . urlencode($content) . '&token=' . $token . '&dlrUrl=' . urlencode($dlrUrl) . '&dlrMethod=GET&customData=' . $customData . '&sendAt=' . urlencode($sendAt);
          $request = new Request('GET',$url);
          $res = $client->sendAsync($request)->wait();
          return $res->getBody();


        }
        //Send SMS
        function Sendsms($msg,$tel,$sender)
        {
          // // Filtrer le messages
          // $nvMsg = str_replace('à','a', $msg);
          // $nvMsg = str_replace('á','a', $nvMsg);
          // $nvMsg = str_replace('â','a', $nvMsg);
          // $nvMsg = str_replace('ç','c', $nvMsg);
          // $nvMsg = str_replace('è','e', $nvMsg);
          // $nvMsg = str_replace('é','e', $nvMsg);
          // $nvMsg = str_replace('ê','e', $nvMsg);
          // $nvMsg = str_replace('ë','e', $nvMsg);
          // $nvMsg = str_replace('ù','u', $nvMsg);
          // $nvMsg = str_replace('ù','u', $nvMsg);
          // $nvMsg = str_replace('ü','u', $nvMsg);
          // $nvMsg = str_replace('û','u', $nvMsg);
          // $nvMsg = str_replace('ô','o', $nvMsg);
          // $nvMsg = str_replace('î','i', $nvMsg);
          // $key = '2701cf6c9ba227f87af1f0e5ededd3';
          // $api = 'Authorization: Bearer '.$key."";
          // // Step 1: Créer la campagne
          // $curl = curl_init();
          // $datas= [
          //   'step' => NULL,
          //   'sender' => $sender,
          //   'name' => 'SMS',
          //   'campaignType' => 'SIMPLE',
          //   'recipientSource' => 'CUSTOM',
          //   'groupId' => NULL,
          //   'filename' => NULL,
          //   'saveAsModel' => false,
          //   'destination' => 'NAT_INTER',
          //   'message' => $msg,
          //   'emailText' => NULL,
          //   'recipients' =>
          //   [
          //     [
          //       'phone' => $tel,
          //     ],
          //   ],
          //   'sendAt' => [],
          //   'dlrUrl' => NULL,
          //   'responseUrl' => NULL,
          // ];
          // curl_setopt_array($curl, array(
          //     CURLOPT_URL => 'https://api.letexto.com/v1/campaigns',
          //     CURLOPT_RETURNTRANSFER => true,
          //     CURLOPT_ENCODING => '',
          //     CURLOPT_MAXREDIRS => 10,
          //     CURLOPT_TIMEOUT => 0,
          //     CURLOPT_FOLLOWLOCATION => true,
          //     CURLOPT_SSL_VERIFYHOST => 0,
          //     CURLOPT_SSL_VERIFYPEER => 0,
          //     //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0),
          //     //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
          //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          //     CURLOPT_CUSTOMREQUEST => 'POST',
          //     CURLOPT_POSTFIELDS =>json_encode($datas),
          //     CURLOPT_HTTPHEADER => array(
          //       $api,
          //       'Content-Type: application/json'
          //     ),
          // ));
          // $response = curl_exec($curl);
          // curl_close($curl);
          // $res = json_decode($response);
          // $camp_id = $res->id;

          // // Step2: Programmer la campagne
          // $curl_send = curl_init();
          // curl_setopt_array($curl_send, array(
          //   CURLOPT_URL => 'https://api.letexto.com/v1/campaigns/'.$camp_id.'/schedules',
          //   CURLOPT_RETURNTRANSFER => true,
          //   CURLOPT_ENCODING => '',
          //   CURLOPT_MAXREDIRS => 10,
          //   CURLOPT_TIMEOUT => 0,
          //   CURLOPT_FOLLOWLOCATION => true,
          //   CURLOPT_SSL_VERIFYHOST => 0,
          //   CURLOPT_SSL_VERIFYPEER => 0,
          //   //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0),
          //   //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
          //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          //   CURLOPT_CUSTOMREQUEST => 'POST',
          //   CURLOPT_HTTPHEADER => array(
          //     $api
          //   ),
          // ));

          // //dd($curl_send);
          // $response_send = curl_exec($curl_send);
          // //dd($response_send);
          // curl_close($curl_send);
          // return $response_send;

          $prodUrl = "https://apis.letexto.com/";
          $token = "2701cf6c9ba227f87af1f0e5ededd3";
          $from = $sender;
          $to = $tel;
          $content = $msg;
          $dlrUrl = "https://mydomain.com:4444/dlr";
          $customData = "customData";
          $sendAt = date('j-m-Y h:i:s');

          $client = new Client();
          $url = $prodUrl.'V1/messages/send?from=' . urlencode($from) . '&to=' . $to . '&content=' . urlencode($content) . '&token=' . $token . '&dlrUrl=' . urlencode($dlrUrl) . '&dlrMethod=GET&customData=' . $customData . '&sendAt=' . urlencode($sendAt);
          $request = new Request('GET',$url);
          $res = $client->sendAsync($request)->wait();
          return $res->getBody();  

        }
        //Volume SMS
        function SMSVolume()
        {
          $key = '2701cf6c9ba227f87af1f0e5ededd3';
          $api = 'Authorization: Bearer '.$key."";
          //Créer la requête
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.letexto.com/v1/user-volume',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            //CURLOPT_POSTFIELDS =>json_encode($datas),
            CURLOPT_HTTPHEADER => array(
              $api,
              'Content-Type: application/json'
            ),
          ));
          //Execution de la requête
          $response = curl_exec($curl);
          curl_close($curl);
          //dd($api);
          //Retour json
          $res = json_decode($response);
          $text = $res->national;
          return $text;
        }


    /**
     * -----------------------------
     *  NOTIFICATION EMAIL && PUSH
     * -----------------------------
     */
      //Support email
      function support()
      {
        return "contact@lesmarmitesadidou.com";
      }
      //send push
      function sendPush($tokenFCM,$pushTitre,$pushMsg,$pushImg,$status)
      {
        $userdata = DB::table('clients')->where('tokenFCM','=',$tokenFCM)->first();
        if($userdata){
          $user = $userdata->iduser;
        }else{
          $userdata = DB::table('livreurs')->where('tokenFCM','=',$tokenFCM)->first();
          $user = $userdata->id_user;
        }
        
        Http::post('https://exp.host/--/api/v2/push/send', [
          'to' =>  $tokenFCM,
          'title' => $pushTitre,
          'body' => $pushMsg,
          'image' => env('APP_URL').$pushImg,
          'sound' => 'default',
        ])->throw();
        addUserPush($pushTitre,$pushMsg,'true',$status,$user);
        
      }
      //Get delivery days
      function getdeliverydays()
      {
        $days =  DB::table('deliverydays')->get();
        return $days;
      }
      //Send push Larafirebase
      function sendPushFirebase($tokenFCM,$pushTitre,$pushMsg)
      {
        echo "larafirebase";
      }
      //Send Email TEXT
      function SendEmail($to,$titre,$msg)
      {
          $from = support();
          $to = $to;
          $subject = $titre;
          $message = $msg;
          $headers = "From:" . $from;
          mail($to,$subject,$message, $headers);
      }

    /**
     * ------------
     *  OTP CODE
     * ------------
     */
       
        //Update OTP CODE
        function updateOTP($OTP)
        {
            DB::table('code_otp')->where('code','=',$OTP)
                                 ->update(['used'=>1]);
        }
        //Generate OTP CODE WITH TEL
        function generateOTP($tel)
        {
            do{
              $code = rand(0,99999);
              $OTP =  $code;
              $comd = DB::table('code_otp')->where('code','=',$OTP)->first();
            } while ($comd!=null);
            DB::table('code_otp')->insert(['code'=>$OTP,'tel'=>$tel]);
            return $OTP;
        }
        //Generate OTP WITH EMAIL
        
        //Check OTP code
        function checkOTP($OTP,$tel)
        {
          $OTP = DB::table('code_otp')->where('code','=',$OTP)->where('used','=',0)->where('tel','=',$tel)->first();
          return $OTP;
        }

    /**
     * ------------------------------
     *  SYSTEME DE PAIEMENT CINETPAY
     * ------------------------------
     */  
        //API key
        function apikey()
        {
          $api = '188375423961402d02d3b216.71475314';
          return $api;
        }

        //SITE ID
        function siteID()
        {
          $site_id = '295492';
          return $site_id;
        }

        //GUICHET DE PAIEMENT CINETPAY
        function Guichet($transaction_id,$montant,$description_trans,$client_name,$client_surname,$client_phone,$client_email,$notify_url,$return_url)
        {
          try {
            //Parameter
            $currency = 'XOF';
            $amount = $montant;
            $description = $description_trans;
            //Initiate variable for credit card
            $alternative_currency = 'XOF';
            $customer_email = $client_email;
            $customer_phone_number =$client_phone;
            $customer_address = 'Abidjan';
            $customer_city = 'Abidjan';
            $customer_country = 'CI';
            $customer_state = 'ABJ';
            $customer_zip_code ='225';
            //Transaction ID
            $id_transaction = $transaction_id;
            //apiKey
            $apikey = apikey();
            //siteId
            $site_id = siteID();
            //version
            $version = "V2";
            //notify url
            $notify_url = env('APP_URL').'api/'.$notify_url;
            //return url
            $return_url = env('APP_URL').'api/'.$return_url;
            //Channel list
            $channels = "ALL";
            //Create Guichet
            $formData = array(
              "transaction_id"=> $id_transaction,
              "amount"=> $amount,
              "currency"=> $currency,
              "customer_surname"=>$client_name,
              "customer_name"=>$client_surname,
              "description"=> $description,
              "notify_url" => $notify_url,
              "return_url" => $return_url,
              "channels" => $channels,
              //Pour afficher le paiement par carte de crédit
              "alternative_currency" => $alternative_currency,
              "customer_email" => $customer_email,
              "customer_phone_number" => $customer_phone_number,
              "customer_address" => $customer_address,
              "customer_city" => $customer_city,
              "customer_country" => strtoupper($customer_country),
              "customer_state" => $customer_state,
              "customer_zip_code" => $customer_zip_code
            );
            //Lancement de CinetPay
            $CinetPay = new CinetPay($site_id, $apikey, $version);
            $result = $CinetPay->generatePaymentLink($formData);
            //Traitement du resultat
            if ($result['code']=='201') {
              $url = $result["data"]["payment_url"];
              return response()->json(['msg'=>$url,'info'=>'1']);
            }

          } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['statusCode'=>500,
                                     'status' => false,
                                     'message' => $th->getMessage()
                                    ], 500);
          }
        }

        //GUICHET CINETPAY DE TRANSFERT D'ARGENT
        function GuichetPayOut($transfert_id,$phone,$amount,$name,$email,$type,$payment_method,$profil_id)
        {
          $CinetPayTransfert = new CinetPayService();
          $transfert = [
            'transfer_id'=> $transfert_id,
            'type'=> $type,
            'profil_id'=>$profil_id,
            'prefix'=> '225',
            'name'=> $name,
            'phone'=> $phone,
            'email'=> $email,
            'amount'=> $amount,
            "payment_method"=> $payment_method,
            'notify_url'=> 'notify_transfert',
            'country_iso'=> 'CI',
          ];
          return  $CinetPayTransfert->sendMoney($transfert);
        }

       //Enregistrer le paiement
       function savePay($transaction_id,$type_paiement,$montantpay,$description,$client_name,$client_surname,$client_phone,$client_email,$user_id)
       {
         $data = ['transaction_id' => $transaction_id,
                  'type_paiement'  => $type_paiement,
                  'montantpay'     => $montantpay,
                  'description'    => $description,
                  'client_name'    => $client_name,
                  'client_surname' => $client_surname,
                  'client_phone'   => $client_phone,
                  'client_email'   => $client_email,
                  'user_id'        => $user_id,
                  ];
          DB::table('paiement')->insert($data);
       }

       //Recuper un paiement
       function checkPayment($pay_id)
       {
          $pay = DB::table('paiement')->where('transaction_id','=',$pay_id)->first();
          return $pay;
       }

       
