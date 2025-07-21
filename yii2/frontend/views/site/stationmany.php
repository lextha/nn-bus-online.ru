<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
//use yii\bootstrap\ActiveForm;
//use yii\captcha\Captcha;
Yii::$app->view->registerJsFile('/js/station.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?render=6LcL0rQcAAAAABXq8yHHdxrN56i5D6Dk7nw4L7bT');
Yii::$app->view->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=2270d43a-3606-43eb-a583-974f7519ba49',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = $station->name;
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id])]];
$this->params['breadcrumbs'][] = "Остановка ".$station->name;
function route_to($route,$sid,$td=0) { /* td - type_direction*/
    $text='';
    $flag=false;
    if (isset($route[0]) AND $td==0) { 
         $key0 = array_search($sid, array_column($route[0]['stations'], 'sid'));
         $first=array_key_first($route[0]['stations']);
         $last=array_key_last($route[0]['stations']);
       //  $text=$first." ".$key0;
         if ($first==$key0) { $text.='Начальная остановка маршрута'; } // <a href="'..'">'.$route[0]->name.'</a>'}
         elseif ($last==$key0) { $text.='Конечная остановка маршрута'; } // <a href="'..'">'.$route[0]->name.'</a>'}
         else { $text.="Следует к остановке <a href='".Url::toRoute(['site/station', 'id' => $route[0]['stations'][$last]['sid']])."'>".$route[0]['stations'][$last]['name_station']."</a>"; }
        //var_dump($text);
         $flag=true;
     } 
     if (isset($route[1]) AND $td==1) { 
         $key1 = array_search($sid, array_column($route[1]['stations'], 'sid'));
         $first=array_key_first($route[1]['stations']);
         $last=array_key_last($route[1]['stations']);
         if ($first==$key1) { $text.=($flag)?'<br>В обратном направлении начальная остановка маршрута':'Начальная остановка маршрута'; } // <a href="'..'">'.$route[0]->name.'</a>'}
         elseif ($last==$key1) { $text.=($flag)?'<br>В обратном направлении конечная остановка маршрута':'Конечная остановка маршрута'; } // <a href="'..'">'.$route[0]->name.'</a>'}
         else { $text.=($flag)?"<br>В обратном направлении следует к остановке <a href='".Url::toRoute(['site/station', 'id' => $route[1]['stations'][$last]['sid']])."'>".$route[1]['stations'][$last]['name_station']."</a>":"Следует к остановке <a href='".Url::toRoute(['site/station', 'id' => $route[1]['stations'][$last]['sid']])."'>".$route[1]['stations'][$last]['name_station']."</a>"; }
     }
     
   return $text;
}
$type_transport=['1'=>'Автобусы','4'=>'Маршрутки','2'=>'Троллейбусы','3'=>'Трамваи','5'=>'Электрички'];
$type_transport2=['1'=>'автобусов','4'=>'маршруток','2'=>'троллейбусов','3'=>'трамваев','5'=>'электричек'];
if ($city->sklon=='') { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }
$city_s=explode('/',$city->sklon);

$tt_o=[];
$tt_n=[];
foreach ($type_transport2 as $i => $tt) {
    if (isset($routes[$i]) && count($routes[$i])>0) {
        $tt_o[]=$tt;
        foreach ($routes[$i] as $route)  {  
            $rrr=(isset($route[0]))?$route[0]:((isset($route[1]))?$route[1]:'-');
            if (count($tt_n)<10) {
                if (isset($rrr['number']) AND $rrr['number']!='-') {
                    $tt_n[]="№".$rrr['number'];
                }
            }
        }
    }
}
if (count($tt_n)>0) {
    $tte_n="(".implode(", ", $tt_n).")";
} else {
    $tte_n='';
}

if (count($tt_o)>1) {
    $tte_o=implode(", ", $tt_o);
} else {
    $tte_o='общественного транспорта';
}
//if ($station->info!='') { $info=" (".$station->info.") "; } else { $info=''; }
$this->title="Остановка ".$station->name." в ".$city_s[5]." | ".$tte_n;

$descr="Маршруты ".$tte_o.$tte_n." через остановки ".$station->name." в городе ".$city_s[0].". Как проехать, направление и время отправления.";


$this->registerMetaTag(
  ['name' => 'description', 'content' => $descr]
);
$this->registerMetaTag(
  ['name' => 'keywords', 'content' => "остановка ".$station->name.", маршруты, время работы, направление"]
);
$this->registerLinkTag(['rel' => 'canonical', 'href' => (Url::toRoute(['site/city', 'id' => $city->id])."/st/".$station->alias)]);
//echo "<prE>";var_dump($station);
?>
<div class="site-body wrapper">

<div class="site-content">
<div class="site-cont">

<div class="city-top">
		
        <h1 class="subtitle">Маршруты общественного транспорта через остановки "<?=$station->name;?>"</h1>

<? ///////////////////////////////////////////////////////////////////////////// 
// adsense
 echo $this->render('rek/_ggl771'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>
    <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],'homeLink' => false
        ]) ?>
        <div class="route-map">
            <a href="#map" id="showmap" idr="<?=$station->id;?>"><div class="map_bg">Остановки на карте</div></a>
        </div>
	<div class="route-stop">

                <div class="tab pss"  day='<?=$day_week;?>'>
			<div href="?day_week=1" <?=($day_week==1)?'class="active"':'';?> day='1'>ПН</div>
			<div href="?day_week=2" <?=($day_week==2)?'class="active"':'';?> day='2'>ВТ</div>
                        <div href="?day_week=3" <?=($day_week==3)?'class="active"':'';?> day='3'>СР</div>
                        <div href="?day_week=4" <?=($day_week==4)?'class="active"':'';?> day='4'>ЧТ</div>
                        <div href="?day_week=5" <?=($day_week==5)?'class="active"':'';?> day='5'>ПТ</div>
                        <div href="?day_week=6" <?=($day_week==6)?'class="active"':'';?> day='6'>СБ</div>
                        <div href="?day_week=7" <?=($day_week==7)?'class="active"':'';?> day='7'>ВС</div>
		</div>
		<? /* <div class="banner">
			<img src="files/banner-1.jpg" alt=""/>
		</div> */ ?>

	</div><!-- .route-stop -->
        
