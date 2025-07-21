<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\Station;
use common\models\Stationmany;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['index','refreshcitystation'],
                        'allow' => true,
                        'roles' => ['canAdmin'],
                    ],
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
    
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
     public function actionRefreshcitystation()
    {
            if (Yii::$app->user->can('admin')) {
            if (Yii::$app->request->post()) {
                $post=Yii::$app->request->post();
            }
           
          /// 
        if (isset($post['city_id'])) {
            $arr_city=[];
            if ($post['city_id']>999) { // 1000 1001 1002 .....
                $t=($post['city_id']-999)*10; // число начала 0 10 20 30 .....
                $posts = Yii::$app->db->createCommand('SELECT `city_id`,COUNT(*) FROM station GROUP BY `city_id` HAVING COUNT(*) > 1')->queryAll();
                $i=0;
                foreach ($posts as $q) {
                    if ($i>=$t AND $i<($t+10)) { // 10 id городов добавляем, со стартовой позиции $t
                        $arr_city[]= $q['city_id'];
                    }
                    $arr_city2[]=$q['city_id'];
                    $i++;
                }
            } else {
                 $arr_city=[$post['city_id']];
            }
            echo "<pre>"; var_dump($arr_city2,$arr_city); die();
            foreach ($arr_city as $city_id) {
                $array_st=[];
                $stations=Station::find()->where("city_id=".$city_id."")->all();
                foreach ($stations as $s) {
                 //   var_dump($s); die();
                    if (!in_array($s->id, $array_st)) {
                        $s->name= str_replace("'", "\'", $s->name);
                        $station_all=Station::find()->where("REPLACE('".$s->name."', ' ', '') = REPLACE(name, ' ', '')"
                                . " AND city_id='".$s->city_id."'"
                                . " AND inmany=0"
                                . " AND ABS(x-".$s->x.")<0.001 AND ABS(y-".$s->y.")<0.001")->all();
                        $stationmany=new Stationmany;
                        $stationmany->name=$s->name;
                        $stationmany->alias=$s->alias;
                        $stationmany->city_id=$s->city_id;
                        $stationmany->save();
                        $stationmany->refresh();
                        foreach ($station_all as $value) {
                            (new \yii\db\Query())->createCommand()->insert('stationmany_station', [
                                            'stationmany_id' => $stationmany->id,
                                            'station_id' => $value->id,
                                        ])->execute();
                            $value->inmany=1;
                            $value->save();
                            $array_st[]=$value->id; // записываем уже обработаные остановки
                        }  
                    }
                }
            }
           // die();
           }
        }

        return $this->goHome();
    }
    
    

}
