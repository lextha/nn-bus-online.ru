<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\City;
use common\models\Route;
use common\models\TimeWork;
use common\models\Station;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
//use frontend\models\ContactForm;
use common\models\User;
//use yii\helpers\Url;
//use yii\web\HttpException;
use common\models\Stationmany;
//use common\rules\AuthorViewstep;
//use common\helpers\TimeHelper;
use common\helpers\NameHelper;
use common\models\News;
use yii\helpers\Url;

/**
 * Site controller
 */
class SiteController extends Controller {

    public $city_online_magic = [
        '182' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // нижний новгород
        '26' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // арзамас
        '34' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // балахна
        '289' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // богородск
        '44' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // бор
        '80' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // городец
        '83' => ['http://asip.cds-nnov.ru/api/rpc.php', 'nn'], // дзержинск
        '111' => ['https://navi.kazantransport.ru/api/rpc.php', 'kazan'], ///КАЗАНЬ 
        '93' => ['http://xn--80axnakf7a.xn--80acgfbsl1azdqr.xn--p1ai/api/rpc.php', 'ekt'], // екатеренбург
        '241' => ['http://bus67.ru/api/rpc.php', "smolensk"], // смоленск  
        '9' => ['https://businfo.cdsvyatka.com/api/rpc.php', 'kirov'], /// id_city => url // КИРОВ
        '199' => ['http://bus-55.ru/api/rpc.php', "omsk"], // ОМСК
        '61' => ['https://transport.volganet.ru/api/rpc.php', ''], //ВОЛГОГРАД OLD MAFIC
        '29' => ['https://bus.arhtc.ru/api/rpc.php', "arxangelsk"], // архангельск
    ];
    public $city_online = [/// массив с онлайн городами
        //  
        //  
        //   
        //  '3'=>'https://bus42.info/navi/api/rpc.php', 
        //   '13'=>'https://bus42.info/navi/api/rpc.php', 
        '305' => 'https://bus42.info/navi/api/rpc.php', //КИСЕЛЕВСК
        '410' => 'https://bus42.info/navi/api/rpc.php', //Анжеро-Судженск
        '37' => 'https://bus42.info/navi/api/rpc.php', //Белово
        '455' => 'https://bus42.info/navi/api/rpc.php', //Берёзовский
        '293' => 'https://bus42.info/navi/api/rpc.php', //Гурьевск
        '1165' => 'https://bus42.info/navi/api/rpc.php', //Ижморский
        '388' => 'https://bus42.info/navi/api/rpc.php', //Ленинск-Кузнецкий
        '375' => 'https://bus42.info/navi/api/rpc.php', //Мариинск
        '8' => 'https://bus42.info/navi/api/rpc.php', //Междуреченск
        '809' => 'https://bus42.info/navi/api/rpc.php', //Мыски
        '13' => 'https://bus42.info/navi/api/rpc.php', //Новокузнецк
        '872' => 'https://bus42.info/navi/api/rpc.php', //Осинники
        '212' => 'https://bus42.info/navi/api/rpc.php', //Прокопьевск
        '1166' => 'https://bus42.info/navi/api/rpc.php', //Промышленная
        '1029' => 'https://bus42.info/navi/api/rpc.php', //Тайга
        '1036' => 'https://bus42.info/navi/api/rpc.php', //	Таштагол
        '1045' => 'https://bus42.info/navi/api/rpc.php', //	Топки
        '1167' => 'https://bus42.info/navi/api/rpc.php', //Тяжинский
        '281' => 'https://bus42.info/navi/api/rpc.php', //Юрга
        '1164' => 'https://bus42.info/navi/api/rpc.php', //Яшкино
        // 
        // 
        '138' => 'https://mu-kgt.ru/informing/api/rpc.php', // красноярск
        '32' => 'https://mu-kgt.ru/regions/api/rpc.php', // ачинск
        '267' => 'https://transportrb.ru/api/rpc.php', // уфа
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
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
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        $id = Yii::$app->params['city_id'];
        $city = City::findOne(['id' => $id]);
        date_default_timezone_set(Yii::$app->params['DateTimeZone']); // часовой пояс по городу
        $cache = Yii::$app->cache;

        //$cache->flush();
        $cache_return = $cache->get('city_' . $id); // var_dump($cache_return); die('555'); 
        // if (Yii::$app->user->can('admin') OR $_SERVER['REMOTE_ADDR'] == '5.187.71.181') {
        $cache_return = false;
        //     }
        // var_dump($cache_return);
        if ($cache_return === false) {
            $routes = Route::find()->where("city_id=" . $city->id . " AND active=1 AND name<>'none' AND (version=1 OR version=4)")->orderBy('cast(number as unsigned), number')->all(); //AND (version=1 OR version=4)
            //var_dump($city->id);
            $route_dacha = Route::find()->where("city_id=" . $city->id . " AND active=1 AND season=2")->count();
            if (isset($city->info_dacha) && $city->info_dacha != '' && $route_dacha == 0) {
                $route_dacha = 1;
            }
            $routes_no_work = Route::find()->where("city_id=" . $city->id . " AND active=0 AND name<>'none'")->orderBy('cast(number as unsigned), number')->all();
            $routes_by_groups = [1 => [], 2 => [], 3 => [], 5 => []];
            $routes_no_work_by_groups = [];
            foreach ($routes as $r) {
                if ($r->type_transport == 4) {
                    $ttr = 1;
                } else {
                    $ttr = $r->type_transport;
                }

                $r->name = NameHelper::replaceWordsInStringWsokrat($r->name);
                // var_dump($r); die();
                $routes_by_groups[$ttr][] = $r;
            }

            foreach ($routes_no_work as $r) {
                //if ($_SERVER['REMOTE_ADDR']=='5.187.71.217') { echo "<pre>"; var_dump($r->id,$r->redirect);echo "</pre>"; }
                if ($r->redirect == false) { // если маршрут недействующий и без редиректа
                    $routes_no_work_by_groups[$r->type_direction][$r->type_transport][] = $r;
                }
            }
            $news = News::find()->where("city_id=" . $city->id . " AND title2<>''")->orderBy('time DESC')->limit(2)->all();
            $return = $this->render('index', ['city' => $city, 'routes' => $routes_by_groups, 'routes_no_work' => $routes_no_work_by_groups, 'dacha' => $route_dacha, "news" => $news]);
            //   if (!Yii::$app->user->can('admin') AND $_SERVER['REMOTE_ADDR'] != '5.187.71.217') {
            $cache->set('city_' . $id, $return, 200000);
            //    }
            return $return;
        } else {
            return $cache_return;
        }
        //return $this->render('city',['city'=>$city,'routes'=>$routes_by_groups]);
    }