<? /*
        <div class="search">
                <div class="title">Найти расписание в <?=$names[2];?> по остановке или номеру маршрута</div>
                <div class="form">
                    <input type="text" name="" placeholder="Введите номер или остановку маршрута"/><button type="submit">Найти</button>
                </div>
        </div>
*/ ?>
</div>

<div class="city-route">

<? Pjax::begin(['id'=>'stationsection','enablePushState' => false,'linkSelector' => '.value_get']); ?>
        <div class="block">
            <? 
            $i_count=0;
            foreach ($type_transport as $i => $tt) { ?>
                <? if (isset($routes[$i]) && count($routes[$i])>0) { $i_count++; ?>
                    <div class="city-route-item v<?=$i?>">
                        <div class="subtitle">
                                <div class="tx"><?=$tt?></div>
                                <div class="nm">(<?=count($routes[$i]);?> маршрутов)</div>
                        </div>
                        
                        <ul>
                            <? foreach ($routes[$i] as $route)  {  
                                 $r=(isset($route[0]))?$route[0]:((isset($route[1]))?$route[1]:null);
                              
                              //  var_dump($r['stations']);
                              if (isset($r)) { ?>
                            <li>
                                <a href="<?=Url::toRoute(['site/route', 'id' => $r['id']]);?>">
                                    <span class='level'><i></i><?=$r['number'];?> - <?=$r['name']?></span>
                                </a>
                                <span class="value2">
                                    
                                    <? if (isset($route[0])) {
    // echo "<pre>"; var_dump($route[0]); die();  
                                     ?>
                                <span class="value_to_t" idi="<?=$route[0]['station_rout_id'];?>">
                                    <?
                                    echo $this->render('_timework', [
                                        's' => $station,
                                        'type_day' => $route[0]['type_day'],
                                        'day_week' => $day_week,
                                        'f'=>false,
                                         'time_work'=> $route[0],
                                              'flag2'=>true,
                                              'city'=>$city,
                                    ]);
                                     ?> 
                                </span>  
                                    <?    
                                 /*       $key0 = array_search($station->id, array_column($route[0]['stations'], 'sid'));
                                        ?>
                                    <span class="value_to"><? echo route_to($route,$station->id,0);?></span>
                                    <? echo "<pre>"; var_dump($station->id,$route[0]['stations']); ?>
                                    <span class="value_to_t" idi="<?=$route[0]['stations'][$key0]['station_route_id'];?>">
                                     <?=$this->render('_timework', [
                                   's' => $station,
                                   'type_day' => $route[0]['type_day'],
                                   'day_week' => $day_week,
                                   'f'=>false,
                                    'time_work'=> $route[0],
                                         'flag2'=>true,
                                         'city'=>$city,
                                ])  ?>
                                   </span>
                                    <? */} elseif (isset($route[1])) { 
                                    ?>
                                <span class="value_to_t" idi="<?=$route[1]['station_rout_id'];?>">
                                    <?
                                     echo $this->render('_timework', [
                                   's' => $station,
                                   'type_day' => $route[1]['type_day'],
                                   'day_week' => $day_week,
                                   'f'=>false,
                                    'time_work'=> $route[1],
                                         'flag2'=>true,
                                         'city'=>$city,
                                ]);
                                    ?> 
                                    </span>  
                                    <?
                                        
                                        /*
                                        $key1 = array_search($station->id, array_column($route[1]['stations'], 'sid'));?>
                                    <span class="value_to"><? echo route_to($route,$station->id,1);?></span>
                                    <span class="value_to_t" idi="<?=$route[1]['stations'][$key1]['station_route_id'];?>">
                                     <?=$this->render('_timework', [
                                   's' => $station,
                                   'type_day' => $route[1]['type_day'],
                                   'day_week' => $day_week,
                                   'f'=>false,
                                    'time_work'=> $route[1],
                                    'flag2'=>true,
                                         'city'=>$city,
                                ])  ?>
                                   </span>
                                    <? */} ?>
 </span>
                            </li>
                            <? } } ?>
                        </ul>
                        
                    </div><!-- .city-route-item -->
                <? }
            }
            if ($i_count==0) {
                ?>
                    <div class="item item-3" style="background: #fff5f5;border: 1px solid #ff0e51;padding: 20px;"><div class="level">Маршрутов через остановку не найдено</div></div>
                    <?
            }
            ?>
        </div> <? Pjax::end(); ?>
</div>
    <? $O1=['o','о'];
       $B1=['B','В'];
       $z1=['_','-','*','`',' ','—',' ','–'];
    ?>
    <div style="display:none;"><noindex><!--googleoff: all--><div class="modalwin" id="errorfindmodal"></div> <!--googleon: all--></noindex></div><div style="display:none;"><noindex><!--googleoff: all-->2011 © на базу данных <?="G".$O1[array_rand($O1)].$z1[array_rand($z1)].$O1[array_rand($O1)]."n".$z1[array_rand($z1)].$B1[array_rand($B1)]."us .".$z1[array_rand($z1)]."ru"?></noindex></div>
</div>
</div>
</div>