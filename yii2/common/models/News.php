<?php

namespace common\models;



use Yii;
use yii\helpers\Url;
use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\helpers\TimeHelper;
use yii\db\Expression;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property int $city_id
 * @property string $title
 * @property string $alias
 * @property string $text
 * @property string $time
 */
class News extends \yii\db\ActiveRecord {

    public function behaviors() {
        return [
            'sitemap' => [
                'class' => SitemapBehavior::className(),
                'scope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->select(['alias2', 'id', 'time']);
                    $model->where("city_id=".Yii::$app->params['city_id']);//andWhere(["active" => 1])->andWhere(["!=", "alias", "''"])->andWhere(["==", "city_id", Yii::$app->params['city_id']]);
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    return [
                'loc' => Url::toRoute(['/site/news', 'id' => $model->id], true), //Url::to($model->url, true),
                'lastmod' => date('c', strtotime($model->time)),
                'changefreq' => SitemapBehavior::CHANGEFREQ_WEEKLY,
                'priority' => 0.8
                    ];
                }
            ],
            /*   'sitemap' => [
              'class' => SitemapBehavior::className(),
              'scope' => function ($model) {

              $model->select(['alias', 'id', 'lastmod']);
              $model->andWhere(["active" => 1])->andWhere(["!=", "alias", "''"])->andWhere(["!=", "city_id", "0"]);
              },
              'dataClosure' => function ($model) {

              return [
              'loc' => Url::toRoute(['/site/route', 'id' => $model->id], true), //Url::to($model->url, true),
              'lastmod' => strtotime($model->lastmod), //strtotime(date("H:i d-m-Y", microtime(true)-(rand(1,15000)+60*60*rand(70, 8000)))),
              'changefreq' => SitemapBehavior::CHANGEFREQ_WEEKLY,
              'priority' => 0.8
              ];
              }
              ], */
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'time'
                ],
                'value' => function () {
                    return date('Y-m-d H:i:s');
                } // unix timestamp },
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes) {
      /*  if (!Yii::$app->request->isConsoleRequest) {
            if ($insert) {
                Yii::$app->session->setFlash('success', 'Новость добавлена');
            } else {
                Yii::$app->session->setFlash('success', 'Новость обновлена');
            }
        }*/
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert) {


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
        if (($this->alias == 'none' OR $this->alias == '') AND $this->title != 'none') {
            $string = $this->title;
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
            $find = News::find()->where(['title' => $this->title, 'city_id' => $this->city_id])->count();
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
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['city_id', 'title', 'text'], 'required'],
            [['city_id', 'view'], 'integer'],
            [['text'], 'string'],
            [['time'], 'safe'],
            [['title', 'alias','source','title2','alias2','text2','titleh1','descr','url_source','photo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'title' => 'Заголовок',
            'titleh1' => 'Заголовок H1',
            'descr' => 'descr',
            'alias' => 'Alias',
            'text' => 'Текст',
            'time' => 'Дата',
            'view' => 'Просмотров',
            'source' => 'Источник',
            'url_source'=>'Ссылка источник',
            'photo'=>'Фото'
        ];
    }

    public function getCity() {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
    
    public function getCityname() {
        if (!isset($this->city_id)) {
            return false;
        }
        $list = City::getCities();
        return $list[$this->city_id];
    }
    
}