    public function actionRoute($id, $route, $day_week = 0) {

        $city_id = Yii::$app->params['city_id'];
        $city = City::findOne(['id' => $city_id]);
        date_default_timezone_set(Yii::$app->params['DateTimeZone']); // часовой пояс по городу
        // var_dump($route); die();
        /* if (isset($route->routeredirect->url) AND (!$route->active)) {
          Yii::$app->response->redirect($route->routeredirect->url, 301)->send();
          Yii::$app->end();
          return;
          } */
        if ($route->name == 'none') {
            throw new \yii\web\HttpException(404, 'Страница не найдена.');
        }

        $post = Yii::$app->request->get();
        $pjax_z = 0;
        /*   if (isset($post['day_week']) AND !isset($post['_pjax'])) {
          $url = explode("?", $_SERVER['REQUEST_URI']);
          Yii::$app->response->redirect("https://" . $_SERVER['SERVER_NAME'] . $url[0], 301)->send();
          Yii::$app->end();
          return;
          } elseif (isset($post['_pjax'])) {
          $pjax_z = 1;
          } else {
          //if ($_SERVER['REMOTE_ADDR']=='5.187.70.145') {
          // $route->setIncViews(); // инкримент кол-ва просмотров маршрута
          // }
          } */
        //  die('fdfdf');
        $cache = Yii::$app->cache;
     //   $cache->flush();


        if ($day_week == 0) { // определяем текущий день недели, и выводим расписание для него. Если этого дня недели нет в расписании то ближайший день недели
            $day_week = date('w');
            if ($day_week == 0) {
                $day_week = 7;
            }
            if ($route->type_day == 6 AND $day_week < 6) {
                $day_week = 6;
            }
            if ($route->type_day == 5 AND $day_week > 5) {
                $day_week = 1;
            }
            if ($route->type_day == 8 AND $day_week == 7) {
                $day_week = 1;
            }
            if ($route->type_day == 10 AND $day_week < 6) {
                $day_week = 6;
            }
            if ($route->type_day == 11 AND $day_week == 7) {
                $day_week = 1;
            }
        }

        $cache_return = $cache->get('route_' . $id . "_" . $day_week . "_" . $pjax_z);
        // var_dump($cache_return); die();
        // if ($_SERVER['REMOTE_ADDR'] == '88.210.10.69') { echo 'route_' . $id . "_" . $day_week . "_" . $pjax_z;   var_dump($cache_return); die(); }
        if (Yii::$app->user->can('admin') OR $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
            $cache_return = false;
        }
        /* if ($_SERVER['REMOTE_ADDR'] == '5.187.71.226') {
          $cache_return = false;
          } */
        //  if ($city->id=='9') { $cache_return=false;  }
        if ($cache_return === false) {
            if ($route->version == '1' OR $route->version == '4') {
                $route['name'] = NameHelper::replaceWordsInString($route['name']);
                $similar = []; //Route::getRoutesSimilar($id, $city->id);
                $stations = $route->getStations()->all();
                // var_dump($stations); die();
                foreach ($stations as $k => $s) {
                    $stations[$k]['name'] = NameHelper::replaceWordsInString($stations[$k]['name']);
                }
                $stationsall = $route->getStationRouteAll($id);
                /* if (!$route->active) { // не активный NameHelper::replaceWordsInString(
                  $pohozhii = Route::getRoutesSimilarone($route, $city->id);
                  } else {
                  $pohozhii = null;
                  } */
                //  if ($_SERVER['REMOTE_ADDR']=='5.187.69.40') {                echo "<pre>";    var_dump($stationsall);  }
                foreach ($stationsall as $keyr => $st) {
                    if ($st) {
                        foreach ($st as $key => $st1) {

                            $stationsall[$keyr][$key]['name'] = NameHelper::replaceWordsInString($stationsall[$keyr][$key]['name']);
                            $stationsall[$keyr][$key]['time_work'] = TimeWork::getTimeWork($st1['id_station_rout']);
                        }
                    }
                }

                $marsh = [];

                if (isset($this->city_online_magic[$route->city_id])) {
                    $return = $this->render('route1online2', ['route' => $route, 'marsh' => $marsh, 'similar' => $similar, 'city' => $city, 'stations' => $stations, /* 'stations0'=>$stations0,'stations1'=>$stations1, */ 'stationsall' => $stationsall, 'day_week' => $day_week]);
                } elseif (isset($this->city_online[$route->city_id])) {//AND $_SERVER['REMOTE_ADDR']=='5.187.69.126'
                    /*   if ($_SERVER['REMOTE_ADDR']=='5.187.71.119') {
                      $return=$this->render('route1online2',['route'=>$route,'marsh'=>$marsh,'similar'=>$similar,'city'=>$city,'stations'=>$stations,'stationsall'=>$stationsall,'day_week' => $day_week]);
                      } else { */
                    $return = $this->render('route1online2', ['route' => $route, 'marsh' => $marsh, 'similar' => $similar, 'city' => $city, 'stations' => $stations, /* 'stations0'=>$stations0,'stations1'=>$stations1, */ 'stationsall' => $stationsall, 'day_week' => $day_week]);
                    // }
                } else {
                    $return = $this->render('route1', ['route' => $route, 'marsh' => $marsh, 'similar' => $similar, 'pohozhii' => $pohozhii, 'city' => $city, 'stations' => $stations, /* 'stations0'=>$stations0,'stations1'=>$stations1, */ 'stationsall' => $stationsall, 'day_week' => $day_week]);
                }
            } elseif ($route->version == '2') { //Старые без карты
                $similar = []; //Route::getRoutesSimilar2($id, $city->id);
                $marshrut = $route->getMarshrut();
                $extra_fields = $route->getExtraFields();
                $return = $this->render('route2', ['route' => $route, 'city' => $city, 'marshrut' => $marshrut, 'extra_fields' => $extra_fields, 'similar' => $similar]);
            } elseif ($route->version == '3') { //Новые без карты
                $similar = []; //Route::getRoutesSimilar2($id, $city->id);
                $marshrut = $route->getMarshrut();
                //   $extra_fields=$route->getExtraFields();
                $return = $this->render('route3', ['route' => $route, 'city' => $city, 'marshrut' => $marshrut, 'similar' => $similar]);
            }
            /*     if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {
              //  $cache_return1 = $cache->delete('route_'.$id."_".$day_week."_".$pjax_z);
              var_dump('route_'.$id."_".$day_week."_".$pjax_z); die();
              } */
            // if (!Yii::$app->user->can('admin') AND $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
            $cache->set('route_' . $id . "_" . $day_week . "_" . $pjax_z, $return, 500000);
            //var_dump($cache,'route_' . $id . "_" . $day_week . "_" . $pjax_z);
            //  }
            return $return;
        } else {
            /*   if ($_SERVER['REMOTE_ADDR'] == '88.210.10.69') {
              echo "ccccc"; die();
              }
             */
            return $cache_return;
        }
    }

    public function actionNewslist($id) {
        $news = News::find()->where("city_id=" . $id)->orderBy(['time' => SORT_DESC])->limit(25)->all();
        $city = City::find()->where("id=" . $id)->one();
        $return = $this->render('newslist', ['news' => $news, 'city' => $city]);
        return $return;
    }

