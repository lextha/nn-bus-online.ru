<?php

namespace backend\controllers;

use Yii;
use common\models\Route;
use app\models\RouteStep;
use common\models\RouteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      //  die('fdg');
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view','delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['index','create','update'],
                        'allow' => true,
                        'roles' => ['admin','editor'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST','GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Route models.
     * @return mixed
     */
    public function actionIndex()
    {
        $editor_citis=[];
        $user= User::findOne(['id'=>Yii::$app->user->id]);
      //  echo "<pre>"; var_dump($user->citis); die();
        foreach ($user->citis as $c) {
            $editor_citis[]=$c['id'];
        }
      //  $editor_citis=[23,287,33,43,59,3,1,174,369,2,230,232,234,251,266,271,279,280,382,383,384,386,387,385,112,209,224,106,284,5];
     //   $editor_citis=[33,41,224,2,230,387,284,234,27,125,252,170,254,232,30,67,200,259,36,93,111,17,18,53,160,265,183,205];
        $params=Yii::$app->request->queryParams;
        if (count($params) <= 1) {
            $params = Yii::$app->session['params'];
            if(isset(Yii::$app->session['params']['page'])) {
                    $_GET['page'] = Yii::$app->session['params']['page'];
            }
        } else {
                Yii::$app->session['params'] = $params;
        }
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search($params,$editor_citis);        
        
        //var_dump(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'editor_citis'=>$editor_citis,
        ]);
    }

    /**
     * Displays a single Route model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
       // die('rfgnvghmjyt');
        $model=$this->findModel($id);
        $ser["RouteSearch"]["city_id"]=$model->city_id;
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search($ser);
       // array(2) { ["RouteSearch"]=> array(7) { ["name"]=> string(0) "" ["number"]=> string(0) "" ["active"]=> string(0) "" ["city_id"]=> string(1) "3" ["type_transport"]=> string(0) "" ["type_direction"]=> string(0) "" ["version"]=> string(0) "" } ["_pjax"]=> string(3) "#p0" }
        return $this->render('view', [
            'model' => $model,
             'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Route model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Route();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Route model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        die('nah');
       /* $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            if ($post['Route']['version']=='-1') {
                $route_step=new RouteStep();
                $route_step->saveStep($model,$post['Route']);
            }
           
        } 
        
       /* if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }*/
/*
        return $this->render('update', [
            'model' => $model,
        ]);*/
    }

    /**
     * Deletes an existing Route model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $model->deleteRoute();
        $model->delete();
        return $this->redirect(['index']);
    }
    
   /* удаляем все в городе
    * id города поменять 
    * текст функции вставить в верхнюю и нажать удалить на любом маршруте города
    */
    public function actionDeleteAllInCity($id)
    {
        $model3 = Route::find()->where('city_id=1169')->all();
        foreach ($model3 as $m) {
            echo $m->id."/";
            $model=$this->findModel($m->id);
            $model->deleteRoute();
            $model->delete();
        } die('ok');
        return $this->redirect(['index']);
    }


    /**
     * Finds the Route model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Route the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
       // die('rfgnvgh456mjyt');
        if (($model = Route::findOne($id)) !== null) {    
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
