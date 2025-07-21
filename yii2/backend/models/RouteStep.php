<?php

namespace app\models;
use common\models\Route;
use common\models\TimeWork;
use common\helpers\TimeHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "route_step".
 *
 * @property int $id
 * @property int $route_id
 * @property string $name
 * @property string $number
 * @property string $alias
 * @property int $city_id
 * @property string $price
 * @property int $type_transport 1-автобус,2-троллейбус, 3-трамвай, 4-маршрутки, 5-электрички
 * @property int $type_direction 1-город, 2-пригород, 3-межгород
 * @property string $organization
 * @property string $info
 * @property string $time_work
 * @property string $route_text
 * @property int $type_day 1- буд-вых, 2 - кажд день, 3 - одно на все дни, 4 - будни-суб-вс
 * @property string $temp_route_id
 * @property int $active
 * @property string $lastmod
 * @property int $version 1-новые с картой, -1 - старые без карты
 * @property string $marshruts_value
 * @property int $user_id
 * @property string $date
 * @property int $status
 */
class RouteStep extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_step';
    }
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'lastmod',
                ],
                'value' => function() { return gmdate('Y-m-d H:i:s'); } // unix timestamp },
            ],
        ];
    }
    public function beforeSave($insert) {
      //  var_dump($this->id); die();
        parent::beforeSave($insert);
        if ($this->alias=='none' OR $this->alias=='' OR $this->alias==NULL) {
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
            if ($this->alias!='none') {
                $find=Route::find()->where(['name'=>$this->name,'city_id'=>$this->city_id])->count();
                //var_dump($find);
                if ($find<2) {
                    $this->alias=$output;
                } else {
                    $this->alias=$output."-".$find;
                }
                $this->updateAttributes(['alias']);
            }
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['route_id', 'city_id', 'type_transport', 'type_direction', 'type_day', 'season', 'active', 'version', 'user_id', 'status','pay','price_edit'], 'integer'],
            [['info', 'time_work', 'route_text', 'temp_route_id', 'marshruts_value','otklon'], 'string'],
            [['lastmod', 'date'], 'safe'],
            [['name', 'alias', 'price', 'organization'], 'string', 'max' => 255],
            [['number','redirect'], 'string', 'max' => 250],
            [['source'], 'string', 'max' => 2250]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route_id' => 'ID Маршрута',
            'name' => 'Название маршрута',
            'number' => 'Номер',
            'alias' => 'Alias',
            'city_id' => 'Город',
            'price' => 'Стоимость проезда (руб.)',
            'type_transport' => 'Вид транспорта',
            'type_direction' => 'Тип направления',
            'organization' => 'Обслуживающее предприятие',
            'info' => 'Дополнительная информация',
            'time_work' => 'Время работы',
            'route_text' => 'Маршрут текстом',
            'type_day' => 'Тип расписания по дням',
            'season' => 'Сезонный',
            'temp_route_id' => 'Temp Route ID',
            'active' => 'Действующий',
            'lastmod' => 'Дата редактирования',
            'version' => 'Версия',
            'marshruts_value' => 'Marshruts Value',
            'user_id' => 'User ID',
            'date' => 'Дата создания',
            'status' => 'Статус',
            'redirect'=>'Редирект',
            'pay'=>'Оплата',
            'source'=>'Источник',
            'price_edit'=>'Цена правки',
        ];
    }
    public static function getMarshrutfromPost($post) {
         $marshruts_value=[];
        $i=0;
        if (isset($post['marshrut_name_in']) AND is_array($post['marshrut_name_in'])) {
            foreach ($post['marshrut_name_in'] as $r => $in) {
                $marshruts_value[$i]=[$in,$post['marshrut_time_in'][$r],'in'];
                $i++;
            }
        }
        if (isset($post['marshrut_name_out']) AND is_array($post['marshrut_name_out'])) {
            foreach ($post['marshrut_name_out'] as $r => $in) {
                $marshruts_value[$i]=[$in,$post['marshrut_time_out'][$r],'out'];
                $i++;
            }
        }
        return json_encode($marshruts_value, JSON_UNESCAPED_UNICODE);
    }

    /*
     * одобрение и запись в БД времени работы
     */
    public static function setTimeworkfromPost($post) {
        if (isset($post['twps'])) { // обработка легенды, на случай если время пустое
            foreach ($post['twps'] as $day => $twps) {
                foreach ($twps as $id=>$t) { 
                   if (isset($post['tw'][$day][$id]) AND $post['tw'][$day][$id]=='' AND $post['twps'][$day][$id]!='') {
                       $post['tw'][$day][$id]='legend';
                   }
                }
            }
        }
        if(isset($post['tw']) && is_array($post['tw'])) {
            foreach ($post['tw'] as $day => $tw) {
                foreach ($tw as $id=>$t) {
                        Yii::$app->db->createCommand()->delete('time_work', "station_rout_id= $id")->execute();
                }
            }
              
            foreach ($post['tw'] as $day => $tw) {
                foreach ($tw as $id=>$t) { 
                    if ($t!='') {
                        $time_to_db=TimeHelper::time_to_db($t, $post['twps'][$day][$id]);

                  // echo "<pre>"; var_dump($t,$time_to_db,$id); 

                        $find=TimeWork::find()->where(['station_rout_id'=>$id])->count();
                        if ($find) {
                            //  var_dump('+',$find,$id);
                            Yii::$app->db->createCommand()->update('time_work', [$day => $time_to_db], "station_rout_id = $id")->execute();
                        } else {
                           // var_dump('-',$find,$id);
                            Yii::$app->db->createCommand()->insert('time_work', [$day => $time_to_db, "station_rout_id" => $id])->execute();
                        }
                    } /*else {
                        Yii::$app->db->createCommand()->delete('time_work', "station_rout_id= $id")->execute();
                    }*/
                  //  var_dump($day,$id,$time_to_db); 
                   // Yii::$app->db->createCommand()->update('time_work', [$day => $time_to_db], "station_rout_id = $id")->execute();
                }
            }
        }
        return true;
    }
    /*
     * запись в БД правки времени работы
     */
    public static function setTimeworkfromPostStep($post,$route_step_id) {
        $it=[];//var_dump($post['tw']); die();
        if (isset($post['twps'])) { // обработка легенды, на случай если время пустое
            foreach ($post['twps'] as $day => $twps) {
                foreach ($twps as $id=>$t) { 
                    if (isset($post['tw'][$day][$id]) AND $post['tw'][$day][$id]=='' AND $post['twps'][$day][$id]!='') {
                       $post['tw'][$day][$id]='legend';
                   }
                }
            }
        }
        if (isset($post['tw'])) {
            foreach ($post['tw'] as $day => $tw) {
                foreach ($tw as $id=>$t) { 
                    @$time_to_db=TimeHelper::time_to_db($t, $post['twps'][$day][$id]);
                    $it[$id][$day]=$time_to_db;
                }
            }
        }
       //var_dump($it,$post); die();
        
        foreach ($it as $id => $d) {
            $d['monday']=(isset($d['monday']))?$d['monday']:'';
            $d['tuesday']=(isset($d['tuesday']))?$d['tuesday']:'';
            $d['wednesday']=(isset($d['wednesday']))?$d['wednesday']:'';
            $d['thursday']=(isset($d['thursday']))?$d['thursday']:'';
            $d['friday']=(isset($d['friday']))?$d['friday']:'';
            $d['saturday']=(isset($d['saturday']))?$d['saturday']:'';
            $d['sunday']=(isset($d['sunday']))?$d['sunday']:'';
            $row = Yii::$app->db->createCommand('SELECT * FROM time_work_step WHERE route_step_id="'.$route_step_id.'" AND station_rout_id = "'.$id.'"')->queryOne();
            
           // if (isset($post['edit'])) {
            if ($row) {
                $q=Yii::$app->db->createCommand()->update('time_work_step', ['monday'=>$d['monday'], 'tuesday'=>$d['tuesday'], 'wednesday'=>$d['wednesday'],
                'thursday'=>$d['thursday'], 'friday'=>$d['friday'], 'saturday'=>$d['saturday'], 
                'sunday'=>$d['sunday']], "route_step_id=$route_step_id AND station_rout_id = $id")->execute();
            } else {
                $q=Yii::$app->db->createCommand()->insert('time_work_step', ['monday'=>$d['monday'], 'tuesday'=>$d['tuesday'], 'wednesday'=>$d['wednesday'],
                'thursday'=>$d['thursday'], 'friday'=>$d['friday'], 'saturday'=>$d['saturday'], 
                'sunday'=>$d['sunday'],'route_step_id'=>$route_step_id,'station_rout_id' => $id])->execute();
            }
            // die();
           // if (!$q) { var_dump($q); die('error 5882617');}
        }
        return true;
    }
    
    public function saveStep_1($model,$post,$model_name) {
       // var_dump($model);
        if ($model_name=='Route') {
            $post[$model_name]['route_id']=$model->id;
           // if ($post[$model_name]['version']=='2') { $post[$model_name]['version']=3; }
        }
        if (!isset($post['savegood']) AND !isset($post['savebad'])) { // если не одобряем правку, то записываем id автора правки
            $post[$model_name]['user_id']=Yii::$app->user->identity->id;
        }
       
        
      //  echo "<pre>"; var_dump($post['Route']); //die();
        
        $this->load($post[$model_name],'');
       // echo "<pre>"; var_dump($this); die();
        $this->save();
        $this->refresh();
        $this->setTimeworkfromPostStep($post,$this->id);
//$this->load(['name'=>'1', 'number'=>'1', 'alias'=>'1', 'city_id'=>'1', 'price'=>'1', 'type_transport'=>'1', 'organization'=>'1', 'info'=>'1', 'time_work'=>'1', 'route_text'=>'1', 'type_day'=>'1', 'version'=>'1'],'');
      //  var_dump($this);
        return true;
    }
    
    public function saveStep_2_3($model,$post,$model_name) {
       // var_dump($post);
        if ($model_name=='Route') {
            $post[$model_name]['route_id']=$model->id;
            
        }
        if ($post[$model_name]['version']=='2') { $post[$model_name]['version']=3; }
       
        if (!isset($post['savegood']) AND !isset($post['savebad'])) { // если не одобряем правку, то записываем id автора правки
            $post[$model_name]['user_id']=Yii::$app->user->identity->id;
        } 
       // echo $post[$model_name]['user_id']; die();
        $post[$model_name]['marshruts_value']=$this->getMarshrutfromPost($post);
        
      //  echo "<pre>"; var_dump($post['Route']); //die();
        $this->load($post[$model_name],'');
      //  echo "<pre>"; var_dump();
//$this->load(['name'=>'1', 'number'=>'1', 'alias'=>'1', 'city_id'=>'1', 'price'=>'1', 'type_transport'=>'1', 'organization'=>'1', 'info'=>'1', 'time_work'=>'1', 'route_text'=>'1', 'type_day'=>'1', 'version'=>'1'],'');
       // $this);
        return true;
    }
    /*
    public function saveStep_redirectfrom($route) {
       // var_dump($post);
        if ($model_name=='Route') {
            $post[$model_name]['route_id']=$model->id;
            
        }
        if ($post[$model_name]['version']=='2') { $post[$model_name]['version']=3; }
        if (!isset($post['savegood'])) { // если не одобряем правку, то записываем id автора правки
            $post[$model_name]['user_id']=Yii::$app->user->identity->id;
        }
        $post[$model_name]['marshruts_value']=$this->getMarshrutfromPost($post);
        
      //  echo "<pre>"; var_dump($post['Route']); //die();
        $this->load($route,'');
      //  echo "<pre>"; var_dump();
//$this->load(['name'=>'1', 'number'=>'1', 'alias'=>'1', 'city_id'=>'1', 'price'=>'1', 'type_transport'=>'1', 'organization'=>'1', 'info'=>'1', 'time_work'=>'1', 'route_text'=>'1', 'type_day'=>'1', 'version'=>'1'],'');
       // $this);
        return true;
    }
    */
    public function getMarshrut() {
        $step_id=$this->id;
        $row = Yii::$app->db->createCommand('SELECT marshruts_value FROM route_step WHERE id='.$step_id.'')->queryOne();
        if ($row['marshruts_value']==NULL) { 
            $all=[];
        } else { //var_dump($all); die();
            $all=json_decode($row['marshruts_value']); 
        }
     //   
        return $all;
    }
    
   /* public function getRedirect() {
       // var_dump($this); die();
        $step_id=$this->route_id;
        $row = Yii::$app->db->createCommand('SELECT url FROM redirect WHERE route_id='.$step_id.'')->queryOne();
        if ($row) { $url=$row['url']; } else { $url=''; }
        return $url;
    }
    
    public function getSource() {
       // var_dump($this); die();
        $step_id=$this->route_id;
        $row = Yii::$app->db->createCommand('SELECT text FROM redirect WHERE route_id='.$step_id.'')->queryOne();
        if ($row) { $url=$row['url']; } else { $url=''; }
        return $url;
    }*/
    
     public static function getTypetransportList() {
         return ['1'=>'Автобус','2'=>'Троллейбус','3'=>'Трамвай','4'=>'Маршрутка','5'=>'Электричка','6'=>'Речной транспорт','7'=>'Канатная дорога'];
    }
    
    public static function getTypedirectionList() {
        return ['1'=>'Городской','2'=>'Пригородный','3'=>'Междугородний'];
    }
    
    public static function getVersionList() {
        return ['1'=>'Новые с картой','2'=>'Старые без карты','3'=>'Новые без карты','4'=>'Старые с картой'];
    }
    
    public static function getStatustype(){
        return ['0'=>'На модерации','1'=>'Одобрена','2'=>'Отклонена',NULL=>'Все'];
    }

    public function getStatusname() {
        $list=self::getStatustype();
        return $list[$this->status];
    }
    
    public function getTypetransport() {
        $list=self::getTypetransportList();
        return $list[$this->type_transport];
    }
     public function getUsername() {
        $list=self::getUserall();
        return $list[$this->user_id];
    }
     public function getCityname() {
        if (!isset($this->city_id)) { return ''; }
        $list=self::getCityall();
        return $list[$this->city_id];
    }
    
    public function getCityall()
    {
        $citis=[];
        $row = Yii::$app->db->createCommand('SELECT * FROM city')->queryAll();
        foreach ($row as $c) {
            $citis[$c['id']]=$c['name'];
        }
        return $citis;
    }
    
     public static function getUserall()
    {
        $user=[];
        $row = Yii::$app->db->createCommand('SELECT * FROM user')->queryAll();
        foreach ($row as $c) {
            $user[$c['id']]=$c['username'];
        }
        return $user;
    }
    
    public function getExtraFields() {
        if (isset($this->route_id)) {
            $q='SELECT i.extra_fields FROM route as r, items_old as i  WHERE r.id='.$this->route_id.' AND r.time_work=i.id';
            $row = Yii::$app->db->createCommand($q)->queryOne();
            $all=json_decode($row['extra_fields']); 
            //var_dump($q); die();
        } else {
            $all=[];
        }
        return $all;
    }
    
}