    public function actionNews($id) {
        $city_id = Yii::$app->params['city_id'];
        $news = News::find()->where("id=" . $id)->one();
        // $news->view = $news->view + 1;
        //  $news->save();
        $city = City::find()->where("id=" . $city_id)->one();
        preg_match_all('/{a n=\'([^\']+)\' t=\'([^\']+)\'}([^{]+){\/a}/u', $news->text2, $matches);
        //echo "<pre>"; var_dump($matches); die();
        if (isset($matches[1]) AND count($matches[1]) > 0) {
            foreach ($matches[1] as $key => $match) {
                $type_transport = $matches[2][$key];
                if (!($type_transport > 0 AND $type_transport < 6)) {
                    $type_transport = 1;
                }
                /*  $ex = explode(" ", $match);
                  if ($ex[0] == '№') { */
                $route = Route::find()->where(["number" => $match, "city_id" => $city_id, "type_transport" => $type_transport])->andWhere("version=1 OR version=4")->one();
                if ($route) {
                    $link = '<a href="' . Url::toRoute(['site/route', 'id' => $route->id, 'route' => $route, 'city' => $city]) . '">' . $matches[3][$key] . '</a>';
                    // $links[] = $link;
                    // echo "<pre>"; var_dump($match,$link); 
                    $news->text2 = preg_replace('/{a n=\'' . $match . '\' t=\'' . $type_transport . '\'}[^{]+{\/a}/', $link, $news->text2);
                }
                // }
            }
        }//die();
        $news->text2 = preg_replace('/{a n=\'([^\']+)\' t=\'([^\']+)\'}([^{]+){\/a}/u', '$3', $news->text2);
        $news->text2 = str_replace(["{a}", "{/a}"], '', $news->text2);

        ///////////////////////////
        // Проверяем, пуста ли входящая переменная
        /*  if (empty($news->photo)) {
          $directory = 'i/city/' . $city->alias;
          $filePath = $directory . '/i.txt';

          // Получаем список файлов с расширением .jpg в папке
          $jpgFiles = glob($directory . '/*.jpg');
          $jpgCount = count($jpgFiles);
          if ($jpgCount != 0) {
          // Читаем текущее число из файла
          if (file_exists($filePath)) {
          $currentNumber = (int) file_get_contents($filePath);
          } else {
          $currentNumber = 0;
          }
          // Увеличиваем число на 1, если оно меньше количества файлов jpg
          if ($currentNumber < $jpgCount) {
          $newNumber = $currentNumber + 1;
          } else {
          // Если число больше или равно количеству файлов, устанавливаем 1
          $newNumber = 1;
          }
          $news->photo=$directory . "/bus".$newNumber.".jpg";
          $news->save();
          // Записываем новое число в файл
          file_put_contents($filePath, $newNumber);
          }
          }
         */
        ///////////////////////////


        $newslist = News::find()->where("id<>" . $id . " AND city_id=" . $city_id)->all();
        $return = $this->render('news', ['news' => $news, 'city' => $city, 'newslist' => $newslist]);
        return $return;
    }

