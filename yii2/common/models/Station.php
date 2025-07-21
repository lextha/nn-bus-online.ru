<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\Stationmany;
/**
 * This is the model class for table "station".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $city_id
 * @property float $x
 * @property float $y
 * @property int $temp_id
 */
class Station extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'sitemap' => [
                'class' => SitemapBehavior::className(),
                'scope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->select(['alias','id','lastmod']);
                    $model->andWhere(["!=","alias","''"]);
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    return [
                        'loc' => Url::toRoute(['/site/station', 'id' => $model->id],true),//Url::to($model->url, true),
                        'lastmod' => strtotime($model->lastmod),//strtotime(date("H:i d-m-Y", microtime(true)-(rand(1,15000)+60*60*rand(70, 8000)))),
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
                'value' => function() { return date('Y-m-d H:i:s'); } // unix timestamp },
            ],
        ];
    }
    
    public function beforeSave($insert)
    {
   //     if ($this->name=='По требованию') {            var_dump($this->alias); }
        if ($this->alias=='none') {
            $string=$this->name;
            // $string = iconv('windows-1251', 'utf-8', $string);
            $string = mb_strtolower($string, 'UTF-8');
            $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
            $string = strip_tags($string);
            $string = preg_replace('/[\r\n\t]+/', ' ', $string);

            $table = [
                    'а' => 'a','б' => 'b','в' => 'v','г' => 'g',
                    'д' => 'd','е' => 'e','ё' => 'yo','ж' => 'zh',
                    'з' => 'z','и' => 'i','й' => 'j','к' => 'k',
                    'л' => 'l','м' => 'm','н' => 'n','о' => 'o',
                    'п' => 'p','р' => 'r','с' => 's','т' => 't',
                    'у' => 'u','ф' => 'f','х' => 'h','ц' => 'c',
                    'ч' => 'ch','ш' => 'sh','щ' => 'csh','ь' => '',
                    'ы' => 'y','ъ' => '','э' => 'e','ю' => 'yu',
                    'я' => 'ya',' ' => '-','«' => "",'»' => "",'№'=>""
            ];

            $output = str_replace(
                    array_keys($table),
                    array_values($table),$string
            );

            $output = str_replace("--","-",$output);
            $output = str_replace("--","-",$output);
            $output = str_replace("--","-",$output);
            $output = str_replace("--","-",$output);

            $output = preg_replace('/|[^\w-]+|/u','',$output);//var_dump($output);
           // $output = preg_replace('/[\x00-\x1F\x7F]/u', '', $output);

            $output = str_replace("--","-",$output);
            $output = str_replace("--","-",$output);

            $output = trim($output,"-");
            /*$output2= explode('-', $output);
            $output3=[];
            //$output='';
            foreach ($output2 as $o) {
                if (strlen($o)>1) {
                    $output3[]=$o;
                }
            }
            $output= implode('-', $output3);*/
            /*$find=Station::find()->where(['name'=>$this->name,'city_id'=>$this->city_id])->count();
            // if ($output=='po-trebovaniyu') { var_dump($find); die(); }
             if ($find<2) {
                 $this->alias=$output;
             } else {
                 $this->alias=$output."-2";
             }*/
            $this->alias=$output;
            $i=1;
            $flagi=true;
            while ($flagi) {
                $find=Station::find()->where(['alias'=>$this->alias,'city_id'=>$this->city_id])->count();
                if ($find>0) { $this->alias=$output.'-'.$i; } else { $flagi=false; }
                $i++;  
            }
          
            $this->updateAttributes(['alias']);
        }
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'station';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city_id', 'x', 'y', 'temp_id'], 'required'],
            [['city_id'], 'integer'],
            [['x', 'y'], 'number'],
            [['name', 'alias'], 'string', 'max' => 100],
            [['info', 'temp_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'alias' => 'Alias',
            'city_id' => 'City ID',
            'x' => 'X',
            'y' => 'Y',
            'temp_id' => 'Temp ID',
            'info' => 'Информация',
        ];
    }
    
    public function getRoutes()
    {
        return $this->hasMany(Route::className(), ['id' => 'route_id'])
            ->viaTable('station_rout', ['station_id' => 'id']);
    }
    
    public function getStationmany()
    {
        return $this->hasOne(Stationmany::className(), ['id' => 'stationmany_id'])
            ->viaTable('stationmany_station', ['station_id' => 'id']);
    }
    
    public static function getRoutesByStation($station_id) {
   
        
        $rows = Yii::$app->db->createCommand('SELECT s.name as name_station, sr.direction as direction, sr.id as station_rout_id, r.* '
                . 'FROM station as s, station_rout as sr, route as r '
                . 'WHERE r.active=1 AND sr.station_id='.$station_id.' AND sr.route_id=r.id AND s.id=sr.station_id')->queryAll();

        $routes=[];
       
        foreach ($rows as $key=>$r) {
            
             $st = Yii::$app->db->createCommand('SELECT s.name as name_station, s.id as sid, sr.direction as direction, sr.id as station_route_id, r.id '
                . 'FROM station as s, station_rout as sr, route as r '
                . 'WHERE r.active=1 AND r.id=sr.route_id AND sr.route_id='.$r['id'].' AND s.id=sr.station_id AND sr.direction='.$r['direction'])->queryAll();
              //  echo ' <pre>'; var_dump($r); die();
             $tw = Yii::$app->db->createCommand('SELECT * '
                . 'FROM time_work '
                . 'WHERE station_rout_id='.$r['station_rout_id'])->queryOne();
             if (!is_array($tw)) {
                 $tw=["monday"=>'',"tuesday"=>'',"wednesday"=>'',"thursday"=>'',"friday"=>'',"saturday"=>'',"sunday"=>''];
             }
             $r=array_merge($tw,$r);
             $routes[$r['type_transport']][$r['id']][$r['direction']]=$r;
             $routes[$r['type_transport']][$r['id']][$r['direction']]['stations']=$st;
           //  $rows[$key]['direction']=$st;
        }
     //  echo ' <pre>'; var_dump($rows); die();
       //var_dump($row); 
        return $routes;
    }
    
    public static function setOld() {
   
        $rows = Yii::$app->db->createCommand("SELECT c.name,c.name_p, m.alias "
                . "FROM pzpv5_menu as m, pzpv5_k2_categories as c "
                . "WHERE m.title=c.name AND c.published=1 AND m.note='on' AND m.published=1")->queryAll();

        $routes=[];
        foreach ($rows as $key=>$r) {
            Yii::$app->db->createCommand("INSERT INTO city "
                . "(`id`, `name`, `alias`, `description`, `sklon`, `count_rout`) "
                . "VALUES (NULL,'".$r['name']."','".$r['alias']."','','".$r['name_p']."','0')")->query();          
        }
  
        return $rows;
    }


    public static function setOld3() { /// ЗАМЕНА В НАЗВАНИЯХ ОСТАНОВОК
        $rows = Yii::$app->db->createCommand("SELECT * "
            . "FROM route "
            . "WHERE name LIKE '%проезд%' AND city_id=1")->queryAll();

        foreach ($rows as $key=>$r) {
            $name=$r['name'];
            $name= str_replace("проезд", " проезд", $name);
            $name = preg_replace('/[\s]{2,}/', ' ', $name);
            $q="UPDATE `route` SET `name`='".$name."' WHERE id='".$r['id']."'";
            $upd = Yii::$app->db->createCommand($q)->query();       
        }
  
        return true;
    }
    
        public static function setOld4() { /// Установка версии
        $rows = Yii::$app->db->createCommand("SELECT * "
            . "FROM route WHERE version=0")->queryAll();

        foreach ($rows as $key=>$r) {
            if ($r['temp_route_id']=='-1' OR $r['temp_route_id']=='0') {
                $version=2;
            } else {
                $version=1;
            }
            $q="UPDATE `route` SET `version`='".$version."' WHERE id='".$r['id']."'";
            $upd = Yii::$app->db->createCommand($q)->query();       
        }
  die('45');
        return true;
    }
    
    public static function setOld2() {
   ini_set('max_execution_time', 10000);
   $rows2 = Yii::$app->db->createCommand("SELECT r.id as rid, m.id as mid FROM route as r, marshruts as m WHERE m.temp<>1 AND r.time_work<>'' AND r.time_work=m.itemID LIMIT 5000")->queryAll();
   $rr=0;
   foreach ($rows2 as $k=>$r) {
            $q="UPDATE `marshruts` SET `itemID`='".$r['rid']."',`temp`=1 WHERE id='".$r['mid']."'";
            $upd = Yii::$app->db->createCommand($q)->query();
            
           /* $q="UPDATE `route` SET `time_work`='' WHERE id='".$r['id']."'";
            $upd = Yii::$app->db->createCommand($q)->query();*/
   }
   die('good');
   
 /*  $rows2 = Yii::$app->db->createCommand("SELECT * "
                . "FROM pzpv5_k2_items  "
                . "WHERE trash='1' OR ex_active=0")->queryAll();
   $rr=0;
   foreach ($rows2 as $k=>$i) {
       
        $rrrr="DELETE FROM `route` WHERE time_work='".$i['id']."'";
                    Yii::$app->db->createCommand($rrrr)->query(); 
       
                $rrrr="DELETE FROM `marshruts` WHERE itemID='".$i['id']."'";
        Yii::$app->db->createCommand($rrrr)->query(); 
         $rr++;           
   }
   return $rr;*/
 //  die();

          /*    $rows2 = Yii::$app->db->createCommand("SELECT * "
                . "FROM pzpv5_k2_items  "
                . "WHERE catid='248'")->queryAll();
              
            foreach ($rows2 as $k=>$i) {
                if ($i['ex_type']=='Автобус') { $type=1; } 
                elseif ($i['ex_type']=='Троллейбус') { $type=2; }
                elseif ($i['ex_type']=='Трамвай') { $type=3; }
                elseif ($i['ex_type']=='Маршрутка') { $type=4; }
                elseif ($i['ex_type']=='Маршрутные такси') { $type=4; }
                elseif ($i['ex_type']=='Микроавтобус') { $type=4; }
                elseif ($i['ex_type']=='Электричка') { $type=5; }
                elseif ($i['ex_type']=='Водный') { $type=6; }
                elseif ($i['ex_type']=='Фуникулёр') { $type=7; }
                elseif ($i['ex_type']=='Метро') { continue; }
                
                if ($i['ex_naznach']=='1') { $type_=1; } 
                elseif ($i['ex_naznach']=='2') { $type=2; }
                elseif ($i['ex_naznach']=='3') { $type=3; }
                $ef=json_decode($i['extra_fields']);
                //var_dump($ef);
                
                if ($i['ex_number']!='—' AND $i['ex_number']!='-' AND $i['ex_number']!='' AND $i['ex_number']!=' ') {
                    $dubl = Yii::$app->db->createCommand("SELECT * "
                    . "FROM route "
                    . "WHERE number='".$i['ex_number']."' AND `type_transport`='".$type."' AND `type_direction`='".$i['ex_naznach']."' AND city_id='1'")->queryAll();
                    foreach ($dubl as $du) {
                        $fp = fopen("dubl2.txt", "a");
                        $mytext = "Москва - ".$i['ex_number']." - http://domen.ruu/moskva/".$du['alias']." | http://domen.ruu/moskva/".$i['alias']."\r\n"; // Исходная строка
                        $test = fwrite($fp, $mytext); 
                        fclose($fp); //Закрытие файла
                    }
                }
                
                 $dubl = Yii::$app->db->createCommand("SELECT id "
                    . "FROM route "
                    . "WHERE alias='".$i['alias']."' AND city_id='1'")->queryAll();
   // var_dump($dubl);
              //  if ($dubl)
                 if (count($dubl)==0) {
                $rrrr="INSERT INTO route "
                    . "(`id`, `name`, `number`, `alias`, `city_id`, `price`, `type_transport`, `type_direction`, "
                    . "`organization`, `info`, `time_work`, `route_text`, `type_day`, `temp_route_id`, `active`,`lastmod`) "
                    . "VALUES (NULL,'".$i['title']."','".$i['ex_number']."','".$i['alias']."','1','','".$type."','".$i['ex_naznach']."',"
                    . " '".$ef[7]->value."','','".$i['id']."','',0,'-1',1,'2020-05-22 14:21:55')";
                    Yii::$app->db->createCommand($rrrr)->query(); 
                 }
          // echo "<pre>"; var_dump($rrrr);
                
             //   if ($k>20) { die(); }
            }
            die();*/
            
  
   /*
   
       $rows = Yii::$app->db->createCommand("SELECT * "
               . "FROM city "
               . "WHERE name<>'Москва'")->queryAll();
        foreach ($rows as $key=>$r) {
              $rows2 = Yii::$app->db->createCommand("SELECT i.* "
                . "FROM pzpv5_k2_items as i, pzpv5_k2_categories as c "
                . "WHERE i.catid=c.id AND c.name='".$r['name']."' AND i.published=1")->queryAll();
              
            foreach ($rows2 as $k=>$i) {
                if ($i['ex_type']=='Автобус') { $type=1; } 
                elseif ($i['ex_type']=='Троллейбус') { $type=2; }
                elseif ($i['ex_type']=='Трамвай') { $type=3; }
                elseif ($i['ex_type']=='Маршрутка') { $type=4; }
                elseif ($i['ex_type']=='Маршрутные такси') { $type=4; }
                elseif ($i['ex_type']=='Микроавтобус') { $type=4; }
                elseif ($i['ex_type']=='Электричка') { $type=5; }
                elseif ($i['ex_type']=='Водный') { $type=6; }
                elseif ($i['ex_type']=='Фуникулёр') { $type=7; }
                elseif ($i['ex_type']=='Метро') { continue; }
                
                if ($i['ex_naznach']=='1') { $type_=1; } 
                elseif ($i['ex_naznach']=='2') { $type=2; }
                elseif ($i['ex_naznach']=='3') { $type=3; }
                $ef=json_decode($i['extra_fields']);
                //var_dump($ef);
                
                if ($i['ex_number']!='—' AND $i['ex_number']!='-' AND $i['ex_number']!='' AND $i['ex_number']!=' ') {
                    $dubl = Yii::$app->db->createCommand("SELECT * "
                    . "FROM route "
                    . "WHERE number='".$i['ex_number']."' AND `type_transport`='".$type."' AND `type_direction`='".$i['ex_naznach']."' AND city_id='".$r['id']."'")->queryAll();
                    foreach ($dubl as $du) {
                        $fp = fopen("dubl.txt", "a");
                        $mytext = $r['name']." - ".$i['ex_number']." - http://domen.ruu/".$r['alias']."/".$du['alias']." | http://domen.ruu/".$r['alias']."/".$i['alias']."\r\n"; // Исходная строка
                        $test = fwrite($fp, $mytext); 
                        fclose($fp); //Закрытие файла
                    }
                }
                
                $rrrr="INSERT INTO route "
                    . "(`id`, `name`, `number`, `alias`, `city_id`, `price`, `type_transport`, `type_direction`, "
                    . "`organization`, `info`, `time_work`, `route_text`, `type_day`, `temp_route_id`, `active`) "
                    . "VALUES (NULL,'".$i['title']."','".$i['ex_number']."','".$i['alias']."','".$r['id']."','','".$type."','".$i['ex_naznach']."',"
                    . " '".$ef[7]->value."','','".$i['id']."','',0,0,1)";
          // echo "<pre>"; var_dump($rrrr);
                Yii::$app->db->createCommand($rrrr)->query();     
              
            }
             // if ($k>20) { die(); }
        }
   */
   /******************************************************
    * **************************************************
    */
   /*
$rows = Yii::$app->db->createCommand("SELECT * "
               . "FROM city "
               . "WHERE name<>'Москва'")->queryAll();
        foreach ($rows as $key=>$city) {
             $rows2 = Yii::$app->db->createCommand("SELECT r.*,m.value "
                . "FROM route as r, marshruts as m "
                . "WHERE r.time_work=m.itemID AND r.city_id='".$city['id']."'")->queryAll();
              
            foreach ($rows2 as $k=>$route) {
                $marsh=json_decode($route['value']);
                $itog=[];
                if (isset($marsh) AND is_array($marsh)){
                    foreach ($marsh as $m) {
                        //$m[0]= str_replace("ул.", "", $m[0]);
                        $m[0]=trim($m[0]);
                        $mm=explode(" ", $m[0]);
                        foreach ($mm as $mmm) {
                            $count=iconv_strlen($mmm);
                            if ($count>2 AND (!in_array($mmm, $itog))) {
                                if ($mmm!='ул.' AND $mmm!='пл.' AND $mmm!='Пл.' AND $mmm!='ст.' 
                                        AND $mmm!='ст.м.' AND $mmm!='ж-д' AND $mmm!='жд' AND $mmm!='пр.' AND $mmm!='улица' AND $mmm!='микрорайон') {
                                    $itog[]=trim($mmm);
                                }
                            }
                        }
                    }
                    $itog_string= implode(" ", $itog);
                    $itog_string=$route['number']." ".$itog_string;
                    $rrrr="INSERT INTO `search_string`(`id`, `city_id`, `route_id`, `text`) "
                                . "VALUES (NULL,'".$city['id']."','".$route['id']."',".\Yii::$app->db->quoteValue($itog_string).")";
                    Yii::$app->db->createCommand($rrrr)->query();  
                }
            }
        }
    
        
        return true; */////////////////////////////////////////////////////////////////////
   /**************************************************************************/
   
   
$rows = Yii::$app->db->createCommand("SELECT * "
               . "FROM city "
               . "WHERE name='Москва'")->queryAll();
        foreach ($rows as $key=>$city) {
             $rows2 = Yii::$app->db->createCommand("SELECT id, number "
                . "FROM route "
                . "WHERE city_id='".$city['id']."'")->queryAll();

            foreach ($rows2 as $k=>$route) {
                 $marsh = Yii::$app->db->createCommand("SELECT s.name "
                . "FROM station_rout as sr, station as s "
                . "WHERE sr.route_id='".$route['id']."' AND sr.station_id=s.id")->queryAll();
               //  var_dump($rows22); die();
               // $marsh=json_decode($route['value']);
                $itog=[];
                if (isset($marsh) AND is_array($marsh)){
                    foreach ($marsh as $m) {
                        //$m[0]= str_replace("ул.", "", $m[0]);
                        $mmm=trim($m['name']);
                       // $mmm=explode(" ", $m['name']);
                       // foreach ($mm as $mmm) {
                        $count=iconv_strlen($mmm);
                        if ($count>2 AND (!in_array($mmm, $itog))) {
                            if ($mmm!='ул.' AND $mmm!='пл.' AND $mmm!='Пл.' AND $mmm!='ст.' 
                                    AND $mmm!='ст.м.' AND $mmm!='ж-д' AND $mmm!='жд' AND $mmm!='пр.' AND $mmm!='улица' AND $mmm!='микрорайон') {
                                $itog[]=trim($mmm);
                            }
                        }
                      //  }
                    }
                    $itog_string= implode(" ", $itog);
                    $itog_string=$route['number']." ".$itog_string;
                    $rrrr="INSERT INTO `search_string`(`id`, `city_id`, `route_id`, `text`) "
                                . "VALUES (NULL,'".$city['id']."','".$route['id']."',".\Yii::$app->db->quoteValue($itog_string).")";
                    Yii::$app->db->createCommand($rrrr)->query();  
                }
            }
        }
    
        
        return true;
   
    }
   
}
