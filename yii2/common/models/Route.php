<?php

namespace common\models;

use Yii;
use common\models\Station;
use app\models\RouteStep;
use yii\helpers\Url;
use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\RouteRedirect;
use common\helpers\TimeHelper;
use yii\db\Expression;

/**
 * This is the model class for table "route".
 *
 * @property int $id
 * @property string $name
 * @property string $number
 * @property string $alias
 * @property int $city_id
 * @property string $price
 * @property int $type_transport 1-автобус,2-троллейбус, 3-трамвай
 * @property string $organization
 * @property string $info
 * @property string $time_work
 * @property string $route_text
 * @property int $type_day 1- буд-вых, 2 - кажд день, 3 - одно на все дни, 4 - будни-суб-вс
 * @property int $temp_route_id
 */
class Route extends ActiveRecord {

    public $redirect;
    public $source;

    public function behaviors() {
        return [
            'sitemap' => [
                'class' => SitemapBehavior::className(),
                'scope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->select(['alias', 'id', 'lastmod']);
                    $model->where("(version=1 OR version=4) AND active=1 AND alias<>'' AND city_id=".Yii::$app->params['city_id']);//andWhere(["active" => 1])->andWhere(["!=", "alias", "''"])->andWhere(["==", "city_id", Yii::$app->params['city_id']]);
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    return [
                'loc' => Url::toRoute(['/site/route', 'id' => $model->id], true), //Url::to($model->url, true),
                'lastmod' => strtotime($model->lastmod), //strtotime(date("H:i d-m-Y", microtime(true)-(rand(1,15000)+60*60*rand(70, 8000)))),
                'changefreq' => SitemapBehavior::CHANGEFREQ_WEEKLY,
                'priority' => 0.8
                    ];
                }
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'lastmod',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'lastmod',
                ],
                'value' => function () {
                    return date('Y-m-d H:i:s');
                } // unix timestamp },
            ],
        ];
    }

    /*   public function afterSave($insert, $changedAttributes)
      {
      if ($insert) {
      Yii::$app->session->setFlash('success', 'Маршрут добавлен');
      } else {
      Yii::$app->session->setFlash('success', 'Маршрут обновлен');
      }
      parent::afterSave($insert, $changedAttributes);
      } */

    public function afterSave($insert, $changedAttributes) {
        if (!Yii::$app->request->isConsoleRequest) {
            if ($insert) {
                Yii::$app->session->setFlash('success', 'Маршрут добавлен');
            } else {
                Yii::$app->session->setFlash('success', 'Маршрут обновлен');
            }
        }
        parent::afterSave($insert, $changedAttributes);
        $model = RouteRedirect::findOne(['route_id' => $this->id]);
        if ($this->redirect == '') {
            if ($model) {
                $model->delete();
            }
        } else {
            if (!$model) {
                $model = new RouteRedirect();
                $model->route_id = $this->id;
            }
            $model->url = $this->redirect;
            $model->save();
        }
        $model = RouteSource::findOne(['route_id' => $this->id]);
        if ($this->source == '') {
            //$model->delete();
        } else {
            if (!$model) {
                $model = new RouteSource();
                $model->route_id = $this->id;
            }
            $model->text = $this->source;
            // var_dump($model);
            $model->save();
            //var_dump($model->errors);
        }
    }

    public function beforeSave($insert) {

        if ($this->type_day == NULL) {
            $this->type_day = 3;
        } // Одно расписание на все
        //  return false;
        // var_dump($insert,$this->alias); die();
        if (isset($this->id) AND $this->id != '') {
            $ar = " AND id<>" . $this->id;
        } else {
            $ar = '';
        }
        if ($this->alias != 'none') {
            $find = Route::find()->where("alias='" . $this->alias . "' AND city_id=" . $this->city_id . $ar)->count();
            // var_dump($insert,$this->alias); die();
            if ($find > 0) {
                $this->alias = $this->alias . "-" . $this->number;
                $find = Route::find()->where(['alias' => $this->alias, 'city_id' => $this->city_id])->count();
                if ($find > 0) {
                    $this->alias = $this->alias . "-" . $this->number . "-" . $find;
                }
                $this->updateAttributes(['alias']);
            }
        }
        if ($this->alias == 'none' AND $this->name != 'none') {
            $string = $this->name;
            // $string = iconv('windows-1251', 'utf-8', $string);
            $string = mb_strtolower($string, 'UTF-8');
            $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
            $string = strip_tags($string);
            $string = preg_replace('/[\r\n\t]+/', ' ', $string);

            $table = [
                'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g',
                'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
                'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k',
                'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
                'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
                'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
                'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh', 'ь' => '',
                'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu',
                'я' => 'ya', ' ' => '-', '«' => "", '»' => "", '№' => ""
            ];

            $output = str_replace(
                    array_keys($table),
                    array_values($table), $string
            );

            $output = str_replace("--", "-", $output);
            $output = str_replace("--", "-", $output);
            $output = str_replace("--", "-", $output);
            $output = str_replace("--", "-", $output);

            $output = preg_replace('/|[^\w-]+|/u', '', $output); //var_dump($output);
            // $output = preg_replace('/[\x00-\x1F\x7F]/u', '', $output);

            $output = str_replace("--", "-", $output);
            $output = str_replace("--", "-", $output);

            $output = trim($output, "-");
            /* $output2= explode('-', $output);
              $output3=[];
              //$output='';
              foreach ($output2 as $o) {
              if (strlen($o)>1) {
              $output3[]=$o;
              }
              }
              $output= implode('-', $output3); */
            $find = Route::find()->where(['name' => $this->name, 'city_id' => $this->city_id])->count();
            // var_dump($find); die();
            if ($find < 2) {
                $this->alias = $output;
            } else {
                $this->alias = $output . "-" . $find;
            }
            $this->updateAttributes(['alias']);
        }
        parent::beforeSave($insert);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'route';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'number', 'city_id', 'type_transport', 'type_direction', 'active'], 'required'],
            [['id', 'city_id', 'type_transport', 'type_day', 'season', 'type_direction', 'version', 'user_id', 'views'], 'integer'],
            [['info', 'time_work', 'route_text', 'temp_route_id'], 'string'],
            [['name', 'alias', 'price', 'organization'], 'string', 'max' => 255],
            [['number'], 'string', 'max' => 50],
            [['redirect', 'source', 'lastmod'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название маршрута',
            'number' => 'Номер',
            'alias' => 'Адрес',
            'city_id' => 'Город',
            'price' => 'Стоимость проезда (руб.)',
            'type_transport' => 'Тип транспорта',
            'type_direction' => 'Тип направления',
            'organization' => 'Организация',
            'info' => 'Доп. информация',
            'time_work' => 'Время работы и интервалы',
            'route_text' => 'Маршрут следования',
            'type_day' => 'Тип расписания по дням',
            'season' => 'Сезонный маршрут',
            'temp_route_id' => 'Temp Route ID',
            'active' => 'Действующий',
            'version' => 'Версия',
            'redirect' => 'Редирект',
            'source' => 'Источники',
            'lastmod' => 'Дата редактирования',
            'has_time' => 'Есть расписание'
        ];
    }

    public function getRouteredirect() {
        return $this->hasOne(RouteRedirect::className(), ['route_id' => 'id']);
    }

    public function getRoutesource() {
        return $this->hasOne(RouteSource::className(), ['route_id' => 'id']);
    }

    /*  public function afterFind() {
      parent::afterFind();
      if ($this->active==0) {
      if (isset($this->routeredirect->url)) {
      $this->redirect= $this->routeredirect->url;
      } else {
      $this->redirect=false;
      }
      }
      if (isset($this->routesource->text)) {
      $this->source= $this->routesource->text;
      } else {
      $this->source=false;
      }
      return;

      } */

    public function getEditstatus() {
        $q = 'SELECT * FROM route_step WHERE route_id=' . $this->id . ' AND status=0 AND user_id=' . Yii::$app->user->id;
        $row = Yii::$app->db->createCommand($q)->queryOne();
        return $row;
    }

    /*   public static function getTypeversionList() {
      return ['-1'=>'С картой','1'=>'Без карты'];
      } */

    public function deleteRoute() {
        /* Yii::$app->db->createCommand("DELETE FROM items_old WHERE id='".$this->time_work."'")->query();
          Yii::$app->db->createCommand("DELETE FROM map_rout WHERE route_id='".$this->id."'")->query();
          Yii::$app->db->createCommand("DELETE FROM station_rout WHERE route_id='".$this->id."'")->query();
          Yii::$app->db->createCommand("DELETE FROM marshruts WHERE itemID='".$this->id."'")->query();
          Yii::$app->db->createCommand("DELETE FROM route_step WHERE route_id='".$this->id."'")->query();

         */
        if (is_int($this->time_work)) {
            $q1 = Yii::$app->db->createCommand("DELETE FROM items_old WHERE id='" . $this->time_work . "'")->query();
        }
        $q2 = Yii::$app->db->createCommand("DELETE FROM map_rout WHERE route_id='" . $this->id . "'")->query();
        $q3 = Yii::$app->db->createCommand("DELETE FROM marshruts WHERE itemID='" . $this->id . "'")->query();
        $q4 = Yii::$app->db->createCommand("DELETE FROM route_messages WHERE route_id='" . $this->id . "'")->query();
        $q5 = Yii::$app->db->createCommand("DELETE FROM route_redirect WHERE route_id='" . $this->id . "'")->query();
        $q6 = Yii::$app->db->createCommand("DELETE FROM route_source WHERE route_id='" . $this->id . "'")->query();
        $q7 = Yii::$app->db->createCommand("DELETE FROM route_step WHERE route_id='" . $this->id . "'")->query();
        $q8 = Yii::$app->db->createCommand("DELETE FROM search_string WHERE route_id='" . $this->id . "'")->query();
        $q9 = Yii::$app->db->createCommand("DELETE FROM similar_route WHERE route_id='" . $this->id . "' OR similar_route_id='" . $this->id . "'")->query();
        $this->deleteStationRout();
        $q10 = Yii::$app->db->createCommand("DELETE FROM route WHERE id='" . $this->id . "'")->query();
        // var_dump($q1,$q2,$q3,$q4,$q5,$q6,$q7,$q8,$q9,$q10); die();
        return true;
    }

    public function addRedirect($redirect_to, $redirect_from) {
        $q = "INSERT INTO `route_redirect2` (`id`, `from_url`,`to_url`,`date`) "
                . "VALUES (NULL, '" . $redirect_from . "', '" . $redirect_to . "', NOW());";
        $insert = Yii::$app->db->createCommand($q)->query();
        return true;
    }

    public function delRedirect() {
        $q = "DELETE FROM route_redirect WHERE route_id='" . $this->id . "'";
        Yii::$app->db->createCommand($q)->query();
        return true;
    }

    public function setStationRout($station_id, $direction, $order_st, $style = '', $key_day = '1111111') {
        $route_id = $this->id;
        $row = Yii::$app->db->createCommand('SELECT * FROM station_rout WHERE route_id=' . $route_id . ' AND station_id=' . $station_id . ' AND direction=' . $direction)->queryOne();
        if ($row) {
            Yii::$app->db->createCommand()->update('station_rout', ['order_st' => $order_st, 'style' => $style, 'key_day' => $key_day], ["route_id" => $route_id, "station_id" => $station_id, "direction" => $direction])->execute();
            return $row['id'];
        } else {
            $q = "INSERT INTO `station_rout`(`id`, `route_id`, `station_id`, `direction`, `order_st`, `style`, `key_day`) "
                    . "VALUES (NULL,'" . $route_id . "','" . $station_id . "','" . $direction . "','" . $order_st . "','" . $style . "','" . $key_day . "')";
            $insert = Yii::$app->db->createCommand($q)->query();

            return Yii::$app->db->getLastInsertID();
        }
    }

    public function getStationRout($station_id, $direction) {
        $route_id = $this->id;
        $row = Yii::$app->db->createCommand('SELECT * FROM station_rout WHERE route_id=' . $route_id . ' AND station_id=' . $station_id . ' AND direction=' . $direction . '')->queryOne();
        //var_dump($row);
        if (isset($row['id'])) {
            $id = $row['id'];
        } else {
            $id = false;
        }
        return $id;
    }

    public function deleteStationRout() {
        $route_id = $this->id;

        $rows_all_route = Yii::$app->db->createCommand("SELECT id FROM station_rout WHERE route_id=" . $route_id)->queryAll(); //все направления маршрута
        foreach ($rows_all_route as $rar) {
            $row = Yii::$app->db->createCommand('DELETE FROM time_work WHERE station_rout_id=' . $rar['id'] . '')->query(); // удаляем все время направления
        }
        $row = Yii::$app->db->createCommand('DELETE FROM station_rout WHERE route_id=' . $route_id . '')->query();
        //var_dump($row);
        return $row;
    }

    public function setTimeWork($station_rout_id, $arr_day) {
        $q = "INSERT INTO `time_work` (`station_rout_id`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`) "
                . "VALUES ($station_rout_id, '" . $arr_day[0] . "', '" . $arr_day[1] . "', '" . $arr_day[2] . "', '" . $arr_day[3] . "', '" . $arr_day[4] . "', '" . $arr_day[5] . "', '" . $arr_day[6] . "');";
        $insert = Yii::$app->db->createCommand($q)->query();
        return $station_rout_id;
    }

    public function setMapRout($line, $direction) {
        $route_id = $this->id;
        $line_j = json_encode($line);
        $insert = Yii::$app->db->createCommand("INSERT INTO `map_rout`(`id`, `route_id`, `line`, `direction`, `active`) "
                        . "VALUES (NULL,'" . $route_id . "','" . $line_j . "','" . $direction . "','1')")->query();
    }

    public function getVersionname() {
        $list = self::getVersionList();
        //var_dump($this->version);
        return $list[$this->version];
    }

    public static function getTypetransportList() {
        return ['1' => 'Автобус', '2' => 'Троллейбус', '3' => 'Трамвай', '4' => 'Маршрутка', '5' => 'Электричка', '6' => 'Речной транспорт', '7' => 'Канатная дорога'];
    }

    public static function getTypedirectionList() {
        return ['1' => 'Городской', '2' => 'Пригородный', '3' => 'Междугородний'];
    }

    public static function getVersionList() {
        return ['1' => 'Новые с картой', '2' => 'Старые без карты', '3' => 'Новые без карты', '4' => 'Старые с картой'];
    }

    public static function getYesList() {
        return ['1' => 'Да', '0' => 'Нет', NULL => 'Все'];
    }

    public static function getTypedayList() {
        return ['1' => 'Будни/Выходные', '2' => 'На каждый день', '3' => 'Одно на все', '4' => 'Будни/Сб/Вс', '5' => 'Будни', '6' => 'Выходные', '7' => 'Будни+Сб/Вс', '8' => 'Будни/Сб',
            '9' => 'Будни/Пт/Выходные', '10' => 'Вс/Сб', '11' => 'Пн-Чт/Пт/Сб', '12' => 'Будни/Пт/Сб/Вс'];
    }

    public static function getTypedayFromList($type) {
        // $list=self::getTypedayList();
        $arr = [];
        if ($type == 1) {
            $arr = [1 => 'Будни', 6 => 'Выходные'];
        } elseif ($type == 2) {
            $arr = [1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'];
        } elseif ($type == 3) {
            $arr = [1 => 'Расписание'];
        } elseif ($type == 4) {
            $arr = [1 => 'Будни', 6 => 'Суббота', 7 => 'Воскресенье'];
        } elseif ($type == 5) {
            $arr = [1 => 'Будни'];
        } elseif ($type == 6) {
            $arr = [6 => 'Выходные'];
        } elseif ($type == 7) {
            $arr = [1 => 'Будни и Суббота', 7 => 'Воскресенье'];
        } elseif ($type == 8) {
            $arr = [1 => 'Будни', 6 => 'Суббота'];
        } elseif ($type == 9) {
            $arr = [1 => 'Будни', 5 => 'Пятница', 6 => 'Выходные'];
        } elseif ($type == 10) {
            $arr = [6 => 'Суббота', 7 => 'Воскресенье'];
        } elseif ($type == 11) {
            $arr = [1 => 'Будни', 5 => 'Пятница', 6 => 'Суббота'];
        } elseif ($type == 12) {
            $arr = [1 => 'Будни', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'];
        }
        return $arr;
    }

    public static function getActiveList() {
        return ['1' => 'Действует', '0' => 'Не действует', NULL => ''];
    }

    public function getTypetransport() {
        if (!isset($this->type_transport)) {
            $this->type_transport = 1;
        }
        $list = self::getTypetransportList();
        return $list[$this->type_transport];
    }

    public function getTypedirection() {
        if (!isset($this->type_direction)) {
            $this->type_direction = 1;
        }
        $list = self::getTypedirectionList();
        return $list[$this->type_direction];
    }

    public function getCityname() {
        if (!isset($this->city_id)) {
            return false;
        }
        $list = City::getCities();
        return $list[$this->city_id];
    }

    public function getStations() {
        return $this->hasMany(Station::className(), ['id' => 'station_id'])
                        ->viaTable('station_rout', ['route_id' => 'id']);
    }

    public function getCity() {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getStationRoute0($route_id) {

        $rows = Yii::$app->db->createCommand('SELECT s.*,sr.id as id_station_rout, sr.style, sr.order_st, sr.key_day FROM station as s, station_rout as sr WHERE sr.route_id=' . $route_id . ' AND s.id=sr.station_id AND sr.direction=0 ORDER by sr.id')->queryAll();
        return $rows;
    }

    public function getStationRoute1($route_id) {

        $rows = Yii::$app->db->createCommand('SELECT s.*,sr.id as id_station_rout, sr.style, sr.order_st, sr.key_day FROM station as s, station_rout as sr WHERE sr.route_id=' . $route_id . ' AND s.id=sr.station_id AND sr.direction=1 ORDER by sr.id')->queryAll();
        return $rows;
    }

    public static function getStationRouteAll($route_id) {
        $rows = Yii::$app->db->createCommand('SELECT direction FROM station_rout WHERE route_id=' . $route_id . ' GROUP BY direction')->queryAll();
        // var_dump($rows); die();
        $all = [];
        foreach ($rows as $r) {
            $all[$r['direction']] = Yii::$app->db->createCommand('SELECT s.*,sr.id as id_station_rout, sr.style, sr.order_st, sr.key_day FROM station as s, station_rout as sr WHERE sr.route_id=' . $route_id . ' AND s.id=sr.station_id AND sr.direction=' . $r['direction'] . ' ORDER by sr.id')->queryAll();
        }
        $all = array_values($all);

        return $all;
    }

    public static function getStationRouteOne($id_station_rout) {

        $row = Yii::$app->db->createCommand('SELECT s.*,sr.id as id_station_rout, r.type_day, r.city_id FROM station as s, station_rout as sr, route as r WHERE sr.id=' . $id_station_rout . ' AND sr.station_id=s.id AND r.id=sr.route_id')->queryOne();
        //var_dump($row);
        if (!$row) {
            return null;
        }
        return $row;
    }

    public static function getStationRouteOld($route_id, $ostan_ishod) {
        $ostan_ishod = urldecode($ostan_ishod);
        $city = Yii::$app->db->createCommand('SELECT * FROM route as r, city as c WHERE r.city_id=c.id AND r.id=' . $route_id . '')->queryOne();

        $rows_route = Yii::$app->db->createCommand("SELECT r.*,m.value FROM route as r, marshruts as m WHERE r.temp_route_id<>'-1' AND r.city_id=" . $city['id'] . " AND r.id<>" . $route_id . " AND r.active=1 AND r.id=m.itemID")->queryAll();
        $pohozhie = [];
        $i = 0;
        //foreach ($rows_route as $key => $value) {
        $x = 0;
        while ($i <= 6 AND count($rows_route) > $x) {

            $all_ostan = json_decode($rows_route[$x]['value']);
            if (is_array($all_ostan)) {
                foreach ($all_ostan as $ostan) {
                    $q1 = str_replace(" ", "", $ostan_ishod);
                    $q2 = str_replace(" ", "", $ostan[0]);
                    if ($q1 == $q2) {
                        if (!isset($pohozhie[$rows_route[$x]['id']])) {
                            $pohozhie[$rows_route[$x]['id']] = $rows_route[$x];
                            $i++;
                        }
                    }
                }
            }
            $x++;
        }
        return $pohozhie;
    }

    public static function getRoutesSimilarone($route, $city_id) { // запрос самого похожего маршрута
        $num = preg_replace('/[^0-9]/', '', $route->number);
        $row = Yii::$app->db->createCommand("SELECT id FROM route WHERE number='" . $num . "' AND id<>" . $route->id . " AND active=1 AND city_id=" . $city_id)->queryAll(); //все маршруты города
        return $row;
    }

    public static function getRoutesSimilar($route_id, $city_id) { // запрос похожих маршрутов
        $rows = Yii::$app->db->createCommand('SELECT r.*,sr.date FROM similar_route as sr, route as r WHERE sr.route_id=' . $route_id . ' AND sr.similar_route_id=r.id')->queryAll();
        if (count($rows) > 0 AND (strtotime($rows[0]['date']) > (time() - 5000000))) { // похожие свежие
        } else { // обновить похожие
            $del = Yii::$app->db->createCommand('DELETE FROM similar_route WHERE route_id=' . $route_id . '')->query();
            $rows_route = Yii::$app->db->createCommand('SELECT station_id FROM station_rout WHERE route_id=' . $route_id . '')->queryColumn(); //все остановки маршрута(к которому ищем похожести)

            $rows_all_route = Yii::$app->db->createCommand("SELECT id FROM route WHERE id<>" . $route_id . " AND active=1 AND temp_route_id<>'-1' AND city_id=" . $city_id)->queryAll(); //все маршруты города
            $rows_route_count = count($rows_route);
            if ($rows_route_count == 0) {
                return [];
            }
            $q = 0;
            foreach ($rows_all_route as $rar) {
                $rows_route_rar = Yii::$app->db->createCommand('SELECT station_id FROM station_rout WHERE route_id=' . $rar['id'] . '')->queryColumn();
                $result = array_intersect($rows_route, $rows_route_rar);
                $count_similar = count($result);
                $perc_similar = $count_similar * 100 / $rows_route_count; //var_dump($perc_similar);
                if ($perc_similar > 24) { ////////// ПРОЦЕНТ СОВПАДЕНИЯ
                    //     $del = Yii::$app->db->createCommand('DELETE FROM similar_route WHERE route_id='.$route_id.' AND similar_route_id='.$rar['id'])->query();
                    $insert = Yii::$app->db->createCommand("INSERT INTO `similar_route`(`id`, `route_id`, `similar_route_id`, `rate_similar`, `date`) "
                                    . "VALUES (NULL,'" . $route_id . "','" . $rar['id'] . "','" . (int) $perc_similar . "',NOW())")->query();
                    $q++;
                }
            }
            if ($q == 0) {

                for ($i = 0; $i < 6; $i++) {
                    $c = rand(0, $rows_route_count - 1);
                    if (isset($rows_all_route[$c])) {
                        $insert = Yii::$app->db->createCommand("INSERT INTO `similar_route`(`id`, `route_id`, `similar_route_id`, `rate_similar`, `date`) "
                                        . "VALUES (NULL,'" . $route_id . "','" . $rows_all_route[$c]['id'] . "','50',NOW())")->query();
                        empty($rows_all_route[$c]);
                    }
                }
            }
        }
        $rows_all = Yii::$app->db->createCommand('SELECT r.*,sr.rate_similar FROM route as r, similar_route as sr WHERE r.active=1 AND sr.similar_route_id=r.id AND sr.route_id=' . $route_id)->queryAll();
        return $rows_all;
    }

    public static function getRoutesSimilar2($route_id, $city_id) { // запрос похожих маршрутов, для старой версии маршрутов 
        $rows = Yii::$app->db->createCommand('SELECT r.*,sr.date FROM similar_route as sr, route as r WHERE sr.route_id=' . $route_id . ' AND sr.similar_route_id=r.id')->queryAll();
        if (count($rows) > 0 AND (strtotime($rows[0]['date']) > (time() - 5000000))) { // похожие свежие
        } else { // обновить похожие
            $del = Yii::$app->db->createCommand('DELETE FROM similar_route WHERE route_id=' . $route_id . '')->query();
            $route_ost = Yii::$app->db->createCommand('SELECT m.value FROM route as r, marshruts as m WHERE r.id=' . $route_id . ' AND r.id=m.itemID')->queryOne();
            if (isset($route_ost['value'])) {
                $all = json_decode($route_ost['value']);
            } else {
                $all = false;
            }
            $rows_route = Yii::$app->db->createCommand('SELECT r.*,m.value FROM route as r, marshruts as m WHERE r.city_id=' . $city_id . ' AND r.id<>' . $route_id . ' AND r.active=1 AND r.id=m.itemID')->queryAll(); //все остановки маршрута(к которому ищем похожести)
            $pohozhie = [];

            foreach ($rows_route as $key => $value) {
                //  $value['value'] = fixJSON($value['value']);echo "<pre>"; var_dump($value['value']); die();
                $all_ostan = json_decode($value['value']);
                if (!$all_ostan OR !$all) { // Записать ошибку, json кривой с кавычками
                    /*   $insert = Yii::$app->db->createCommand("INSERT INTO `error`(`id`, `type_error`, `text`) "
                      . "VALUES (NULL,'json kavychki','".$value['id']."')")->query(); */
                } else {
                    $i = 0;
                    foreach ($all_ostan as $ostan) {
                        foreach ($all as $ostan_ishod) {
                            $q1 = str_replace(" ", "", $ostan_ishod[0]);
                            $q2 = str_replace(" ", "", $ostan[0]);

                            if ($q1 == $q2) {
                                $i++;
                            }
                        }
                    }
                    if ($i > 0) {
                        $value['pohozhie'] = $i;
                        $pohozhie[] = $value;
                    }
                }
            }

            function cmp1($a, $b) {
                if ($a['pohozhie'] == $b['pohozhie']) {
                    return 0;
                }
                return ($a['pohozhie'] > $b['pohozhie']) ? -1 : 1;
            }

            usort($pohozhie, "common\models\cmp1");

            for ($i = 0; ($i < 10 AND isset($pohozhie[$i])); $i++) {
                // echo "<pre>"; var_dump($i); die();

                $insert = Yii::$app->db->createCommand("INSERT INTO `similar_route`(`id`, `route_id`, `similar_route_id`, `rate_similar`, `date`) "
                                . "VALUES (NULL,'" . $route_id . "','" . $pohozhie[$i]['id'] . "','" . (int) $pohozhie[$i]['pohozhie'] . "',NOW())")->query();
            }
        }
        $rows_all = Yii::$app->db->createCommand('SELECT r.*,sr.rate_similar FROM route as r, similar_route as sr WHERE r.active=1 AND sr.similar_route_id=r.id AND sr.route_id=' . $route_id)->queryAll();
        //   var_dump($rows_all); die();
        return $rows_all;
    }

    public static function getMaproute($route_id) {
        $rows = Yii::$app->db->createCommand('SELECT * FROM map_rout WHERE route_id=' . $route_id . '')->queryAll();
        if (count($rows) == 1) {
            $rows = array_values($rows);
        } // если нет маршрута туда
        return $rows;
    }

    public function getMarshrut() {
        if (isset($this->id)) {
            $route_id = $this->id;
            $q = 'SELECT m.value FROM route as r, marshruts as m  WHERE r.id=' . $route_id . ' AND r.id=m.itemID';
            $row = Yii::$app->db->createCommand($q)->queryOne();
            if (isset($row['value'])) {
                $all = json_decode($row['value']);
            } else {
                $all = [];
            }
        } else {
            $all = [];
        }
        return $all;
    }

    public function setIncViews() {
        if (isset($this->id)) {
            Yii::$app->db->createCommand()->update('route', ['views' => new Expression('(views+1)')], "id = " . $this->id)->execute();
        } else {
            return false;
        }
    }

    public function getExtraFields() {
        $all = [];
        if (isset($this->id)) {
            $q = 'SELECT i.extra_fields FROM route as r, items_old as i  WHERE r.id=' . $this->id . ' AND r.time_work=i.id';
            $row = Yii::$app->db->createCommand($q)->queryOne();
            if ($row) {
                $all = json_decode($row['extra_fields']);
            }
            //var_dump($q); die();
        }
        return $all;
    }

    public static function setRoutemessage($route_id, $text) {

        // $insert = Yii::$app->db->createCommand("INSERT INTO `route_messages`(`id`, `route_id`, `text`, `photo`, `date`) "
        //        . "VALUES (NULL,".$route_id.",'".$text."','',NOW())")->query();
        $date = date('Y-m-d H:i:s');
        \Yii::$app->db->createCommand()->insert('route_messages', [
            'route_id' => $route_id,
            'text' => $text,
            'date' => $date
        ])->execute();

        return true;
    }

    public function save_good_2_3($post, $model_name) {
        // var_dump($this->id); die();
        $marshruts_value = RouteStep::getMarshrutfromPost($post);
        /* if (isset($this->id)) {
          $id=$this->id;
          } else {

          } */
        // $isExists = User::find()->where(['id' => 2])->exists(); 
        $row = Yii::$app->db->createCommand("SELECT id FROM marshruts WHERE itemID=" . $this->id)->queryOne();
        //  $insert = Yii::$app->db->createCommand("UPDATE `marshruts` SET `value` = '".$marshruts_value."' WHERE `itemID` = '".$this->id."';")->query();
        if ($row) {
            Yii::$app->db->createCommand()->update('marshruts', ['value' => $marshruts_value], "itemID = $this->id")->execute();
        } else {
            Yii::$app->db->createCommand()->insert('marshruts', ['value' => $marshruts_value, 'itemID' => $this->id])->execute();
        }

        $post[$model_name]['id'] = $this->id;
        //var_dump($post['RouteStep']); die();
        $this->load($post[$model_name], '');
        $this->has_time = 1;
        $this->version = 3;
        //   var_dump($post);
        return $this->save();
    }

    public function save_good_1($post, $model_name) {

        $rtp = RouteStep::setTimeworkfromPost($post);

        //  $insert = Yii::$app->db->createCommand("UPDATE `marshruts` SET `value` = '".$marshruts_value."' WHERE `itemID` = '".$this->id."';")->query();
        //Yii::$app->db->createCommand()->update('marshruts', ['value' => $marshruts_value], "itemID = $this->id")->execute();
        $post[$model_name]['id'] = $this->id;
        //var_dump($post); die();

        $this->load($post[$model_name], '');
        $this->version = 1; // если обновляли старый с картой(4), то меняем на новый с картой
        $this->has_time = 1;
        /*
          $it=[];//var_dump($post['tw']); die();
          if (isset($post['tw'])) {
          foreach ($post['tw'] as $day => $tw) {
          foreach ($tw as $id=>$t) {
          $time_to_db=TimeHelper::time_to_db($t, $post['twps'][$day][$id]);
          $it[$id][$day]=$time_to_db;
          }
          }
          }
          //var_dump($it); die();

          foreach ($it as $id => $d) {
          $d['monday']=(isset($d['monday']))?$d['monday']:'';
          $d['tuesday']=(isset($d['tuesday']))?$d['tuesday']:'';
          $d['wednesday']=(isset($d['wednesday']))?$d['wednesday']:'';
          $d['thursday']=(isset($d['thursday']))?$d['thursday']:'';
          $d['friday']=(isset($d['friday']))?$d['friday']:'';
          $d['saturday']=(isset($d['saturday']))?$d['saturday']:'';
          $d['sunday']=(isset($d['sunday']))?$d['sunday']:'';
          if ($post['edit']) {
          Yii::$app->db->createCommand()->update('time_work', ['monday'=>$d['monday'], 'tuesday'=>$d['tuesday'], 'wednesday'=>$d['wednesday'],
          'thursday'=>$d['thursday'], 'friday'=>$d['friday'], 'saturday'=>$d['saturday'],
          'sunday'=>$d['sunday']], "station_rout_id = $id")->execute();
          } else {
          Yii::$app->db->createCommand()->insert('time_work', ['monday'=>$d['monday'], 'tuesday'=>$d['tuesday'], 'wednesday'=>$d['wednesday'],
          'thursday'=>$d['thursday'], 'friday'=>$d['friday'], 'saturday'=>$d['saturday'],
          'sunday'=>$d['sunday'],'station_rout_id' => $id])->execute();
          }
          }
         */

        //var_dump($post); die();
        return $this->save();
    }

    /*  public function getRedirect() {
      // var_dump($this); die();
      $step_id=$this->id;
      $row = Yii::$app->db->createCommand('SELECT url FROM redirect WHERE route_id='.$step_id.'')->queryOne();
      if ($row) { $url=$row['url']; } else { $url=''; }
      return $url;
      }
      public function getSource() {
      $step_id=$this->id;
      $row = Yii::$app->db->createCommand('SELECT text FROM  WHERE route_id='.$step_id.'')->queryOne();
      if ($row) { $url=$row['url']; } else { $url=''; }
      return $url;
      } */
}
