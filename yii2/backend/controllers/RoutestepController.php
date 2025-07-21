<?php

namespace backend\controllers;

use Yii;
use app\models\RouteStep;
use app\models\RouteStepSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Route;
use yii\filters\AccessControl;
use common\models\TimeWork;
use common\models\MapRout;
use common\models\Station;
use console\helpers\FuncHelper;
/**
 * RouteStepController implements the CRUD actions for RouteStep model.
 */
class RoutestepController extends Controller
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
                        'actions' => ['index','view','create','update','delete','gettypetime','updateredirect'],
                        'allow' => true,
                        'roles' => ['admin','editor','manager'],
                    ],
                    [
                        'actions' => ['pay'],
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
     * Lists all RouteStep models.
     * @return mixed
     */
    public function actionIndex()
    {
     //   die('dfsdfsfsdf');
       
        
       // var_dump(Yii::$app->user->can('admin'));
        if (Yii::$app->user->can('admin')) {
            $user_id=0;
        } else {
            $user_id=Yii::$app->user->id;
        }
        
        $params=Yii::$app->request->queryParams;
        if (count($params) <= 1) {
            $params = Yii::$app->session['params_step'];
            if(isset(Yii::$app->session['params_step']['page'])) {
                    $_GET['page'] = Yii::$app->session['params_step']['page'];
            }
        } else {
                Yii::$app->session['params_step'] = $params;
        }
        $searchModel = new RouteStepSearch();
        $dataProvider = $searchModel->search($params,['userID'=>$user_id]);//, ['isAuthor'=>Yii::$app->user->id]);
//var_dump(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RouteStep model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionDelete($id=0)
    {
        if ($id!=0) {
            Yii::$app->db->createCommand('DELETE FROM time_work_step WHERE route_step_id='.$id)->execute();
            Yii::$app->db->createCommand('DELETE FROM route_step WHERE id='.$id)->execute();
        }
        return $this->redirect(['routestep/index']);
    }
    /**
     * Creates a new RouteStep model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($version=3)
    {
       
        $model = new Route();
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
        }
        
        /********************/
        //$model_name='Route';  
        $route_step=new RouteStep();
       // $model->id=0;
       // $post['Route']=['version'=>3];
     //   $route_step->saveStep_2_3($model,$post,$model_name); 
        $route_step->status=0;
        $route_step->name='none';
        $route_step->route_id=0;
        $route_step->user_id=Yii::$app->user->id;
        $route_step->number='0';
        $q=$route_step->save();
       // var_dump($route_step->errors);
        $route_step->refresh();
       // var_dump($route_step); die();
        return $this->redirect(['routestep/update?edit=1&id='.$route_step->id]); 
        
        /********************/
        if (isset($post) AND $model->load($post)) {
          //   die('3234');
           // var_dump($post);
            $model_name='Route';  
            $route_step=new RouteStep();
            $model->id=0;
            if ($version>1) {
                $route_step->saveStep_2_3($model,$post,$model_name); 
            } else {
                $route_step->saveStep_1($model,$post,$model_name); 
            }
            $route_step->status=0;
            $q=$route_step->save();
            if (isset($post['savegood']) AND $post['savegood']=='1' AND (Yii::$app->user->can('admin'))) {
                //https://goonbus.ru/admin/routestep/update?edit=1&id=5734
                 return $this->redirect(['routestep/update?id='.$route->id]); 
            }
         /*   var_dump($q);
            echo "<pre>";var_dump($route_step->getErrors()); */
            return $this->redirect(['routestep/index']);
        }
        $edit=0;
        $model->version=$version;
        if ($version==3) {
            return $this->render('update2', [
                    'model' => $model,
                    'edit' => $edit,
                    'create'=>1
                ]);
        } else {
            return $this->render('update1', [
                    'model' => $model,
                    'edit' => $edit,
                    'create'=>1
                ]);
        }
    }

     public function actionUpdateredirect()
    {
         if (Yii::$app->request->get()) {
            $get=Yii::$app->request->get();
        }
        if (isset($get['redirectfrom'])) {
            $route = Route::findone($get['redirectfrom']);
            $route_to = Route::findone($get['redirectto']);
            $route_step=new RouteStep();
           // $route_step->load($route,''); 
            $data = $route->attributes;
           $route_step->setAttributes($data);
           //  echo "<pre>";var_dump($route_step);
           //  die();
         //    echo "<pre>";var_dump($route);
            $route_step->status=0;
            $route_step->active=0;
            $route_step->redirect=$route_to->alias;
            $route_step->user_id=Yii::$app->user->identity->id;
            $route_step->marshruts_value='';
            $route_step->route_id=$route->id;
            //$route_step->version==3;
            $route_step->save();
            echo "<pre>";var_dump($route_step->getErrors());
            return true;
        }
    }
    private function createMaproute($json,$route) {
        
        $city_id=$route->city_id;
        $array=json_decode($json);
        $array_station=[];
        foreach ($array as $dest => $mar) {       
            $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
            ////////////// линия на карте
            $itog=[];
            foreach ($mar->features as $coord) {
                if (isset($coord->name)){
                    $array_station[$dest][]=$coord;
                    $itog[]=[FuncHelper::helpcoord($coord->coordinates[1]),FuncHelper::helpcoord($coord->coordinates[0])];
                } else {
                    foreach ($coord->points as $c) {
                        $itog[]=[FuncHelper::helpcoord($c[1]),FuncHelper::helpcoord($c[0])];
                    }
                }
            }    
           // var_dump($itog);
            $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
            if ($mapRoute) {
                $mapRoute->line=json_encode($itog);
            } else {
                $mapRoute=new MapRout;
                $mapRoute->route_id=$route->id;
                $mapRoute->line=json_encode($itog);
                $mapRoute->direction=$i;
                $mapRoute->active=1; 
            }
            $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
        }
        /////!!
        
        $route->deleteStationRout(); // удаляем маршрут со временем, чтобы ниже переписать.
        
        foreach ($array_station as $i=>$ss) {
          $key123=0;
                $iu=10;
                foreach ($ss as $st) {
                    if ($st->name!='') {
                        // var_dump($st); die();
                         $station= Station::find()->where(['temp_id' => $st->id,'city_id'=>$city_id])->one();
                         if (!$station) {
                              $station=new Station;
                              $station->name=FuncHelper::refresh_name_station($st->name);
                             if ($key123==0) { $first_st[]=$station->name;}
                             $station->city_id=$city_id;
                             //$point=str_replace(["POINT (",")"],"",$st->location);
                             // $points=explode(" ", $point, $city_id);
                             $station->y=FuncHelper::helpcoord($st->coordinates[1]);
                             $station->x=FuncHelper::helpcoord($st->coordinates[0]);
                             $station->temp_id=$st->id;
                             $station->info='';
                             $station->alias='none';
                             $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                             $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');

                         } else {
                             $station->name=FuncHelper::refresh_name_station($st->name);
                              if ($key123==0) { $first_st[]=$station->name;}
                             $station->y=FuncHelper::helpcoord($st->coordinates[1]);
                             $station->x=FuncHelper::helpcoord($st->coordinates[0]);
                             $station->temp_id=$st->id;
                             $station->info='';
                             $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                             $station->refresh();
                             $id_station_rout=$route->getStationRout($station->id,$i);
                            // var_dump($id_station_rout); die();
                             if (!$id_station_rout) {
                                 $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                             }
                         }
                         $iu=$iu+10;   
                    }
                }
        }
        /////!!
        return true;
    }

    /**
     * Updates an existing RouteStep model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id,$edit=0,$moder=0)
    {
       
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
        }
         
        if (Yii::$app->request->get()) {
            $get=Yii::$app->request->get();
        }
       
        if ($edit OR (isset($post['edit']) AND $post['edit'])) { 
            $model = RouteStep::findone($id); //echo "!!!!!!!";
            //var_dump( $model->source); die();
            $route=Route::findone($model->route_id);
            //$model->redirect=(isset($model->routeredirect->url))?$model->routeredirect->url:null;
            //$model->source=(isset($model->routesource->url))?$model->routesource->url:null;
            $route_id=$model->route_id;
            $model_name='RouteStep';  
           //  var_dump($model->user_id); die('ee');
            if (Yii::$app->user->can('updateStep',['author_id'=>$model->user_id])) {
            
            } else {
               return false;
            }
        } else {
            $model = Route::findone($id);
            
            $model->redirect=(isset($model->routeredirect->url))?$model->routeredirect->url:null;
            $model->source=(isset($model->routesource->text))?$model->routesource->text:null;//var_dump($model->routesource); die();
            $route_id=$id;
            $route=$model;
            $model_name='Route';
        }
        
        
      
       // $allroutecity= Route::findAll(['city_id'=>$route->city_id]);
     /*   var_dump(Yii::$app->authManager->rules);
        var_dump(Yii::$app->authManager->getRule('isAuthor'));//($model->user_id));*/
     //   var_dump(Yii::$app->authManager->checkAccess(Yii::$app->user->id,'updateOwnStep',['author_id'=>$model->user_id])); die();
      //  var_dump(Yii::$app->user->can('updateStep',['author_id'=>$model->user_id]));        die();
      
        
        /////////////////////// SAVE
        if (isset($post)) {
            // echo "<pre>"; var_dump($edit,$post); die();
            if (isset($post['expyand']) AND $post['expyand']=='1') {
                if ($model_name=='Route') { $route_step=new RouteStep(); } else { $route_step=$model; }
                $route_step->saveStep_2_3($model,$post,$model_name); 
                if ($route_step->route_id==0) {
                   $route = new Route;
                   $route->id=NULL;
                   if ($post[$model_name]['version']=='2') { $post[$model_name]['version']=3; }
                   $route->load($post[$model_name],'');
                   $route->save();
                   $route->refresh();
                   $route_step->route_id=$route->id;

                   if (count($route->errors)>0) { var_dump($route->errors); die(); }
                } else {
                    $route = Route::findone($route_step->route_id);
                }
                $route->version=1;
                $route->save();
                $route->refresh();
              //  var_dump($post['expyand']); die();

                $this->createMaproute($post['expyand_json'],$route);
                return $this->redirect(['routestep/update?id='.$route->id]); 
            } 
          /*   if (isset($post['routestep']['version'])) {
                 @$post['rout']['version']=$post['routestep']['version'];// id="routestep-version" 
             } elseif(isset($post['route']['version'])) {
                 @$post['routestep']['version']=post['rout']['version'];
             } else {
                 $post[$model_name]['version']='1';
             }
             */
            if ($post[$model_name]['version']=='2' OR $post[$model_name]['version']=='3') { // 2 - старые без карты 3-новые без карты
                if ($model_name=='Route') { $route_step=new RouteStep(); } else { $route_step=$model; }
                $route_step->saveStep_2_3($model,$post,$model_name); 
              
                if (isset($post['savegood']) AND $post['savegood']=='1' AND (Yii::$app->user->can('admin'))) {
                    if ($route_step->route_id==0) {
                        $route = new Route;
                        $route->id=NULL;
                        if ($post[$model_name]['version']=='2') { $post[$model_name]['version']=3; }
                        $route->load($post[$model_name],'');
                        $route->save();
                        $route->refresh();
                        $route_step->route_id=$route->id;
                        
                        if (count($route->errors)>0) { var_dump($route->errors); die(); }
                    } else {
                        $route = Route::findone($route_step->route_id);
                    }
                    if ($post[$model_name]['version']=='2') { $post[$model_name]['version']=3; }
                    $route->save_good_2_3($post,$model_name);
                    $route_step->status=1; // статус одобрен
                    $route_step->save();
                    
                    $cache = Yii::$app->cache;
                    $cache_return3 = $cache->delete('city_'.$route_step->city_id);
                    for($i3=0;$i3<8;$i3++) {
                        $cache_return1 = $cache->delete('route_'.$route_step->route_id."_".$i3."_0");
                        $cache_return2 = $cache->delete('route_'.$route_step->route_id."_".$i3."_1");                        
                      
                    }
                    $moder_steps= RouteStep::findAll(['status'=>'0', 'user_id'=>$route_step->user_id]);
                   // var_dump('route_'.$route_step->route_id."_".$i3."_0",$cache_return1,$cache_return2); die();
                   return $this->redirect(['routestep/index']); 
                   // // return $this->redirect(['routestep/update','moder'=>1,'edit'=>1,'id'=>$moder_steps[0]->id]); 
                  //  die();va
                } elseif(isset($post['savebad']) AND $post['savebad']=='1' AND (Yii::$app->user->can('admin'))) {
                     $route_step->status=2; // статус отклонен
                    // echo $route_step->user_id; die();
                     $route_step->save();
                }
                else {
                    $route_step->status=0; // статус на модерации
                    $route_step->save();
                }
                
            } elseif ($post[$model_name]['version']=='1' OR $post[$model_name]['version']=='4') {  // 1-новые с картой 4-старые с картой
                if ($model_name=='Route') { $route_step=new RouteStep(); } else { $route_step=$model; }
                $route_step->saveStep_1($model,$post,$model_name); 
            //    var_dump($post,Yii::$app->user->can('admin')); die();
                if (isset($post['savegood']) AND $post['savegood']=='1' AND (Yii::$app->user->can('admin'))) {
                    $route = Route::findone($route_step->route_id);
                    $route->save_good_1($post,$model_name);
                    $route_step->status=1;
                    $route_step->save();
                    
                    $cache = Yii::$app->cache;
                   //  echo "<pre>"; var_dump($cache->get('route_'.$route_step->route_id."_0_0"));    
                    $cache_return3 = $cache->delete('city_'.$route_step->city_id);
                    for($i3=0;$i3<8;$i3++) {
                        $cache_return1 = $cache->delete('route_'.$route_step->route_id."_".$i3."_0");
                        $cache_return2 = $cache->delete('route_'.$route_step->route_id."_".$i3."_1");  
                       // echo "<pre>"; var_dump('route_'.$route_step->route_id."_".$i3."_0",$cache_return1,$cache_return2);                       
                      
                    }
                 
                    
                    $moder_steps= RouteStep::findAll(['status'=>'0', 'user_id'=>$route_step->user_id]);
                    return $this->redirect(['routestep/index']); 
                    //return $this->redirect(['routestep/update','moder'=>1,'edit'=>1,'id'=>$moder_steps[0]->id]); 
                    
                } elseif(isset($post['savebad']) AND $post['savebad']=='1' AND (Yii::$app->user->can('admin'))) {
                     $route_step->status=2; // статус отклонен
                     $route_step->save();
                } else {
                    $route_step->status=0;
                    $route_step->save();
                }       
            }
            
            return $this->redirect(['route/index']); 
        }
        
        
        
        //////////////////////////////
        // var_dump($model->version); die();
        if ($model->version==1 OR $model->version==4) {
            //var_dump($route); die();
                $stations=$route->getStations()->all();
                $stations_all=$route->getStationRouteAll($route_id);
        if (empty($stations_all[0]) AND !empty($stations_all[1])) {
            $stations0=$stations_all[1];
            $stations1=[];
        } else {
                $stations0=@$stations_all[0];//$route->getStationRoute0($route_id);
                $stations1=@$stations_all[1];//$route->getStationRoute1($route_id);
        }
                function f_time_work_cach($q,$id) {// для уменьшения запросов к базе с одинаковыми запросами
                    static  $time_work_cach=[];

                    if(isset($time_work_cach[$q])) {
                        return $time_work_cach[$q];
                    } else {
                        $time_work_cach[$q]=TimeWork::getTimeWorkStep($q,$id);
                        return $time_work_cach[$q];
                    }
                }
                
            //    echo "<pre>"; var_dump($stations_all,$stations0,$stations1); die();
               if ($edit OR (isset($post['edit']) AND $post['edit'])) {
                    foreach ($stations_all as $keyq=>$st) {
                        if ($st) {
                            foreach ($st as $key => $s) {
                                $stations_all[$keyq][$key]['time_work']=f_time_work_cach($st[$key]['id_station_rout'],$id);//TimeWork::getTimeWorkStep($st[$key]['id_station_rout'],$id);
                            }
                        }
                    }
                    if ($stations0) {
                        foreach ($stations0 as $key => $s) {
                            $stations0[$key]['time_work']=f_time_work_cach($stations0[$key]['id_station_rout'],$id);//TimeWork::getTimeWorkStep($stations0[$key]['id_station_rout'],$id);
                        }
                    }
                    if ($stations1) {
                        foreach ($stations1 as $key => $s) {
                            $stations1[$key]['time_work']=f_time_work_cach($stations1[$key]['id_station_rout'],$id);//TimeWork::getTimeWorkStep($stations1[$key]['id_station_rout'],$id);
                        }
                    }
                } else {
                    foreach ($stations_all as $keyq=>$st) {
                        if ($st) {
                            foreach ($st as $key => $s) {
                                $stations_all[$keyq][$key]['time_work']=TimeWork::getTimeWork($st[$key]['id_station_rout']);
                            }
                        }
                    }
                    if ($stations0) {
                        foreach ($stations0 as $key => $s) {
                            $stations0[$key]['time_work']=TimeWork::getTimeWork($stations0[$key]['id_station_rout']);
                        }
                    }
                    if ($stations1) {
                        foreach ($stations1 as $key => $s) {
                            $stations1[$key]['time_work']=TimeWork::getTimeWork($stations1[$key]['id_station_rout']);
                        }
                    }
                }
                if (isset($get['typeday_s'])) { $model->type_day=$get['typeday_s']; } 
           // var_dump($stations0); die();
            if ($moder AND (Yii::$app->user->can('admin'))) {
                 return $this->render('update1_moder', [ 
                    'type_day'=>((isset($get['typeday_s']))?$get['typeday_s']:$model->type_day),
                    'model' => $model,
                     'route' => $route,
                    'edit' => $edit,
                    'stations'=>$stations,'stations0'=>$stations0,'stations1'=>$stations1,'stations_all'=>$stations_all,
                     'route_id' => $route_id
                ]);
            } else { 
                return $this->render('update1', [ // новые с картой
                    'type_day'=>((isset($get['typeday_s']))?$get['typeday_s']:$model->type_day),
                    'model' => $model,
                    'edit' => $edit,
                    'stations'=>$stations,'stations0'=>$stations0,'stations1'=>$stations1,'stations_all'=>$stations_all,
                     'route_id' => $route_id
                ]);
            }
        } elseif($model->version==2 OR $model->version==3) { /// старые и новые без карты
            if ($moder AND (Yii::$app->user->can('admin'))) {
                return $this->render('update2_moder', [
                    'model' => $model,
                    'edit' => $edit,
                    'route' => $route,
                    'route_id' => $route_id,
                //    'allroutecity'=>$allroutecity,
                ]);
            } else {
                return $this->render('update2', [
                    'model' => $model,
                    'edit' => $edit,
                    'route_id' => $route_id,
                 //   'allroutecity'=>$allroutecity,
                ]);
            }
        } 
        die('Error 5446732 - что-то с версией маршрута');
    }
    
    

    /**
     * Deletes an existing RouteStep model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
  /*  public function actionDelete()
    {
        
        $action=Yii::$app->request->post('action');
        $selection=(array)Yii::$app->request->post('selection');//typecasting
       // var_dump($selection); 
        foreach($selection as $id){
            $e= RouteStep::findOne((int)$id);
         if ($action=='delete') {
                $e->delete();
                
            }
        
        }
        return $this->redirect(['index']);
    }*/

    /**
     * Finds the RouteStep model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RouteStep the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RouteStep::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    
    public function actionGettypetime() {
        die('sdf435yu56');
        return $this->renderAjax('_typetime', [
                'model' => '$model',
            ]);
        
    }
    
    public function actionPay() {
        $action=Yii::$app->request->post('action');
        $selection=(array)Yii::$app->request->post('selection');//typecasting
       // var_dump($selection); 
        foreach($selection as $id){
            $e= RouteStep::findOne((int)$id);
          //  var_dump($action); 
            if ($action=='nc') {
                $e->pay=1;
                $e->save();  //var_dump($e->errors); die();
            } elseif ($action=='delete') {
                $e->delete();
                //return $this->redirect(['index']);
            }
            
        }
      // var_dump($selection);
        return $this->redirect(['index']);
        
    }
    
}
