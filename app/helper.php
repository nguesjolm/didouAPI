<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;

/*----------------------
  INTEGRATION CINETPAY
-----------------------*/
use App\Cinetpay\Cinetpay;

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
              $plattsAll = DB::table('plats')->where('statut_plat','=',"1")->get();
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
                          'livreurs'        => $comd->livreurs,
                          'statut_livreurs' => $comd->statut_livreurs,
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
                                          'data'=> '',
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
          //Create supplement
          function CreateSupplement($nom,$image,$status)
          {
              //Data
              $data = ['nom'=>ucfirst($nom),
                        'image'=>$image,
                        'status'=>$status,
                      ];
              //Check existing
              $res = $res = DB::table('supplements')->where('nom','=',$nom)->first();
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
                  'image' => $sup->image,
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
                                 'images'=> $galerie->images
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
                      'photo'         => $catg->photo,
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

        


        /*------------------
        GESTION DES RECETTES
        --------------------*/

            //Create recette
            function createRecette($nomrecette,$description,$image,$prix,$categorie,$recommanded)
            {
              $data = ['nomrecette'=>$nomrecette,
                       'description'=>$description,
                       'image'=>$image,
                       'prix'=>$prix,
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
                    'image'        => $recette->image,
                    'prix'         => $recette->prix,
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
                    'image'        => $recettedata->image,
                    'prix'         => $recettedata->prix,
                    'disponible'   => $recettedata->disponible,
                    'recommanded'  => $recettedata->recommanded,
                    'categorie'    => $recettedata->categorie_idcategorie ,
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
            function updateRecette($nomrecette,$description,$categorie,$recommanded,$disponible,$prix,$image,$recetteid)
            {
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
                DB::table('plats_galeries')->where('recettes','=',$recetteid)
                                           ->update(['images'=>$image]);
              }
            }

            //Update recette supplement
            function updateRecetteSupplement($recetteid,$supplement)
            {
              if ($supplement!='') 
              {
                DB::table('plats_supplements')->where('recettes','=',$recetteid)
                                              ->update(['supplements'=>$supplement]);
              }
            }

            //Delete recette
            function deleteRecette($recetteid)
            {
               DB::table('plats')->where('idplats','=',$recetteid)->delete();
               return response()->json(['statusCode'=>'200',
                                        'status'=>'true',
                                        'message'=>"recette supprimée avec succès",
                                        'data'=> '',
                                        'error'=> '',
                                      ]);
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
                 $clientall = DB::table('client')->get();
                 $nb = count($clientall);
                  if ($nb!=0)
                  {
                    $data  = [];
                    foreach ($clientall as $client) 
                    {
                      $data[] = [
                        'id'     => $client->idclient,
                        'nom'    => $client->nom,
                        'email'  => $client->email,
                        'tel'    => $client->tel,
                        'status' => $client->status,
                        'parain' => $client->parain,
                        'datecreat' => $client->datecreat
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
                  // if ($clientdata) {
                  //   $data[] = [
                  //     'status'        => $clientdata->status,
                  //     'parain'        => $clientdata->parain,
                  //     'iduser'        => $clientdata->iduser
                  //   ];
                  //   return response()->json(['statusCode'=>'200',
                  //                             'status'=>'true',
                  //                             'message'=>'Afficher un compte client',
                  //                             'data'=> $data,
                  //                             'error'=> '',
                  //                         ]);
                  // } else {
                  //   return response()->json(['statusCode'=>'404',
                  //                             'status'=>'false',
                  //                             'message'=>'aucun compte client trouvé',
                  //                             'data'=> '',
                  //                             'error'=> '',
                  //                           ]);
                  // }
                 
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
                DB::table('client')->where('idclient','=',$clientsid)->update(["status"=>2]);;
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
              GESTION DES livreursS
            ----------------------*/
            
              //Create livreurs
              function createlivreurs($nom,$tel,$email,$local)
              {
                $data = ['nom'=>$nom,
                         'tel'=>$tel,
                         'email'=>$email,
                         'local'=>$local
                      ];
                //Check
                $restel = DB::table('livreurs')->where('tel', $tel)->first();
                $resmail = DB::table('livreurs')->where('email', $email)->first();
                //result
                if($restel == null && $resmail == null)
                {
                   DB::table('livreurs')->insert($data);
                   return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>'livreurs ajouté avec succès',
                                            'data'=> '',
                                            'error'=> '',
                                           ]);
                } 
                else
                {
                  return response()->json(['statusCode'=>'422',
                                           'status'=>'false',
                                           'message'=>'Ce livreurs existe déjà',
                                           'data'=> '',
                                           'error'=> '',
                                          ]);
                }   
              
              }

              //Get all livreurss
              function getAlllivreurs()
              {
                  $livreursall = DB::table('livreurs')->get();
                  $nb = count($livreursall);
                  if ($nb!=0)
                  {
                     $data  = [];
                     foreach ($livreursall as $livreurs) 
                     {
                        $data  [] = [
                          'id'     => $livreurs->idlivreurs,
                          'nom'    => $livreurs->nom,
                          'email'  => $livreurs->email,
                          'tel'    => $livreurs->tel,
                          'status' => $livreurs->status,
                          'local'  => $livreurs->local,
                          'solde'  => $livreurs->solde,
                          'photo'  => $livreurs->photo
                        ];
                     }
                     
                     return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Liste des livreurss',
                                              'data'=> $data,
                                              'error'=> '',
                                            ]);
                   
                  }       
                  else
                  {
                      return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'Aucun livreurs trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
                  
              }

              //Get single livreurss
              function getSinglelivreurs($livreurs)
              {
                  $livreursdata = DB::table('livreurs')->where('idlivreurs','=',$livreurs)->first();
                  if ($livreursdata)
                  {
                    $data[] = [
                      'id'     => $livreursdata->idlivreurs,
                      'id_user' => $livreursdata->id_user,
                      'local'  => $livreursdata->local,
                      'solde'  => $livreursdata->solde
                    ];
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'Afficher un livreurs',
                                              'data'=> $data,
                                              'error'=> '',
                                          ]);
                  }
                  else 
                  {
                    return response()->json(['statusCode'=>'404',
                                              'status'=>'false',
                                              'message'=>'aucun livreurs trouvé',
                                              'data'=> '',
                                              'error'=> '',
                                          ]);
                  }
                 
              }

              //Update livreurs
              function  updatlivreurs($nom,$tel,$email,$local,$status,$photo,$id)
              {
                  $data = ['nom'=>$nom,
                           'tel'=>$tel,
                           'email'=>$email,
                           'local'=>$local,
                           'status'=>$status,
                           'photo'=>$photo
                          ];
                  if ($nom!='') {
                    DB::table('livreurs')->where('idlivreurs','=',$id)
                                        ->update(['nom'=>$nom,]);
                  }

                  if ($tel!='') {
                    DB::table('livreurs')->where('idlivreurs','=',$id)
                                        ->update(['tel'=>$tel,]);
                  }

                  if ($email!='') {
                    DB::table('livreurs')->where('idlivreurs','=',$id)
                                        ->update(['email'=>$email,]);
                  }

                  if ($local!='') {
                    DB::table('livreurs')->where('idlivreurs','=',$id)
                                        ->update(['local'=>$local,]);
                  }

                  if ($status!='') {
                    DB::table('livreurs')->where('idlivreurs','=',$id)
                                        ->update(['status'=>$status,]);
                  }

                  if ($photo!='') {
                    DB::table('livreurs')->where('idlivreurs','=',$id)
                                        ->update(['photo'=>$photo]);
                  }
                 

                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"Mise à jour effectuée avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //Delete livreurs
              function deletelivreurs($livreurs)
              {
                  DB::table('livreurs')->where('idlivreurs','=',$livreurs)->delete();
                  return response()->json(['statusCode'=>'200',
                                            'status'=>'true',
                                            'message'=>"livreurs supprimé avec succès",
                                            'data'=> '',
                                            'error'=> '',
                                          ]);
              }

              //Enregistrer une livraison
              function livreursLivraison($orderid,$livreurs)
              {
                  $data = ['livreurs'=>$livreurs];
                  DB::table('commandes')->where('idcommandes','=',$orderid)
                                               ->update($data);
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Livraison de la commande attribuée avec succès",
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
              }

              //Livraison des livreurss
              function orderOflivreurs($livreurs)
              {
                 $livraisondata = DB::table('commandes')->where('livreurs','=',$livreurs)->get();
                 $nb = count($livraisondata);
                 if ($nb!=0) 
                 {
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'recupérer les commande du livreurs',
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

              //Liste des commandes en fonction du staut_livreurs
              function orderlivreursStat($livreursid,$status)
              {
                  $orderdata = DB::table('commandes')->where('livreurs','=',$livreursid)
                                                     ->where('statut_livreurs','=',$status)
                                                     ->get();
                  $nb = count($orderdata);
                  if ($nb!=0) 
                  {
                    return response()->json(['statusCode'=>'200',
                                              'status'=>'true',
                                              'message'=>'commandes du livreurs en fonction du statut_livreurs',
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

              //Créditer le solde d'un livreurs
              function crediterSoldeLiv($livreursid)
              {
                 $livreursdata = DB::table('livreurs')->where('idlivreurs','=',$livreursid)->first();

                 $solde = $livreursdata->solde+setting()->gainlivreurs;
                 $data = ['solde'=>$solde];
                 DB::table('livreurs')->where('idlivreurs','=',$livreursid)
                                     ->update($data);
                 #save transaction
                 DB::table('livreurs_pay')->insert(['montant'=>setting()->gainlivreurs,
                                                   'date'=>date('j F Y, H:i'),
                                                   'livreurs_idlivreurs'=>$livreursid,
                                                   "type"=>"dépôt",
                                                  ]);

                 return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Votre solde a été crédit de ".setting()->gainlivreurs." fcfa, vous avez ".$solde." Fcfa sur votre solde",
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

              //Save commande details
              function savecomprod($platId,$qte,$amount,$numComd,$client)
              {
                $data = ['client_idclient'=>$client,
                        'plats_idplats'=>$platId,
                        'qte'=>$qte,
                        'numComd'=>$numComd,
                        'montant'=>$amount
                      ];
                DB::table('panier')->insert($data);
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
                        $data  [] = [
                          'id'             => $comd->idcommandes,
                          'numComd'        => $comd->numComd,
                          'livreurs'        => $comd->livreurs,
                          'statut_livreurs' => $comd->statut_livreurs,
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
                    $data  [] = [
                      'id'             => $orderdata->idcommandes,
                      'numComd'        => $orderdata->numComd,
                      'livreurs'        => $orderdata->livreurs,
                      'statut_livreurs' => $orderdata->statut_livreurs,
                      'statut_client'  => $orderdata->statut_client,
                      'ambassadeur_code'    => $orderdata->ambassadeur_code,
                      'code_gps'       => $orderdata->code_gps,
                      'idclients'      => $orderdata->idclients,
                      'zone_idzone'    => $orderdata->zone_idzone,
                      'qte'            => $orderdata->qte,
                      'montant'        => $orderdata->montant,
                      'datecomd'       => $orderdata->dateComd,
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
                                    'plats_idplats'   => $order->plats_idplats,
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

              //Mise à jour du statu de la commande :: statut_livreurs
              function UpdOrderstatuslivreurs($orderid,$statutlivreurs)
              {
                 $data = ['statut_livreurs'=>$statutlivreurs];
                 DB::table('commandes')->where('idcommandes','=',$orderid)
                                       ->update($data);
                 return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Statut_livreurs mise à jour avec succès",
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
                    //Vérifier si le client a déjà fait une commande minimum qui équivaut à la valeur paramétré
                    $commande = DB::table('commandes')->where('idclients','=',$clientid)
                                                      ->where('statut_client','=','sucess') 
                                                      ->where('montant','=',$setting->creditDidou) 
                                                      ->first();
                    if ($commande) {
                        $data = ['client_idclient'=>$clientid,
                                 'creditDidou'=>generatecredit(),
                                 'montant'=>setting()->creditDidou,
                                 'dateCredit'=>date('d-m-Y'),
                                ];
                        DB::table('credit_didou')->insert($data);
                        return response()->json(['statusCode'=>'200',
                                       'status'=>'true',
                                       'message'=>"crédit accordé avec succès. Vous avez ".setting()->creditDidou." fcfa",
                                       'data'=> $data,
                                       'error'=> '',
                                     ]);
                    }else {
                      return response()->json(['statusCode'=>'423',
                                                'status'=>'false',
                                                'message'=>"crédit refusé. Vous devez effectuée une première commande de ".$setting->creditDidou." fcfa avant d'être éligible",
                                                'data'=> $data,
                                                'error'=> '',
                                              ]);   
                    }
                                 
                  }
                  else
                  {
                    return response()->json(['statusCode'=>'423',
                                              'status'=>'false',
                                              'message'=>"crédit refusé. Vous devez ".$data->montant." fcfa",
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
                        $data  [] = ['id'              =>$credit->idcredit,
                                      'client_idclient'=>$credit->client_idclient,
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
                                              'data'=> '',
                                              'error'=> '',
                                            ]);
                  }
                  
              }
              //Recupérer les crédit d'un client
              function getAllUSerCredit($clientID)
              {
                $creditdata = DB::table('credit_didou')->where('client_idclient','=',$clientID)->get();
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
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>"Crédit Didou remboursé avec succès",
                                          'data'=> $creditdata,
                                          'error'=> '',
                                         ]);
                }
                else
                {
                  return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Code crédit  invalide',
                                          'data'=> '',
                                          'error'=> '',
                                        ]);
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
                      $data  [] = [
                        'id'                    => $amb->idambassadeur ,
                        'code'                  => $amb->code,
                        'client_idclient'       => $amb->client_idclient,
                        'statut_ambassadeur'    => $amb->statut_ambassadeur,
                        'solde'                 => $amb->solde
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
                    'solde'              => $ambData->solde
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
                                            'data'=> '',
                                            'error'=> '',
                                         ]);
                }
                
              }

              //Crediter le solde
              function creditersoldAmbasad($amb)
              {
                 $ambData = DB::table('ambassadeur')->where('code','=',$amb)->first();
                 $solde = $ambData->solde+setting()->gainambassadeur;
                 $data = ['solde'=>$solde];
                 DB::table('ambassadeur')->where('code','=',$amb)
                                         ->update($data);
                 
              }

              //Débiter un solde
              function debiterSoldAmbasad($amb,$montant)
              {
                $ambData = DB::table('ambassadeur')->where('code','=',$amb)->first();
             
                if ($ambData) 
                {
                  if ($montant <= $ambData->solde)
                  {
                    $solde = $ambData->solde-$montant;
                    DB::table('ambassadeur')->where('code','=',$amb)
                                            ->update(['solde'=>$solde]);
                    
                    $data = ['montant'=>$montant,
                             'date'=>date('d-m-Y'),
                             'type'=>"Paiemnet Mobile money",
                             'client_idclient'=>$ambData->client_idclient,
                            ];
                    DB::table('ambassadeur_pay')->insert($data);
                    return response()->json(['img'=>'200',
                                             'titre'=>'true',
                                             'message'=>"Votre solde a été débité de ".$montant." Fcfa avec succès, il vous reste ".$solde." Fcfa",
                                             'data'=> $data,
                                             'error'=> '',
                                            ]);                        


                  }
                  else
                  {
                    return response()->json(['statusCode'=>'404',
                                             'status'=>'false',
                                             'message'=>'Le montant dépasse le solde de votre compte ambassadeur',
                                             'data'=> '',
                                             'error'=> '',
                                            ]);
                  }
                }else{
                  return response()->json(['statusCode'=>'404',
                                           'status'=>'false',
                                           'message'=>'Code ambassadeur invalide',
                                           'data'=> '',
                                           'error'=> '',
                                          ]);
                }
               
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
                                            'message'=>'recupérer les commande du livreurs',
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
                                 'img'=>$push->img,
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
                  $userdata = DB::table('users')->get();
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
              function creatzone($zone)
              {
                 $data = ['nom'=>ucfirst($zone)];  
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
                                  'statut'=>$zone->statut
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
              function updatezone($zone,$zoneid)
              {
                DB::table('zone')->where('idzone','=',$zoneid)
                                 ->update(['nom'=>ucfirst($zone)]);
                
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
              function getOrderzone($zoneid)
              {

                $orderdata = DB::table('commandes')->where('statut_client','=',"success")
                                                   ->get();

                $orderdatabyzone = DB::table('commandes')->where('zone_idzone','=',$zoneid)
                                                  ->where('statut_client','=',"success")
                                                  ->get();
                $nborder = count($orderdata);
                $nbzone = count($orderdatabyzone);
                if ($nbzone!=0) {
                  $zonepourcenage = (100*$nbzone)/$nborder;
                  return response()->json(['statusCode'=>'200',
                                          'status'=>'true',
                                          'message'=>'commande de la zone',
                                          'data'=> $zonepourcenage.' %',
                                          'error'=> '',
                                        ]);
                } else {
                  return response()->json(['statusCode'=>'404',
                                          'status'=>'false',
                                          'message'=>'Aucune commande trouvée',
                                          'data'=> '0 %',
                                          'error'=> '',
                                        ]);
                }
                
              }


            
            /**
               * ----------------------
               * FONCTION SYSTEM GLOBAL
               * -----------------------
            */
                //recuperer les paramètres didou
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
                function settingDidou($gainlivreurs,$gainambassadeur,$promoComd,$creditDidou)
                {
                    $data = ['gainlivreurs'     =>$gainlivreurs,
                             'gainambassadeur' =>$gainambassadeur, 
                             'promoComd'       =>$promoComd,
                             'creditDidou'     =>$creditDidou
                            ];
                    if ($gainlivreurs!='') 
                    {
                      DB::table('settings')->where('idsettings','=',1)
                                           ->update(['gainlivreurs'=>$gainlivreurs]);
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
      //Save transactions
      function saveTransaction($clientid,$ambassadeur,$credit_didou,$montant,$qte,$gps,$zoneid,$dateComd,$statutClient,$data_recettes,$id_transaction)
      {
        $data = ['clientid'          => $clientid,
                 'ambassadeur_code'  => $ambassadeur,
                 'credit_didou'      => $credit_didou,
                 'montant'           => $montant,
                 'qte'               => $qte,
                 'gps'               => $gps,
                 'zoneid'            => $zoneid,
                 'dateComd'          => $dateComd,
                 'statutClient'      => $statutClient,
                 'data_recettes'     => $data_recettes,
                 'id_transaction'    => $id_transaction,
                ];
        DB::table('transaction_commandes')->insert($data);
      }

      //Save commande
      function saveCommand($clientid,$ambassadeur,$credit_didou,$montant,$qte,$gps,$zoneid,$dateComd,$statutClient,$numComd,$precision_plats)
      {
        $data = ['idclients'         => $clientid,
                 'ambassadeur_code'  => $ambassadeur,
                 'code_credit'       => $credit_didou,
                 'montant'           => $montant,
                 'precision_plats'   => $precision_plats,
                 'qte'               => $qte,
                 'code_gps'          => $gps,
                 'zone_idzone'       => $zoneid,
                 'dateComd'          => $dateComd,
                 'statut_client'     => $statutClient,
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
                                  ->update(['statut_client'=>$statut_client,'statut_livreurs'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande en cours de livraison";

          }elseif ($statut_client=="success"){
            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande livrée avec succès";

          }elseif ($statut_client=="fail"){
            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande annulée avec succès";

          }elseif ($statut_client=="init") {

            DB::table('commandes')->where('idcommandes','=',$idcommandes)
                                  ->update(['statut_client'=>$statut_client]);
            $commande = DB::table('commandes')->where('idcommandes','=',$idcommandes)->first();
            $message = "Commande reçu avec succès, vous serez livrée dans 10 minutes";

          }else{
            
            $message = "Status de la commande incorrecte";
            return response()->json(['statusCode' =>'401',
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
          return response()->json(['statusCode'=>401,
                                   'status' => false,
                                   'message' => "Cette commande n'existe pas",
                                   'error' => ''
                                 ], 401);
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
          return response()->json(['statusCode'=>401,
                                   'status' => false,
                                   'message' =>  $state,
                                   'error' => ''
                                 ], 401);
        }
      }

      //Get client all command
      function getClientComdAll($client_id)
      {
        $commande = DB::table('commandes')->where('idclients','=',$client_id)->get();
        $nb = count($commande);
        if ($nb!=0) {
          return response()->json(['statusCode'=>'200',
                                    'status'=>'true',
                                    'message'=> "Historique des commandes du client",
                                    'data'=> $commande,
                                    'error'=> '',
                                  ]);
        }else{
          return response()->json(['statusCode'=>401,
                                   'status'    => false,
                                   'message'   => "Aucune commande trouvée pour ce client",
                                   'error'     => ''
                                  ], 401);
        }
      }

    
    /**
    * ------------------------
    * NOTIFICATION PUSH CLIENT
    * ------------------------
    */
      //Add Push
      function addUserPush($titre,$message,$state,$id_user)
      {
        $data = ['titre'    => $titre,
                 'message' => $message,
                 'state'   => $state,
                 'date_add'=> date('d-m-Y'),
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
        $nb = count($push);
        if ($nb!=0) {
          return response()->json(['statusCode'=>'200',
                                    'status'=>'true',
                                    'message'=> "Notification push du client",
                                    'data'=> $push,
                                    'error'=> '',
                                  ]);
        }else{
          return response()->json(['statusCode'=>401,
                                   'status'    => false,
                                   'message'   => "Aucune notification push trouvée pour ce client",
                                   'error'     => ''
                                  ], 401);
        }
      }
      //Delete all push
      function deleteUserPush($id)
      {
         $res =  DB::table('notifications')->where('id_push','=',$id)->delete();
         if ($res==0) 
         {
           return response()->json(['statusCode'=>401,
                                    'status'    => false,
                                    'message'   => "Aucune notification push trouvée pour ce compte",
                                    'error'     => ''
                                ], 401);
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
 *          API livreurs
 *  ----------------------------
 */
    
    /**
     * -----------------
     * COMMANDE livreurs
     * -----------------
     */
       //Get all livreurs commandes
       function get_livreurs_comd_all($livreurs_id)
       {
         $comd_All = DB::table('commandes')->where('livreurs','=',$livreurs_id)->orderBy('idcommandes','desc')->get();
         $nb = count($comd_All);
         if ($nb!=0) {
            return response()->json(['statusCode'=>'200',
                                      'status'=>'true',
                                      'message'=> 'commandes du livreurs',
                                      'data'=> $comd_All,
                                      'error'=> '',
                                    ]);
         }else{
            return response()->json(['statusCode'=>401,
                                     'status' => false,
                                     'message' =>  "Aucune commande trouvée pour ce livreurs",
                                     'error' => ''
                                    ], 401);
         }
         
       }

       //Get livreurs commande by status
       function get_livreurs_comd_status($livreurs_id,$commande_status)
       {
          $comd_All = DB::table('commandes')->where('livreurs','=',$livreurs_id)->where('statut_livreurs','=',$commande_status)->get();
          $nb = count($comd_All);
          if ($nb!=0) {
              return response()->json(['statusCode'=>'200',
                                       'status'=>'true',
                                       'message'=> 'commandes du livreurs',
                                       'data'=> $comd_All,
                                       'error'=> '',
                                      ]);
          }else{
           return response()->json(['statusCode'=>401,
                                    'status' => false,
                                    'message' =>  "Aucune commande trouvée pour ce livreurs",
                                    'error' => ''
                                   ], 401);
          }
       }

       //Get livreurs commande by today
       function get_livreurs_today_command($livreurs_id,$today_date)
       {
        $comd_All = DB::table('commandes')->where('livreurs','=',$livreurs_id)->where('dateComd','=',$today_date)->get();
        $nb = count($comd_All);
        if ($nb!=0) {
            return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=> 'commandes du livreurs',
                                     'data'=> $comd_All,
                                     'error'=> '',
                                    ]);
        }else{
         return response()->json(['statusCode'=>401,
                                  'status' => false,
                                  'message' =>  "Aucune commande trouvée pour ce livreurs",
                                  'error' => ''
                                 ], 401);
        }
       }
      
    /**
     * ------------------
     *   SOLDE livreurs
     * ------------------
     */
       //Debiter le solde
        function debiter_solde_livreurs($livreursid,$montant)
        {
            $livreursdata = DB::table('livreurs')->where('idlivreurs','=',$livreursid)->first();
            if ($livreursdata=="") {
              return response()->json(['statusCode'=>'401',
                                         'status'=>'false',
                                         'message'=>"Ce livreurs n'existe pas",
                                         'data'=> $livreursdata,
                                         'error'=> "",
                                        ]);
            }
            if($livreursdata->solde > $montant){
             $solde = $livreursdata->solde-$montant;
             $data = ['solde'=>$solde];
             DB::table('livreurs')->where('idlivreurs','=',$livreursid)
                                 ->update($data);
             #save transaction
             DB::table('livreurs_pay')->insert(['montant'=>$montant,
                                               'date'=>date('j F Y, H:i'),
                                               'livreurs_idlivreurs'=>$livreursid,
                                               "type"=>"retrait",
                                              ]);

             #valeur retournée
             return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=>"Votre solde a été débité de ".$montant." fcfa il vous reste ".$solde." Fcfa",
                                     'data'=> $livreursdata,
                                     'error'=> '',
                                   ]);
            }else{
              return response()->json(['statusCode'=>'401',
                                        'status'=>false,
                                        'message'=>"Votre solde est insuffisant",
                                        'data'=> $livreursdata,
                                        'error'=> '',
                                      ]);
            }
          
        }
       //Get all transaction
       function get_livreurs_all_transactions($id_livreurs)
       {
          $livreursdata = DB::table('livreurs_pay')->where('livreurs_idlivreurs','=',$id_livreurs)->orderBy('idlivreurs_pay','desc')->get();
          $nb = count($livreursdata);
          if ($nb==0) {
            return response()->json(['statusCode'=>'401',
                                       'status'=>'false',
                                       'message'=>"Ce livreurs n'existe pas",
                                       'data'=> $livreursdata,
                                       'error'=> "",
                                      ]);
          }else{
            return response()->json(['statusCode'=>'200',
                                     'status'=>'true',
                                     'message'=>"Historique des transactions",
                                     'data'=> $livreursdata,
                                     'error'=> '',
                                   ]);
          }
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
        //Send SMS
        function Sendsms($msg,$tel,$sender)
        {
          // Filtrer le messages
          $nvMsg = str_replace('à','a', $msg);
          $nvMsg = str_replace('á','a', $nvMsg);
          $nvMsg = str_replace('â','a', $nvMsg);
          $nvMsg = str_replace('ç','c', $nvMsg);
          $nvMsg = str_replace('è','e', $nvMsg);
          $nvMsg = str_replace('é','e', $nvMsg);
          $nvMsg = str_replace('ê','e', $nvMsg);
          $nvMsg = str_replace('ë','e', $nvMsg);
          $nvMsg = str_replace('ù','u', $nvMsg);
          $nvMsg = str_replace('ù','u', $nvMsg);
          $nvMsg = str_replace('ü','u', $nvMsg);
          $nvMsg = str_replace('û','u', $nvMsg);
          $nvMsg = str_replace('ô','o', $nvMsg);
          $nvMsg = str_replace('î','i', $nvMsg);
          $key = '2701cf6c9ba227f87af1f0e5ededd3';
          $api = 'Authorization: Bearer '.$key."";
          // Step 1: Créer la campagne
          $curl = curl_init();
          $datas= [
            'step' => NULL,
            'sender' => $sender,
            'name' => 'SMS',
            'campaignType' => 'SIMPLE',
            'recipientSource' => 'CUSTOM',
            'groupId' => NULL,
            'filename' => NULL,
            'saveAsModel' => false,
            'destination' => 'NAT_INTER',
            'message' => $msg,
            'emailText' => NULL,
            'recipients' =>
            [
              [
                'phone' => $tel,
              ],
            ],
            'sendAt' => [],
            'dlrUrl' => NULL,
            'responseUrl' => NULL,
          ];
          curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.letexto.com/v1/campaigns',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => 0,
              //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0),
              //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>json_encode($datas),
              CURLOPT_HTTPHEADER => array(
                $api,
                'Content-Type: application/json'
              ),
          ));
          $response = curl_exec($curl);
          curl_close($curl);
          $res = json_decode($response);
          $camp_id = $res->id;

          // Step2: Programmer la campagne
          $curl_send = curl_init();
          curl_setopt_array($curl_send, array(
            CURLOPT_URL => 'https://api.letexto.com/v1/campaigns/'.$camp_id.'/schedules',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0),
            //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
              $api
            ),
          ));

          //dd($curl_send);
          $response_send = curl_exec($curl_send);
          //dd($response_send);
          curl_close($curl_send);
          return $response_send;
        }


    /**
     * -------------------
     *  NOTIFICATION EMAIL
     * -------------------
     */
      //Support email
      function support()
      {
        return "didou@gmail.com";
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
        //Generate OTP CODE
        function generateOTP()
        {
            do{
              $code = rand(0,99999);
              $OTP =  "D-".$code;
              $comd = DB::table('code_otp')->where('code','=',$OTP)->first();
            } while ($comd!=null);
            DB::table('code_otp')->insert(['code'=>$OTP]);
            return $OTP;
        }
        
        //Check OTP code
        function checkOTP($OTP)
        {
          $OTP = DB::table('code_otp')->where('code','=',$OTP)->where('used','=',0)->first();
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
            $notify_url = env('APP_URL').'/'.$notify_url;
            //return url
            $return_url = env('APP_URL').'/'.$return_url;
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

       //Enregistrer le paiement
       function savePay($transaction_id,$type_paiement,$montant,$description,$client_name,$client_surname,$client_phone,$client_email)
       {
         $data = ['transaction_id' => $transaction_id,
                  'type_paiement'  => $type_paiement,
                  'montant'        => $montant,
                  'description'    => $description,
                  'client_name'    => $client_name,
                  'client_surname' => $client_surname,
                  'client_phone'   => $client_phone,
                  'client_email'   => $client_email
                  ];
          DB::table('paiement')->insert($data);
       }

       //Recuper un paiement
       function checkPayment($pay_id)
       {
          $pay = DB::table('paiement')->where('transaction_id','=',$pay_id)->first();
          return $pay;
       }

       
