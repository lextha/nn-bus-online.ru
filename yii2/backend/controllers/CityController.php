<?php

namespace backend\controllers;

use Yii;
use common\models\City;
use common\models\CitySearch;
use common\models\Route;
use common\models\Station;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\TimeWork;
/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','delete','delcache','hastime'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function cmp($a, $b)
        {
            if ($a->version==$b->version) {
                if ($a->views<$b->views) {
                    return 1;
                } else {
                    return -1;
                }
            } elseif ($a->version==1) {
                return -1;
            } elseif ($a->version==2) {
                return 1;
            } elseif ($a->version==4 AND $b->version==1) {
                return 1;
            } elseif ($a->version==3 AND ($b->version==1 || $b->version==4)) {
                return 1;
            } else {
                return -1;
            }
        }
    /**
     * Displays a single City model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        function version_edit($r){
            if($r->version==4) {
                $r->version=2;
            }elseif($r->version==2) {
                $r->version=4;
            }
            return $r;
        }

    /*   if ($id==42) {
           $routes= Route::find()->where('active=1 AND city_id='.$id)->all();
            foreach ($routes as $route) {
                $route->number=trim($route->number);
                $route->save();
            }
        }*/
       
        $routes= Route::find()->where('active=1 AND city_id='.$id)->all();
        $route_out=$routes;
        $route_itog=[];
        $route_itog_id=[];
        $many_dubl=[];
       
        foreach ($routes as $route) {
            if ($route->number!='' AND $route->number!='-') {                //echo ($route->number);
                if (!in_array($route->id, $route_itog_id)) {
                    $route_itog_id[]=$route->id;
                   
                    $r=Route::find()->where("number='".$route->number."' AND id<>'".$route->id."' AND type_direction<>3 AND city_id=".$id)->all();
                  // echo "<pre>"; var_dump($route,$r); die();
                    $route_i=[]; // массив со всеми аналогичными маршрутами
                   // $route=version_edit($route);
                    $route_i[]=$route; // проверяемый маршрут
                    if (count($r)>0) {
                        for ($i = 0; $i<count($r); $i++) {
                            $type_avtobus=[1,4];// автобусы - троллейбусы - маршрутки
                            if ((in_array($r[$i]->type_transport, $type_avtobus) AND in_array($route->type_transport, $type_avtobus)) OR $r[$i]->type_transport==$route->type_transport) { //типы транспортного средства аналогичны
                                similar_text($route->name, $r[$i]->name, $percent);
                               // var_dump($percent);
                             //   if ($percent>50) {
                                    $route_i[]=$r[$i];//version_edit($r[$i]);
                              //  }
                            }
                            $route_itog_id[]=$r[$i]->id;
                        }    
                        //var_dump($route_i); die();
                        if (count($route_i)>1) {
                            usort($route_i, array($this,'cmp'));
                            $route_itog[]= $route_i;
                           // echo "<pre>"; var_dump($route_itog); die();
                        }
                    }
                }
            }  
        }
        
        if (Yii::$app->request->get()) {
            $get=Yii::$app->request->get();
            if (isset($get['gd'])) {
                $city=City::findOne(['id'=>$id]);
                foreach ($route_itog as $r) {
                    if ($r[0]->id==$get['gd'] OR $get['gd']=='all') {
                        $i=0;
                        $redirect_to='';
                        foreach ($r as $rr) {
                            $redirect_from='';
                            if ($i==0) {
                                $redirect_to=$city->alias."/".$rr->alias;
                                if (isset($rr->routeredirect->url)) {
                                    $rr->active=1;
                                    $rr->save();
                                    $rr->delRedirect();
                                }
                            } else {
                                $redirect_from=$city->alias."/".$rr->alias;
                                $rr->addRedirect($redirect_to,$redirect_from);
                                $rr->deleteRoute();
                            }
                            $i++;
                        }
                       
                    }
                } 
                return $this->redirect(['view', 'id' => $city->id]);
            }
        }
        /*
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $city=City::findOne(['id'=>$id]);
            foreach ($route_itog as $r) {
                $i=0;
                $redirect_to='';
                foreach ($r as $rr) {
                    $redirect_from='';
                    if ($i==0) {
                        $redirect_to=$city->alias."/".$rr->alias;
                    } else {
                        $redirect_from=$city->alias."/".$rr->alias;
                        $rr->addRedirect($redirect_to,$redirect_from);
                        $rr->deleteRoute();
                    }
                    $i++;
                }
            }
        }*/

        return $this->render('view', [
            'model' => $this->findModel($id),
            'routes'=>$route_itog,
            'many_dubl'=>$many_dubl
        ]);
    }
    
    public function actionDeletedubl($id)
    {/*
        $routes= Route::find()->where('active=1 AND city_id='.$id)->all();
        $route_out=$routes;
        $route_itog=[];
        foreach ($routes as $route) {
            $r=Route::find()->where("number='".$route->number."' AND type_direction<>3 AND id<>".$route->id." AND city_id=".$id)->all();
            if (count($r)==1) {
                $type_avtobus=[1,2,4];// автобусы - троллейбусы - маршрутки
                if ((in_array($route->type_transport, $type_avtobus) AND in_array($r[0]->type_transport, $type_avtobus)) OR $route->type_transport==$r[0]->type_transport) { //типы транспортного средства аналогичны
                    
                }
               // $route_itog[]=[$route,$r];
            } else {
                // тут если дублей больше 1
            }
        }*/
    }

    /**
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new City();

        if ($model->load(Yii::$app->request->post())) {
            $q=$model->save();
            if ($q) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                var_dump($model->errors);
                var_dump($q); die();
            }
        } 

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing City model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing City model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        die("защита чтобы не удалить город 45674456");
        $routes= Route::find()->where(['city_id' => $id])->all();
        foreach ($routes as $r) {
            $r->deleteRoute();
            $r->delete();
        }
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    
    public function actionHastime($id) {
        $get=Yii::$app->request->get();
        if (isset($get['offset']) && isset($get['limit'])) {
            $routes= Route::find()->where('active=1 AND city_id='.$id)->offset($get['offset'])->limit($get['limit'])->all();
        } else {
            $routes= Route::find()->where('active=1 AND city_id='.$id)->all();
        }
        foreach ($routes as $r){
            $r->has_time=0;
            $r->save();
            $r->refresh();
        }
        foreach ($routes as $r){
            $q=false;
            if ($r->version==1 OR $r->version==4) {
                $stationsall=$r->getStationRouteAll($r->id);
                foreach ($stationsall as $keyr=>$st) {
                     if ($st) {
                        foreach ($st as $key=>$st1) {
                             $stationsall[$keyr][$key]['time_work']=TimeWork::getTimeWork($st1['id_station_rout']);
                             if ($stationsall[$keyr][$key]['time_work']) {
                                 foreach ($stationsall[$keyr][$key]['time_work'] as $key=>$er) {
                                     if ($key!='station_rout_id') {
                                         if (strlen($er)>10) {
                                            $r->has_time=1;
                                            $r->save(); $q=true; break;
                                         }
                                     }
                                 } 
                                 
                             }
                             if ($q) {  break; }
                        }

                     }
                      if ($q) {  break; }
                 } // echo $r->number." - ".$r->has_time."<br>";
            } else {
                
            }
            
            
        }
        return $this->redirect(['index']);
    }
                
        /**
     * Deletes an existing City model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelcache($id)
    {
        $cache = Yii::$app->cache;
     /*   $city= City::find()->where(['active' => 1])->all();
        foreach ($city as $r) {
            $cache_return3 = $cache->delete('city_'.$r->id);
            $cache_return4 = $cache->delete('citydacha_'.$r->id);  
        }*/
        $cache_return3 = $cache->delete('city_'.$id);
        $cache_return4 = $cache->delete('citydacha_'.$id);    
        $routes= Route::find()->where(['city_id' => $id])->all();
        foreach ($routes as $r) {
            for($i3=0;$i3<8;$i3++) {
                $cache_return1 = $cache->delete('route_'.$r->id."_".$i3."_0");
                $cache_return2 = $cache->delete('route_'.$r->id."_".$i3."_1");         
                $cache_return3 = $cache->delete('map'.$r->id);  
                $cache_return3 = $cache->delete('map1'.$r->id);  
                

            }
        }
        $station= Station::find()->where(['city_id' => $id])->all();
        foreach ($station as $r) {
                $cache_return1 = $cache->delete('map_st3_'.$r->id);
                for($i3=0;$i3<8;$i3++) {
                    $cache_return1 = $cache->delete('stationmany2_'.$r->id."_".$i3."_0");
                    $cache_return2 = $cache->delete('stationmany2_'.$r->id."_".$i3."_1"); 
                    $cache_return2 = $cache->delete('station_'.$r->id."_".$i3."_0"); 
                    $cache_return2 = $cache->delete('station_'.$r->id."_".$i3."_1"); 
                }
                
        }
        return $this->redirect(['index']);
    }
    

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