    public function actionGettemp() {
        $city_id = Yii::$app->params['city_id'];
        $city = City::findOne(['id' => $city_id]);
        $temp = false;
        $key = 'https://api.openweathermap.org/data/2.5/weather?q=' . $city->name . '&appid=6c8349feac5225ee1af114d071e44d69&units=metric';
        $key = str_replace(" ", "%20", $key);
        //  'https://api.openweathermap.org/data/2.5/weather?q=' . $city->name . '&appid=6c8349feac5225ee1af114d071e44d69&units=metric'
        $wether = false;
        while ($wether == false) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $wether = curl_exec($ch);
            curl_close($ch);
          //  var_dump($key, $wether);
          //  die();
            // $wether = file_get_contents($key); // 
        }
        if ($wether) {
            $temp = json_decode($wether);
        }
        //{"coord":{"lon":44.002,"lat":56.3287},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01n"}],"base":"stations","main":{"temp":-0.27,"feels_like":-5.25,"temp_min":-0.27,"temp_max":-0.23,"pressure":1023,"humidity":59},"visibility":10000,"wind":{"speed":5,"deg":210},"clouds":{"all":0},"dt":1710439579,"sys":{"type":2,"id":48933,"country":"RU","sunrise":1710386500,"sunset":1710428684},"timezone":10800,"id":520555,"name":"Nizhny Novgorod","cod":200}
        return $this->renderAjax('_temperatura', ['city' => $city,
                    'temp' => $temp,
        ]);
    }

    public function actionGettime($station_rout_id, $day, $flag2 = 0) {
        // $post = Yii::$app->request->get();
        // if ($_SERVER['REMOTE_ADDR']=='5.187.70.26') {$s=Route::getStationRouteOne($station_rout_id); var_dump($s,$recaptcha); die(); }
        /*  if ((isset($recaptcha->score) AND $recaptcha->score >= 0.5) OR (Yii::$app->user->can('admin')) OR ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) { */
        $s = Route::getStationRouteOne($station_rout_id);
        //  if (!$s) { throw new \yii\web\HttpException(404, 'Не найдена'); }
        $city = City::findOne(['id' => $s['city_id']]);
        date_default_timezone_set(Yii::$app->params['DateTimeZone']); // часовой пояс по городу
        if (isset($s)) {
            $s['time_work'] = TimeWork::getTimeWork($s['id_station_rout']);
        } else {
            $s = null;
        }
        // $timework=TimeWork::getTimeWork($station_rout_id);
        /* if ($day==1) {
          $t=$timework['monday'];
          } elseif($day==2) {
          $t=$timework['tuesday'];
          } elseif($day==3) {
          $t=$timework['wednesday'];
          } elseif($day==4) {
          $t=$timework['thursday'];
          } elseif($day==5) {
          $t=$timework['friday'];
          } elseif($day==6) {
          $t=$timework['saturday'];
          } elseif($day==7) {
          $t=$timework['sunday'];
          } */
        //var_dump($s['type_day']);

        return $this->renderAjax('_timework2', ['s' => $s,
                    'type_day' => $s['type_day'],
                    'day_week' => $day,
                    'f' => 'true',
                    'flag2' => $flag2,
                    'city' => $city,
                    'ajax' => '1']);
        /*  } else {
          //  throw new ForbiddenHttpException('sss');
          throw new \yii\web\HttpException(403, 'Доступ запрещен');
          //   throw new yii\web\HttpException(403, 'Доступ запрещен');
          } */
    }

    public function actionGetsxem($ri) {

        $rl_racetype_to_my = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6];
        $route_id = $ri;
        $post = Yii::$app->request->get();
        $buses = [];
        $route = Route::find()->where(['id' => $route_id])->one();
        /////////////////////////////
        if (isset($this->city_online_magic[$route->city_id])) {
            $json = \console\helpers\FuncHelper::getpos_magic($route, $this->city_online_magic[$route->city_id]);
        } else {
            $json = \console\helpers\FuncHelper::getpos($route, $this->city_online[$route->city_id]);
        }
        if ($json && isset($json->result)) {
            foreach ($json->result as $a) {

                ///////////////////// смена id автобуса на свой
                $m_id = 0;
                $command = (new \yii\db\Query())
                                ->from('idtoid')
                                ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
                                ->createCommand()->queryOne();
                if ($command) {
                    $m_id = $command['m_id'];
                } else {
                    (new \yii\db\Query())->createCommand()->insert('idtoid', [
                        'u_id' => $a->u_id,
                        'route_id' => $route_id,
                    ])->execute();
                    $command = (new \yii\db\Query())
                                    ->from('idtoid')
                                    ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
                                    ->createCommand()->queryOne();
                    $m_id = $command['m_id'];
                }

                if (isset($this->city_online_magic[$route->city_id])) {
                    $json = \console\helpers\FuncHelper::getunit_magic($route, $a->u_id, $this->city_online_magic[$route->city_id]);
                } else {
                    $json = \console\helpers\FuncHelper::getunit_magic($route, $a->u_id, $this->city_online[$route->city_id]);
                }
                //   var_dump($json); die();
                ///////////////////////
                $buses[] = $json->result;
            }
        }
        $stations = Route::getStationRouteAll($ri);

        foreach ($buses as $bb) {
            foreach ($bb as $key_b => $b) {
                foreach ($stations[$rl_racetype_to_my[$b->rl_racetype]] as $key_r => $r) {
                    if ($r['temp_id'] == $b->st_id) {
                        ////////////////// мнеяем время от -1 до +1 минуты
                        $change = rand(0, 2);
                        if ($change === 0) {
                            $newTime = $b->ta_arrivetime;
                        } elseif ($change === 1) {
                            $newTime = date("H:i", strtotime($b->ta_arrivetime) + 60);
                        } else {
                            $newTime = date("H:i", strtotime($b->ta_arrivetime) - 60);
                        }
                        ////////////
                        $stations[$rl_racetype_to_my[$b->rl_racetype]][$key_r]['time'] = $newTime;
                        if ($key_b == 0) {
                            $stations[$rl_racetype_to_my[$b->rl_racetype]][$key_r]['bus_arrive'] = true;
                        } else {
                            $stations[$rl_racetype_to_my[$b->rl_racetype]][$key_r]['bus_arrive'] = false;
                        }
                    }
                }
            }
        }
        return $this->renderAjax('_routesxem', ['route' => $route, 'stations' => $stations]);
    }

    private function randfloat($num) {

        $roundedNum = round($num, 4);

        // Генерируем случайное отклонение
        $deviation = (float) rand(-5, 5) / 100000;

        // Добавляем отклонение к округленному числу
        $formattedNum = $roundedNum + $deviation;
        return (string) $formattedNum;
    }

    private function randfloat2($num) {

        $deviation = rand(-2, 2);

        $formattedNum = $num + $deviation;
        return (string) $formattedNum;
    }

    public function actionGetbusonline($ri) {
        //die('ddfsfsdfsd');

        $route_id = $ri;
        $post = Yii::$app->request->get();
        $marsh = [];
        $route = Route::find()->where(['id' => $route_id])->one();
        /////////////////////////////
        if (isset($this->city_online_magic[$route->city_id])) {
            $json = \console\helpers\FuncHelper::getpos_magic($route, $this->city_online_magic[$route->city_id]);
        } else {
            $json = \console\helpers\FuncHelper::getpos($route, $this->city_online[$route->city_id]);
        }
        if ($json && isset($json->result)) {
            foreach ($json->result as $a) {

                ///////////////////// смена id автобуса на свой
                $m_id = 0;
                $command = (new \yii\db\Query())
                                ->from('idtoid')
                                ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
                                ->createCommand()->queryOne();
                if ($command) {
                    $m_id = $command['m_id'];
                } else {
                    (new \yii\db\Query())->createCommand()->insert('idtoid', [
                        'u_id' => $a->u_id,
                        'route_id' => $route_id,
                    ])->execute();
                    $command = (new \yii\db\Query())
                                    ->from('idtoid')
                                    ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
                                    ->createCommand()->queryOne();
                    $m_id = $command['m_id'];
                }
                $lat = $this->randfloat($a->u_lat);
                $long = $this->randfloat($a->u_long);

                $cours = $this->randfloat2($a->u_course);

                ///////////////////////
                $marsh[] = [$lat, $long, $m_id, $cours];
            }
        }

        return json_encode($marsh);
        die(); /*
          $stationsall = $route->getStationRouteAll($route_id);

          if ($json && isset($json->result)) {
          foreach ($json->result as $a) {
          // var_dump($a); die();
          $x0 = 1;
          $y0 = 1;
          $z0 = 1;
          $pos_insert = 0;
          $mqw = 1;
          //var_dump($stationsall[0][0]['name'],$a->rl_firststation_title);echo "******";
          if (($l0 AND count($l0) > 0) AND ($l1 AND count($l1) > 0)) {
          if ($stationsall[1][0]['name'] == $a->rl_firststation_title) {
          $mqw = 1;
          } else {
          $mqw = 0;
          }
          } else {
          $mqw = 1;
          if ($l1 AND !$l0) {
          $l0 = $l1;
          $l1 = null;
          }
          }
          if ($l0) {
          foreach ($l0 as $k => $l) {
          $x00 = abs((float) $l[0] - (float) $a->u_lat);
          $y00 = abs((float) $l[1] - (float) $a->u_long);
          $z00 = $x00 + $y00;
          if ($z00 < $z0) {
          $pos_insert = $k;
          $z0 = $z00;
          }
          }
          }
          // var_dump($pos_insert, count($l0));
          if ($l1) {
          foreach ($l1 as $k => $l) {
          $x00 = abs((float) $l[0] - (float) $a->u_lat);
          $y00 = abs((float) $l[1] - (float) $a->u_long);
          $z00 = $x00 + $y00;
          if ($z00 < $z0) {
          $pos_insert = $k;
          $z0 = $z00;
          }
          }
          }


          ///////////////////// смена id автобуса на свой
          $m_id = 0;
          $command = (new \yii\db\Query())
          ->from('idtoid')
          ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
          ->createCommand()->queryOne();
          if ($command) {
          $m_id = $command['m_id'];
          } else {
          (new \yii\db\Query())->createCommand()->insert('idtoid', [
          'u_id' => $a->u_id,
          'route_id' => $route_id,
          ])->execute();
          $command = (new \yii\db\Query())
          ->from('idtoid')
          ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
          ->createCommand()->queryOne();
          $m_id = $command['m_id'];
          }
          ///////////////////////
          if ($mqw == 0) {
          $l0 = array_merge(array_slice($l0, 0, $pos_insert, true),
          [array((float) $a->u_lat, (float) $a->u_long, 2, 0, $m_id)], // вместо 0 - $a->u_statenum
          array_slice($l0, $pos_insert, count($l0) - 1, true));
          } elseif ($mqw == 1) {
          $l1 = array_merge(array_slice($l1, 0, $pos_insert, true),
          [array((float) $a->u_lat, (float) $a->u_long, 2, 0, $m_id)], // вместо 0 - $a->u_statenum
          array_slice($l1, $pos_insert, count($l1) - 1, true));
          } else {
          echo 'error 5655';
          }
          }
          $marsh[0] = $l0;
          $marsh[1] = $l1;
          //  echo "<pre>";var_dump($marsh); die();
          /////////////////////////////
          echo json_encode($marsh);
          //  $return = $this->renderAjax('_mapnew', ['route_line' => $route_line, 'marsh' => $marsh, 'stationsall' => $stationsall, 'city' => $city]);
          }
          die('error487515'); */
    }

    public function actionGetmaponline($ri) {
        // die('dsf');
        $route_id = $ri;
        $post = Yii::$app->request->get();

        $route = Route::find()->where(['id' => $route_id])->one();
        $stationsall = $route->getStationRouteAll($route_id);

        $city = City::find()->where(['id' => $route->city_id])->one();

        $route_line = Route::getMaproute($route->id);
        $l0 = [];
        $l1 = [];
        $l0 = json_decode($route_line[0]['line']);
        $l1 = json_decode($route_line[1]['line']);

        if ($l0 && count($l0) > 0) {
            foreach ($l0 as $k => $l) {
                $l0[$k][] = 0;
            }
        }
        if ($l1 && count($l1) > 0) {
            foreach ($l1 as $k => $l) {
                $l1[$k][] = 0;
            }
        }


        if ($stationsall[0]) {
            foreach ($stationsall[0] as $se) { // остановки встраиваем в маршрут
                $x0 = 1;
                $y0 = 1;
                $z0 = 1;
                $pos_insert = 0;
                foreach ($l0 as $k => $l) {
                    $x00 = abs((float) $l[0] - (float) $se['y']);
                    $y00 = abs((float) $l[1] - (float) $se['x']);
                    $z00 = $x00 + $y00;
                    //if ($x00<$x0 AND $y00<$y0 AND $x00<0.01 AND $y00<0.01) { $pos_insert=$k; $x0=$x00; $y0=$y00;    }
                    if ($z00 < $z0) {
                        $pos_insert = $k;
                        $z0 = $z00;
                    }
                }

                //  var_dump($pos_insert,$x0,$y0); 
                // echo "<pre>";
                if ($z0 < 0.005) {
                    $l0 = array_merge(array_slice($l0, 0, $pos_insert),
                            [array((float) $se['y'], (float) $se['x'], 1, $se['name'], $se['id'])],
                            array_slice($l0, $pos_insert, count($l0) - 1));
                }
                //  var_dump($l0);  die();
            }
        }
        if ($stationsall[1]) {
            foreach ($stationsall[1] as $se) { // остановки встраиваем в маршрут
                $x0 = 1;
                $y0 = 1;
                $z0 = 1;
                $pos_insert = 0;
                foreach ($l1 as $k => $l) {
                    $x00 = abs((float) $l[0] - (float) $se['y']);
                    $y00 = abs((float) $l[1] - (float) $se['x']);
                    $z00 = $x00 + $y00;
                    // if ($x00<$x0 AND $y00<$y0 AND $x00<0.01 AND $y00<0.01) { $pos_insert=$k; $x0=$x00; $y0=$y00; }
                    if ($z00 < $z0) {
                        $pos_insert = $k;
                        $z0 = $z00;
                    }
                }
                if ($z0 < 0.005) {
                    $l1 = array_merge(array_slice($l1, 0, $pos_insert, true),
                            [array((float) $se['y'], (float) $se['x'], 1, $se['name'], $se['id'])],
                            array_slice($l1, $pos_insert, count($l1) - 1, true));
                }
            }
        }
        //    var_dump($l0,$l1); die();
        //  array_multisort(array_column($l0, '0'), SORT_ASC, array_column($l0, '1'), SORT_ASC, $l0);
        //  array_multisort(array_column($l1, '0'), SORT_ASC, array_column($l1, '1'), SORT_ASC, $l1);
        $marsh[0] = $l0;
        $marsh[1] = $l1;
        //  echo "<pre>";var_dump($marsh); die();
        /////////////////////////////

        $return = $this->renderAjax('_mapnew', ['route_line' => $route_line, 'marsh' => $marsh, 'stationsall' => $stationsall, 'city' => $city]);

        return $return;
    }

    /*
      public function actionRoutemap($id, $route, $city) {

      return $this->render('routemap', ['route' => $route, 'city' => $city]);
      }

      public function actionGetmarsh($id) {
      $post = Yii::$app->request->get();
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      $recaptcha_secret = '6LcL0rQcAAAAAHoOixrHODf_D-JGLDDT-Sme42M_';
      $recaptcha_response = $post['capfs'];

      // Отправляем POST запрос и декодируем результаты ответа
      $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
      $recaptcha = json_decode($recaptcha);
      //  if ($_SERVER['REMOTE_ADDR']=='5.187.71.68') { var_dump($recaptcha->score);  die(); }
      if ((isset($recaptcha->score) AND $recaptcha->score >= 0.5) OR (Yii::$app->user->can('admin')) OR ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
      $route = Route::find()->where(['id' => $id])->one();
      $marshrut = $route->getMarshrut();
      $return = $this->renderAjax('_route3marsh', ['marshrut' => $marshrut]);
      return $return;
      } else {
      throw new \yii\web\HttpException(403, 'Нет доступа.');
      }
      }

      public function actionGetrasp($route_id) {
      //die('dfg');
      $post = Yii::$app->request->get();
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      $recaptcha_secret = '6LcL0rQcAAAAAHoOixrHODf_D-JGLDDT-Sme42M_';
      $recaptcha_response = $post['capfs'];

      // Отправляем POST запрос и декодируем результаты ответа
      $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
      //  if ($_SERVER['REMOTE_ADDR']=='5.187.71.206') { var_dump($recaptcha);  die(); }
      $recaptcha = json_decode($recaptcha);

      if ((isset($recaptcha->score) AND $recaptcha->score >= 0.5) OR (Yii::$app->user->can('admin')) OR ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
      $route = Route::find()->where(['id' => $route_id])->one();
      if ($route->version == 2) {
      $extra_fields = $route->getExtraFields();
      $tw = $extra_fields[5]->value;
      //  var_dump($tw);
      } else {
      $tw = $route->time_work;
      }
      //var_dump($route); die();
      return $this->renderAjax('_getrasp', ['time_work' => $tw]);
      } else {
      throw new \yii\web\HttpException(403, 'Нет доступа.');
      }
      }

      public function actionGetsearch($number, $text, $city_id) {
      // var_dump($city_id); die();
      $routes = City::getSearch($number, $text, $city_id);
      return $this->renderAjax('_search', ['routes' => $routes]);
      }

      public function actionGetsearchindex($text) {
      // var_dump($city_id); die();
      $cities = City::getSearchcities($text);
      return $this->renderAjax('_searchindex', ['cities' => $cities]);
      }

      public function actionGetmodal($rel, $route_id) {
      return $this->renderAjax('_getmodal', ['rel' => $rel, 'route_id' => $route_id]);
      }

      public function actionSendinfo() {
      $post = Yii::$app->request->post();
      // var_dump($post);
      if (Route::setRoutemessage($post['route_id'], $post['errortext'])) {


      return true;
      } else {
      return false;
      }
      }

      public function actionGetmap($route_id) {
      $post = Yii::$app->request->get();
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      $recaptcha_secret = '6LcL0rQcAAAAAHoOixrHODf_D-JGLDDT-Sme42M_';
      $recaptcha_response = $post['capfs'];

      // Отправляем POST запрос и декодируем результаты ответа
      $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
      $recaptcha = json_decode($recaptcha);
      //if ($_SERVER['REMOTE_ADDR']=='23.129.64.202') { var_dump($recaptcha->score);  die(); }

      if ((isset($recaptcha->score) AND $recaptcha->score >= 0.5) OR (Yii::$app->user->can('admin')) OR ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
      $cache = Yii::$app->cache;
      // $cache->flush();
      //$cache->delete('map1'.$route_id);
      $cache_return = $cache->get('map1' . $route_id);
      // if ($_SERVER['REMOTE_ADDR']=='5.187.69.40') { $cache_return=false; }
      // $cache_return->flushValues();
      if ($cache_return === false) {
      $route = Route::find()->where(['id' => $route_id])->one();
      //$s=Route::getStationRouteOne($station_rout_id);


      $stationsall = $route->getStationRouteAll($route_id);
      if (count($stationsall) < 3) {
      $stations0 = ($stationsall[0]) ? $stationsall[0] : false; //$route->getStationRoute0($id);
      $stations1 = ($stationsall[1]) ? $stationsall[1] : false; //$route->getStationRoute1($id);
      } else {
      $stations0 = false;
      $stations1 = false;
      }

      $route_line = Route::getMaproute($route_id);
      $city = City::find()->where(['id' => $route->city_id])->one();
      $return = $this->renderAjax('_map', ['route_line' => $route_line, 'stations0' => $stations0, 'stations1' => $stations1, 'stationsall' => $stationsall, 'city' => $city]);
      $cache->set('map1' . $route_id, $return, 1200000);
      return $return;
      } else {
      return $cache_return;
      }
      } else {
      throw new \yii\web\HttpException(403, 'Нет доступа.');
      }
      }

      public function actionGetrouteonline($route_id) {
      $post = Yii::$app->request->get();
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      $recaptcha_secret = '6LcL0rQcAAAAAHoOixrHODf_D-JGLDDT-Sme42M_';
      $recaptcha_response = $post['capfs'];
      // Отправляем POST запрос и декодируем результаты ответа
      $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
      $recaptcha = json_decode($recaptcha);
      // if ($_SERVER['REMOTE_ADDR']=='23.129.64.202') { var_dump($recaptcha->score);  die(); }
      if ((isset($recaptcha->score) AND $recaptcha->score >= 0.5) OR (Yii::$app->user->can('admin')) OR ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
      $count_online = -1;
      $route = Route::findOne(['id' => $route_id]);
      $city = City::findOne(['id' => $route->city_id]);
      //   var_dump($route_id);
      $stationsall = $route->getStationRouteAll($route_id);
      foreach ($stationsall as $keyr => $st) {
      if ($st) {
      foreach ($st as $key => $st1) {
      $stationsall[$keyr][$key]['time_work'] = TimeWork::getTimeWork($st1['id_station_rout']);
      }
      }
      }
      $marsh = [];
      //   use console\helpers\FuncHelper;
      // var_dump($city_online[$route->city_id]);
      $json = false;
      if (isset($this->city_online_magic[$route->city_id])) {
      $json = \console\helpers\FuncHelper::getpos_magic($route, $this->city_online_magic[$route->city_id]);
      } elseif (isset($this->city_online[$route->city_id])) {
      $json = \console\helpers\FuncHelper::getpos($route, $this->city_online[$route->city_id]);
      }
      if ($json) {
      $route_line = Route::getMaproute($route->id);
      $l1 = json_decode($route_line[1]['line']);  /// попутаны маршрут на карте и последовательность остановок
      $l0 = json_decode($route_line[0]['line']);

      if (!$l1) {
      $l1 = [];
      }
      if (!$l0) {
      $l0 = [];
      }
      foreach ($l0 as $k => $l) {
      $l0[$k][] = 0;
      }
      foreach ($l1 as $k => $l) {
      $l1[$k][] = 0;
      }
      if ($stationsall[0]) {
      foreach ($stationsall[0] as $se) { // остановки встраиваем в маршрут
      $x0 = 1;
      $y0 = 1;
      $z0 = 1;
      $pos_insert = 0;
      foreach ($l0 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $se['y']);
      $y00 = abs((float) $l[1] - (float) $se['x']);
      $z00 = $x00 + $y00;
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }

      $l0 = array_merge(array_slice($l0, 0, $pos_insert),
      [array((float) $se['y'], (float) $se['x'], 1, $se)],
      array_slice($l0, $pos_insert, count($l0) - 1));
      }
      }
      if ($stationsall[1]) {
      foreach ($stationsall[1] as $se) { // остановки встраиваем в маршрут
      //  var_dump($se);
      $x0 = 1;
      $y0 = 1;
      $z0 = 1;
      $pos_insert = 0;
      foreach ($l1 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $se['y']);
      $y00 = abs((float) $l[1] - (float) $se['x']);
      $z00 = $x00 + $y00;
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }
      $l1 = array_merge(array_slice($l1, 0, $pos_insert, true),
      [array((float) $se['y'], (float) $se['x'], 1, $se)],
      array_slice($l1, $pos_insert, count($l1) - 1, true));
      }
      }
      //    var_dump($json->result); die();
      if (isset($json->result) && is_array($json->result)) {
      $count_online = count($json->result);
      foreach ($json->result as $a) {
      // var_dump($a); die();
      $x0 = 1;
      $y0 = 1;
      $z0 = 1;
      $pos_insert = 0;
      $mqw = 0;
      if ($stationsall[1][0]['name'] == $a->rl_firststation_title) {
      $mqw = 1;
      } else {
      $mqw = 0;
      }

      if (count($l0) > 0 AND count($l1) == 0) {
      $mqw = 0;
      }
      if ($mqw == 0) {
      foreach ($l0 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $a->u_lat);
      $y00 = abs((float) $l[1] - (float) $a->u_long);
      $z00 = $x00 + $y00;
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }
      if ($z0 < 0.001) {
      $l0 = array_merge(array_slice($l0, 0, $pos_insert, true),
      [array((float) $a->u_lat, (float) $a->u_long, 2, $a->u_statenum)],
      array_slice($l0, $pos_insert, count($l0) - 1, true));
      }
      } elseif ($mqw == 1) {
      foreach ($l1 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $a->u_lat);
      $y00 = abs((float) $l[1] - (float) $a->u_long);
      $z00 = $x00 + $y00;
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }
      if ($z0 < 0.001) {
      $l1 = array_merge(array_slice($l1, 0, $pos_insert, true),
      [array((float) $a->u_lat, (float) $a->u_long, 2, $a->u_statenum)],
      array_slice($l1, $pos_insert, count($l1) - 1, true));
      }
      } else {
      echo 'error 5655';
      }
      }
      } else {
      $count_online = 0;
      }
      foreach ($l0 as $kkk => $l) {
      if ($l[2] == 0) {
      unset($l0[$kkk]);
      }
      if (($l[2] == 1)) {
      $l0[$kkk][3] = $l0[$kkk][3]['id_station_rout'];
      }
      }
      foreach ($l1 as $kkk => $l) {
      if ($l[2] == 0) {
      unset($l1[$kkk]);
      }
      if (($l[2] == 1)) {
      $l1[$kkk][3] = $l1[$kkk][3]['id_station_rout'];
      }
      }//echo "<pre>"; var_dump($stationsall); die();
      if (count($l0) > 1) {
      $stationsall[0] = array_values($l0);
      }
      if (count($l1) > 1) {
      $stationsall[1] = array_values($l1);
      }
      echo json_encode(array_values($stationsall));
      die();
      }
      //  $return=$this->render('_getonline',['route'=>$route,'marsh'=>$marsh,'similar'=>$similar,'city'=>$city,'stations'=>$stations,'stationsall'=>$stationsall,'day_week' => $day_week,'count_online'=>$count_online]);
      }
      die('false');
      }

      public function actionGetmap2($route_id) {
      $post = Yii::$app->request->get();
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      $recaptcha_secret = '6LcL0rQcAAAAAHoOixrHODf_D-JGLDDT-Sme42M_';
      $recaptcha_response = $post['capfs'];

      // Отправляем POST запрос и декодируем результаты ответа
      $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
      $recaptcha = json_decode($recaptcha);
      // if ($_SERVER['REMOTE_ADDR']=='23.129.64.202') { var_dump($recaptcha->score);  die(); }
      if ((isset($recaptcha->score) AND $recaptcha->score >= 0.5) OR (Yii::$app->user->can('admin')) OR ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
      $cache = Yii::$app->cache;

      //$cache->flush();
      //$cache->delete('map'.$route_id);
      $cache_return = $cache->get('map' . $route_id);
      $cache_return = false; ////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
      // $cache_return->flushValues();
      if ($cache_return === false) {
      $route = Route::find()->where(['id' => $route_id])->one();
      $stationsall = $route->getStationRouteAll($route_id);
      if (count($stationsall) < 3) {
      $stations0 = ($stationsall[0]) ? $stationsall[0] : false; //$route->getStationRoute0($id);
      $stations1 = ($stationsall[1]) ? $stationsall[1] : false; //$route->getStationRoute1($id);
      } else {
      $stations0 = false;
      $stations1 = false;
      }

      // $route_line=Route::getMaproute($route_id);
      $city = City::find()->where(['id' => $route->city_id])->one();

      /////////////////////////////
      if (isset($this->city_online_magic[$route->city_id])) {
      $json = \console\helpers\FuncHelper::getpos_magic($route, $this->city_online_magic[$route->city_id]);
      } else {
      $json = \console\helpers\FuncHelper::getpos($route, $this->city_online[$route->city_id]);
      }
      $route_line = Route::getMaproute($route->id);
      $l0 = [];
      $l1 = [];
      $l0 = json_decode($route_line[0]['line']);
      $l1 = json_decode($route_line[1]['line']);

      if ($l0 && count($l0) > 0) {
      foreach ($l0 as $k => $l) {
      $l0[$k][] = 0;
      }
      }
      if ($l1 && count($l1) > 0) {
      foreach ($l1 as $k => $l) {
      $l1[$k][] = 0;
      }
      }


      if ($stationsall[0]) {
      foreach ($stationsall[0] as $se) { // остановки встраиваем в маршрут
      $x0 = 1;
      $y0 = 1;
      $z0 = 1;
      $pos_insert = 0;
      foreach ($l0 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $se['y']);
      $y00 = abs((float) $l[1] - (float) $se['x']);
      $z00 = $x00 + $y00;
      //if ($x00<$x0 AND $y00<$y0 AND $x00<0.01 AND $y00<0.01) { $pos_insert=$k; $x0=$x00; $y0=$y00;    }
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }

      //  var_dump($pos_insert,$x0,$y0);
      // echo "<pre>";
      if ($z0 < 0.005) {
      $l0 = array_merge(array_slice($l0, 0, $pos_insert),
      [array((float) $se['y'], (float) $se['x'], 1, $se['name'], $se['id'])],
      array_slice($l0, $pos_insert, count($l0) - 1));
      }
      //  var_dump($l0);  die();
      }
      }
      if ($stationsall[1]) {
      foreach ($stationsall[1] as $se) { // остановки встраиваем в маршрут
      $x0 = 1;
      $y0 = 1;
      $z0 = 1;
      $pos_insert = 0;
      foreach ($l1 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $se['y']);
      $y00 = abs((float) $l[1] - (float) $se['x']);
      $z00 = $x00 + $y00;
      // if ($x00<$x0 AND $y00<$y0 AND $x00<0.01 AND $y00<0.01) { $pos_insert=$k; $x0=$x00; $y0=$y00; }
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }
      if ($z0 < 0.005) {
      $l1 = array_merge(array_slice($l1, 0, $pos_insert, true),
      [array((float) $se['y'], (float) $se['x'], 1, $se['name'], $se['id'])],
      array_slice($l1, $pos_insert, count($l1) - 1, true));
      }
      }
      }
      //    var_dump($l0,$l1); die();
      if ($json && isset($json->result)) {
      foreach ($json->result as $a) {
      // var_dump($a); die();
      $x0 = 1;
      $y0 = 1;
      $z0 = 1;
      $pos_insert = 0;
      $mqw = 1;
      //var_dump($stationsall[0][0]['name'],$a->rl_firststation_title);echo "******";
      if (($l0 AND count($l0) > 0) AND ($l1 AND count($l1) > 0)) {
      if ($stationsall[1][0]['name'] == $a->rl_firststation_title) {
      $mqw = 1;
      } else {
      $mqw = 0;
      }
      } else {
      $mqw = 1;
      if ($l1 AND !$l0) {
      $l0 = $l1;
      $l1 = null;
      }
      }
      if ($l0) {
      foreach ($l0 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $a->u_lat);
      $y00 = abs((float) $l[1] - (float) $a->u_long);
      $z00 = $x00 + $y00;
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }
      }
      // var_dump($pos_insert, count($l0));
      if ($l1) {
      foreach ($l1 as $k => $l) {
      $x00 = abs((float) $l[0] - (float) $a->u_lat);
      $y00 = abs((float) $l[1] - (float) $a->u_long);
      $z00 = $x00 + $y00;
      if ($z00 < $z0) {
      $pos_insert = $k;
      $z0 = $z00;
      }
      }
      }


      ///////////////////// смена id автобуса на свой
      $m_id = 0;
      $command = (new \yii\db\Query())
      ->from('idtoid')
      ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
      ->createCommand()->queryOne();
      if ($command) {
      $m_id = $command['m_id'];
      } else {
      (new \yii\db\Query())->createCommand()->insert('idtoid', [
      'u_id' => $a->u_id,
      'route_id' => $route_id,
      ])->execute();
      $command = (new \yii\db\Query())
      ->from('idtoid')
      ->where(['u_id' => $a->u_id, 'route_id' => $route_id])
      ->createCommand()->queryOne();
      $m_id = $command['m_id'];
      }
      ///////////////////////
      if ($mqw == 0) {
      $l0 = array_merge(array_slice($l0, 0, $pos_insert, true),
      [array((float) $a->u_lat, (float) $a->u_long, 2, 0, $m_id)], // вместо 0 - $a->u_statenum
      array_slice($l0, $pos_insert, count($l0) - 1, true));
      } elseif ($mqw == 1) {
      $l1 = array_merge(array_slice($l1, 0, $pos_insert, true),
      [array((float) $a->u_lat, (float) $a->u_long, 2, 0, $m_id)], // вместо 0 - $a->u_statenum
      array_slice($l1, $pos_insert, count($l1) - 1, true));
      } else {
      echo 'error 5655';
      }
      }
      }


      //  array_multisort(array_column($l0, '0'), SORT_ASC, array_column($l0, '1'), SORT_ASC, $l0);
      //  array_multisort(array_column($l1, '0'), SORT_ASC, array_column($l1, '1'), SORT_ASC, $l1);
      $marsh[0] = $l0;
      $marsh[1] = $l1;
      //  echo "<pre>";var_dump($marsh); die();
      /////////////////////////////

      $return = $this->renderAjax('_mapnew', ['route_line' => $route_line, 'marsh' => $marsh, 'stationsall' => $stationsall, 'city' => $city]);
      $cache->set('map' . $route_id, $return, 1200000);
      return $return;
      } else {
      return $cache_return;
      }
      } else {
      throw new \yii\web\HttpException(403, 'Нет доступа.');
      }
      }

      public function actionGetmaps($station_id) {
      $cache = Yii::$app->cache;
      // $cache->flush();
      $cache_return = $cache->get('map_st3_' . $station_id);
      //$cache_return = false;
      if ($cache_return === false) {
      $stationmany = Stationmany::find()->where(['id' => $station_id])->one();
      $station = [];
      if ($stationmany) {
      foreach ($stationmany->stations as $st) {
      $station[] = Station::find()->where(['id' => $st->id])->one();
      }
      $city_id = $stationmany->city_id;
      } else {
      $station[] = Station::find()->where(['id' => $station_id])->one();
      $city_id = $station[0]->city_id;
      }
      $city = City::find()->where(['id' => $city_id])->one();
      $return = $this->renderAjax('_mapst', ['stations' => $station, 'city' => $city]);
      $cache->set('map_st3_' . $station_id, $return, 1200000);
      return $return;
      } else {
      return $cache_return;
      }
      }

      public function actionGetost($route_id, $ostan_ishod) {
      $s = Route::getStationRouteOld($route_id, $ostan_ishod);

      return $this->renderAjax('_getost', ['s' => $s]);
      }

      public function actionStationmany($id, $station = false, $city = false, $day_week = 0) {
      //  die('dssd');
      if ($station == false OR $city == false) {
      throw new \yii\web\HttpException(404, 'Не найдена');
      }
      // var_dump($day_week); die();
      //var_dump($station->stations); die();

      $pjax_z = 0;
      $post = Yii::$app->request->get();
      if (isset($post['day_week']) AND !isset($post['_pjax'])) {
      $url = explode("?", $_SERVER['REQUEST_URI']);
      Yii::$app->response->redirect("https://" . $_SERVER['SERVER_NAME'] . $url[0], 301)->send();
      Yii::$app->end();
      return;
      } elseif (isset($post['_pjax'])) {
      $pjax_z = 1;
      }

      if ($day_week == 0) { // определяем текущий день недели, и выводим расписание для него. Если этого дня недели нет в расписании то ближайший день недели
      $day_week = date('w');
      }

      $cache = Yii::$app->cache;
      // $cache->flush();
      $cache_return = $cache->get('stationmany2_' . $station->id . "_" . $day_week . "_" . $pjax_z);
      if ($_SERVER['REMOTE_ADDR'] == '5.187.71.244') {
      //var_dump('station_'.$station->id."_".$day_week."_".$pjax_z,$cache_return); die('453');
      $cache_return = false;
      }

      if ($cache_return === false) {

      $all_stations = $station->stations;
      $routes = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => []];
      foreach ($all_stations as $st) {
      $r = Station::getRoutesByStation($st->id);

      for ($ie = 1; $ie <= 5; $ie++) {
      if (isset($r[$ie])) {
      $routes[$ie] = $routes[$ie] + $r[$ie]; //array_merge($routes[$ie],$r[$ie]);
      }
      }
      }

      $return = $this->render('stationmany', ['station' => $station, 'city' => $city, 'routes' => $routes, 'day_week' => $day_week]);
      $cache->set('stationmany2_' . $station->id . "_" . $day_week . "_" . $pjax_z, $return, 600000);
      } else {
      $return = $cache_return;
      }
      return $return;
      }

      public function actionStation($id, $station, $city, $day_week = 0) {
      if ($day_week == '999') {
      die();
      $array_st = [];
      $stations = Station::find()->where("id>64999 AND id<85000 AND inmany=0")->all();
      foreach ($stations as $s) {
      if (!in_array($s->id, $array_st)) {
      $s->name = str_replace("'", "\'", $s->name);
      $station_all = Station::find()->where("name='" . $s->name . "' AND city_id='" . $s->city_id . "' AND inmany=0")->all();
      $stationmany = new Stationmany;
      $stationmany->name = $s->name;
      $stationmany->alias = $s->alias;
      $stationmany->city_id = $s->city_id;
      $stationmany->save();
      $stationmany->refresh();
      foreach ($station_all as $value) {
      (new \yii\db\Query())->createCommand()->insert('stationmany_station', [
      'stationmany_id' => $stationmany->id,
      'station_id' => $value->id,
      ])->execute();
      $value->inmany = 1;
      $value->save();
      $array_st[] = $value->id; // записываем уже обработаные остановки
      }
      }
      }
      die('123');
      }





      /////////////////

      $pjax_z = 0;
      $post = Yii::$app->request->get();
      if (isset($post['day_week']) AND !isset($post['_pjax'])) {
      $url = explode("?", $_SERVER['REQUEST_URI']);
      Yii::$app->response->redirect("https://" . $_SERVER['SERVER_NAME'] . $url[0], 301)->send();
      Yii::$app->end();
      return;
      } elseif (isset($post['_pjax'])) {
      $pjax_z = 1;
      }

      if ($day_week == 0) { // определяем текущий день недели, и выводим расписание для него. Если этого дня недели нет в расписании то ближайший день недели
      $day_week = date('w');
      }

      $cache = Yii::$app->cache;
      // $cache->flush();
      $cache_return = $cache->get('station_' . $station->id . "_" . $day_week . "_" . $pjax_z);

      if ($cache_return === false) {
      $routes = Station::getRoutesByStation($station->id);
      $return = $this->render('station', ['station' => $station, 'city' => $city, 'routes' => $routes, 'day_week' => $day_week]);
      $cache->set('station_' . $station->id . "_" . $day_week . "_" . $pjax_z, $return, 600000);
      } else {
      $return = $cache_return;
      }
      return $return;
      }


      public function actionLogin() {
      die();
      //throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////


      $user = new User();
      $user->username = 'editor27';
      $user->email = 'editor27@goonbus.ru';
      $user->password = 'gs3FH4dger';
      $user->status = '10';
      $user->generateAuthKey();
      $user->save();
      $userRole = Yii::$app->authManager->getRole('editor');
      Yii::$app->authManager->assign($userRole, $user->id);
      echo 'good';
      die();
      // }

      if (!Yii::$app->user->isGuest) {
      return $this->goHome();
      }

      die('ffff');
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


      public function actionLogout() {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      Yii::$app->user->logout();

      return $this->goHome();
      }


      public function actionContact() {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      $model = new ContactForm();
      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
      Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
      } else {
      Yii::$app->session->setFlash('error', 'There was an error sending your message.');
      }

      return $this->refresh();
      } else {
      return $this->render('contact', [
      'model' => $model,
      ]);
      }
      }


      public function actionAbout() {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      return $this->render('about');
      }


      public function actionSignup() {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      $model = new SignupForm();
      if ($model->load(Yii::$app->request->post()) && $model->signup()) {
      Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
      return $this->goHome();
      }

      return $this->render('signup', [
      'model' => $model,
      ]);
      }


      public function actionRequestPasswordReset() {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      $model = new PasswordResetRequestForm();
      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      if ($model->sendEmail()) {
      Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

      return $this->goHome();
      } else {
      Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
      }
      }

      return $this->render('requestPasswordResetToken', [
      'model' => $model,
      ]);
      }


      public function actionResetPassword($token) {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      try {
      $model = new ResetPasswordForm($token);
      } catch (InvalidArgumentException $e) {
      throw new BadRequestHttpException($e->getMessage());
      }

      if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
      Yii::$app->session->setFlash('success', 'New password saved.');

      return $this->goHome();
      }

      return $this->render('resetPassword', [
      'model' => $model,
      ]);
      }


      public function actionVerifyEmail($token) {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      try {
      $model = new VerifyEmailForm($token);
      } catch (InvalidArgumentException $e) {
      throw new BadRequestHttpException($e->getMessage());
      }
      if ($user = $model->verifyEmail()) {
      if (Yii::$app->user->login($user)) {
      Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
      return $this->goHome();
      }
      }

      Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
      return $this->goHome();
      }


      public function actionResendVerificationEmail() {
      throw new \yii\web\NotFoundHttpException('Страница не найдена'); //////////////////////////////////////////////////////////////////
      $model = new ResendVerificationEmailForm();
      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      if ($model->sendEmail()) {
      Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
      return $this->goHome();
      }
      Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
      }

      return $this->render('resendVerificationEmail', [
      'model' => $model
      ]);
      } */
}
