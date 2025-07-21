<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use himiklab\sitemap\behaviors\SitemapBehavior;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $description
 * @property string $sklon
 * @property int $count_rout
 * @property string $info_nowork
 */
class City extends \yii\db\ActiveRecord
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
                    'loc' => Url::toRoute(['/site/city', 'id' => $model->id],true),//Url::to($model->url, true),
                    'lastmod' => strtotime($model->lastmod),//strtotime(date("H:i d-m-Y", microtime(true)-(rand(1,15000)+60*60*rand(70, 8000)))),
                    'changefreq' => SitemapBehavior::CHANGEFREQ_WEEKLY,
                    'priority' => 0.9
                ];
            }
        ],
    ];
}
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'description', 'sklon','active'], 'required'],
            [['description'], 'string'],
            [['count_rout','active','wordstat'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 50],
            [['sklon'], 'string', 'max' => 255],
            [['info_nowork','info_dacha'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'description' => 'Description',
            'sklon' => 'Sklon',
            'count_rout' => 'Count Rout',
            'info_nowork' => 'Информация в нерабочие дни',
            'info_dacha' => 'Информация для дачных маршрутов',
            
        ];
    }
    
    public static function getSearch($number,$text,$city_id) {
       // $route=['',''];
        $number= addslashes(trim($number));
      //  var_dump($number);
        $text=addslashes(trim($text));
        $routes_num=[];
        $routes_text=[];
        if ($number!='') {
            $routes_num = Yii::$app->db->createCommand("SELECT * FROM route WHERE number LIKE '".$number."' AND city_id='".$city_id."' AND active=1 ORDER BY number LIMIT 30")->queryAll();
            if (count($routes_num)<2) {
                $routes_num = Yii::$app->db->createCommand("SELECT * FROM route WHERE number LIKE '".$number."%' AND city_id='".$city_id."' AND active=1 ORDER BY number LIMIT 30")->queryAll();
            } 
            if (count($routes_num)<3) {
                $routes_num = Yii::$app->db->createCommand("SELECT * FROM route WHERE number LIKE '%".$number."%' AND city_id='".$city_id."' AND active=1 ORDER BY number LIMIT 30")->queryAll();
            }
        }
        if (iconv_strlen($text)>2) {
            $routes_text = Yii::$app->db->createCommand("SELECT r.* FROM route as r,search_string as ss WHERE ss.text LIKE '%".$text."%' AND ss.route_id=r.id AND ss.city_id='".$city_id."' AND r.active=1 ORDER BY number LIMIT 30")->queryAll();
        }
        $routes= array_merge($routes_num, $routes_text);
      /*  $ostan_ishod=urldecode($ostan_ishod);
        $city = Yii::$app->db->createCommand('SELECT * FROM route as r, city as c WHERE r.city_id=c.id AND r.id='.$route_id.'')->queryOne();
  
        $rows_route = Yii::$app->db->createCommand('SELECT r.*,m.value FROM route as r, marshruts as m WHERE r.city_id='.$city['id'].' AND r.id<>'.$route_id.' AND r.active=1 AND r.time_work=m.itemID')->queryAll();
        $pohozhie=[];
        $i=0;
        //foreach ($rows_route as $key => $value) {
        $x=0;
        while ($i<=6 AND count($rows_route)>$x) {
            
            $all_ostan=json_decode($rows_route[$x]['value']);
            foreach ($all_ostan as $ostan) {
                    $q1= str_replace(" ", "", $ostan_ishod);
                    $q2= str_replace(" ", "", $ostan[0]);
                    if ($q1==$q2) { if (!isset($pohozhie[$rows_route[$x]['id']])) { $pohozhie[$rows_route[$x]['id']]=$rows_route[$x]; $i++; }}
            }    
            $x++;
        }*/
            return $routes;
    }
    public static function getSearchcities($text) {
        $cities=[];
        $text=addslashes(trim($text));
        if (iconv_strlen($text)>1) {
            $cities = Yii::$app->db->createCommand("SELECT * FROM city WHERE name LIKE '%".$text."%' LIMIT 20")->queryAll();
        }
        return $cities;
    }
    
     public static function getCities($x=0) {
        if ($x AND is_array($x) AND !(Yii::$app->user->can('admin'))) {
            $cities = (new \yii\db\Query())->select('*')->from('city')->andWhere(['id'=>$x])->createCommand()->queryAll();
        } else {
            $cities = Yii::$app->db->createCommand("SELECT * FROM city ORDER by name")->queryAll();
        }
        $cities_all=[];
        foreach ($cities as $c) {
            $cities_all[$c['id']]=$c['name'];
        }   
       // var_dump($cities_all);
        return $cities_all;
    }
    /*
    public static function getCities_editor() {
        $cities = (new \yii\db\Query())->select('*')->from('city')->andWhere(['id'=>[1,2,3,4,5]])->createCommand()->queryAll();//createCommand("SELECT * FROM city WHERE id=1")->queryAll();
        $cities_all=[];
        var_dump($cities);
        foreach ($cities as $c) {
            $cities_all[$c['id']]=$c['name'];
        }   
        return $cities_all;
    }*/
    public function beforeSave($insert) {
         
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
            /*$find=Route::find()->where(['name'=>$this->name,'city_id'=>$this->city_id])->count();
           // var_dump($find); die();
            if ($find<2) {
                
            } else {
                $this->alias=$output."-".$find;
            }*/
            $this->alias=$output;
           // var_dump($this); die();
            $this->updateAttributes(['alias']);
        }
        parent::beforeSave($insert);
        return true;
    }
}
