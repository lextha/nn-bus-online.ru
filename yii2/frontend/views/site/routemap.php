<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
//use yii\bootstrap\ActiveForm;
//use yii\captcha\Captcha;
use yii\widgets\Breadcrumbs;
use app\components\Breadcrumbs\BreadcrumbsUtility;
use common\helpers\TimeHelper;
use nirvana\jsonld\JsonLDHelper;

Yii::$app->view->registerJsFile('/js/route.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('/js/routemap.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=2270d43a-3606-43eb-a583-974f7519ba49',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?render=6LcL0rQcAAAAABXq8yHHdxrN56i5D6Dk7nw4L7bT&onload=onloadCallback');
Yii::$app->view->registerJsFile('/js/jquery.arcticmodal-0.3.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerCssFile('/css/jquery.arcticmodal-0.3.css');
Yii::$app->view->registerCssFile('/css/style1online.css?v=3');
//$day_week_global=$day_week;
//$day_week = ($this)date('w', time());
//var_dump($route); die();
if ($city->sklon=='') { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }
$city_s=explode('/',$city->sklon);
if ($route->type_direction=='3') {
    $type_for_zag='междугороднего';
    $type_for_marshr='междугородней';
} elseif($route->type_direction=='2') {
    $type_for_zag='пригородного';
    $type_for_marshr='пригородной';
} else {
    $type_for_zag='городского';
    $type_for_marshr='городской';
}

if ($route->type_transport=='2') { $type_transport="троллейбуса"; $type_transport2=$type_for_zag." троллейбуса"; $type_transport_iminit="Троллейбус"; $type_transport_iminit_many="Троллейбусы"; } 
elseif ($route->type_transport=='4') { $type_transport="маршрутки"; $type_transport2=$type_for_marshr." маршрутки"; $type_transport_iminit="Маршрутка"; $type_transport_iminit_many="Маршрутки";} 
elseif ($route->type_transport=='3') { $type_transport="трамвая"; $type_transport2=$type_for_zag." трамвая"; $type_transport_iminit="Трамвай"; $type_transport_iminit_many="Трамваи";} 
elseif ($route->type_transport=='5') { $type_transport="электрички"; $type_transport2=$type_for_marshr." электрички"; $type_transport_iminit="Электричка";$type_transport_iminit_many="Электрички";} 
elseif ($route->type_transport=='6') { $type_transport="речного транспорта"; $type_transport2=$type_for_marshr." речного транспорта"; $type_transport_iminit="Речного транспорт"; $type_transport_iminit_many="Речной транспорт";} 
elseif ($route->type_transport=='7') { $type_transport="канатной дороги"; $type_transport2=$type_for_marshr." канатной дороги"; $type_transport_iminit="Канатная дорога"; $type_transport_iminit_many="Канатная дорога";} 
else { $type_transport="автобуса"; $type_transport2=$type_for_zag." автобуса"; $type_transport_iminit="Автобус"; $type_transport_iminit_many="Автобусы";}


//$title_str=(($route->number!='-'&&$route->number!='—')?('№'.$route->number).' ':'').$route->name;
$title_str=(($route->number!='-'&&$route->number!='—')?($route->number).' ':'').$route->name;

if ($route->type_direction=='3') {
    $this->title="Онлайн ".$type_transport." ".$title_str;
} else {
    $this->title=$type_transport_iminit." ".$route->number." онлайн на карте ".$city_s[1]."";//$this->title="Расписание ".$type_transport." ".$title_str." в ".$city_s[5]."";
}

if ($route->type_direction=='3') { // межгород
     $descr="Онлайн ".$title_str.". Маршрут следования, информация о месте отправления и автовокзале. Обновление расписания в ".date("Y")." году.";
} else {
    $descr=$type_transport_iminit_many." ".$title_str." в реальном времени в ".$city_s[5].". Онлайн на карте по координатам ГЛОНАСС и GPS.";
}
$descr=preg_replace('/[\s]{2,}/', ' ', $descr);
$descr = html_entity_decode($descr,ENT_QUOTES, 'utf-8');


$this->registerMetaTag(
  ['name' => 'description', 'content' => $descr]
);
$this->registerMetaTag(
  ['name' => 'keywords', 'content' => "онлайн ".$type_transport.", остановки, маршрут, время работы, интервалы"]
);
	
$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://goonbus.ru'.Url::toRoute(['site/route', 'id' => $route->id])]);

//$this->title = $route->name;
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id])]];
$this->params['breadcrumbs'][] = ['label' => $type_transport_iminit.' '.$route->number, 'url' => [Url::toRoute(['site/route', 'id' => $route->id])]];// $route->name;
$this->params['breadcrumbs'][] = ['label' => "Онлайн на карте", 'url' => [Url::toRoute(['site/routemap', 'id' => $route->id])]];// $route->name;

JsonLDHelper::addBreadcrumbList();

function num2word($num, $words) //echo num2word(50, array('год', 'года', 'лет'));
{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1: {
            return($words[0]);
        }
        case 2: case 3: case 4: {
            return($words[1]);
        }
        default: {
            return($words[2]);
        }
    }
}


if ($city->sklon=='') { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }
$names= explode('/', $city->sklon);
if (!(is_array($names) AND count($names)>4)) {
    for($tq=0;$tq<7;$tq++) { $names[$tq]=$city->name; }
}
        
?>
<div class="site-body wrapper maponline">

<div class="site-content">
<div class="site-cont">
        <div class="route-top">
            <h1 class="subtitle">Маршрут онлайн <?=$type_transport;?> <?=($route->number!='-'&&$route->number!='—')?('№'.$route->number).' ':'';?><?=$route->name?></h1>


            <ul class="breadcrumb">
                <li>
                    <a href="<?=Url::toRoute(['site/city', 'id' => $city->id]);?>">
                       <?=$city->name;?>
                    </a>
                </li>
                <li>
                    <a href="<?=Url::toRoute(['site/route', 'id' => $route->id]);?>">
                       <?=$type_transport_iminit.' '.$route->number;?>
                    </a>
                </li>
                <li>Онлайн на карте</li>
            </ul>
            <div class="clear_fix"></div>
	</div><!-- .route-top route1 -->
        
    <div class="route-map">
        <a href="#map2" id="showmap2" idr="<?=$route->id;?>"><img src="/i/g0R5.gif"></a>
    </div>
</div>
</div>
</div>