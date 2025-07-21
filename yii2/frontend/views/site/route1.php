<header class="header">
			<a href="index.html" class="logo">
				<img src="/img/logo.svg" alt="logo">
				<div class="logo-title">
					<span class="logo__heading">Общественный транспорт</span>
					<span class="logo__text">Нижний Новгород</span>
				</div>
			</a>
			<div class="header-info">
				<span class="header-info__time">
					<img src="/img/time.svg" alt="time"> 12:26
				</span>
				<span class="header-info__weather">
					<img src="/img/snow.svg" alt="weather"> -15°
				</span>
			</div>
			<h1 class="title">
				<span class="title__heading">Автобус <b>28</b></span>
				<span class="title__address">АТП Центральная автостанция</span>
				<span class="title__address">Магазин Цветы по центру</span>
			</h1>
			<div class="dropdown">
				<button class="dropdown__btn">Другие направления маршрута</button>
				<div class="dropdown-content">
					<button class="dropdown__item">АТП Центральная автостанция - Магазин Цветы</button>
					<button class="dropdown__item is_active">Магазин Цветы - АТП Центральная автостанция </button>
					<button class="dropdown__item">АТП Центральная автостанция - Оздоровительный комплекс
						"Сипайловский"</button>
					<button class="dropdown__item">Скорая помощь - Магазин Цветы</button>

				</div>
			</div>
			<nav class="nav">
				<h2 class="nav__item is_active modal_close">Расписание</h2>
				<h2 class="nav__item" data-modal="scheme">Онлайн схема</h2>
				<h2 class="nav__item" data-modal="map">Онлайн карта</h2>
			</nav>
		</header>

		<main class="stops-section">
			<p class="stops__heading">Остановки:</p>
			<ul class="stops">
				<li class="stops-item">
					<span class="bus__anim"></span>
					<span class="stops__text">АТП Центральная автостанция и АТП Центральная автостанция</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item">
					<span class="stops__text">Оздоровительный комплекс "Сипайловский"</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --pink">
					<button class="stops__show">Все остановки (6)</button>
				</li>
				<li class="stops-item --hidden">
					<span class="bus__anim"></span>
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --hidden">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --hidden">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --hidden">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --hidden">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --hidden">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item --hidden">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item">
					<span class="bus__anim"></span>
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
				<li class="stops-item">
					<span class="stops__text">Скорая помощь</span>
					<button class="btn" data-modal="route">Расписание</button>
				</li>
			</ul>
		</main>

		<img src="img/map.jpg" alt="map">


<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */
/*
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
Yii::$app->view->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=2270d43a-3606-43eb-a583-974f7519ba49',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?render=6LcL0rQcAAAAABXq8yHHdxrN56i5D6Dk7nw4L7bT');
Yii::$app->view->registerJsFile('/js/jquery.arcticmodal-0.3.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerCssFile('/css/jquery.arcticmodal-0.3.css');
//$day_week_global=$day_week;
//$day_week = ($this)date('w', time());
//var_dump($route); die();
if ($city->sklon=='' OR ( substr_count($city->sklon, '/')<5)) { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }

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

if ($route->type_transport=='2') { $type_transport="троллейбуса"; $type_transport2=$type_for_zag." троллейбуса"; $type_transport_iminit="Троллейбус"; } 
elseif ($route->type_transport=='4') { $type_transport="маршрутки"; $type_transport2=$type_for_marshr." маршрутки"; $type_transport_iminit="Маршрутка";} 
elseif ($route->type_transport=='3') { $type_transport="трамвая"; $type_transport2=$type_for_zag." трамвая"; $type_transport_iminit="Трамвай";} 
elseif ($route->type_transport=='5') { $type_transport="электрички"; $type_transport2=$type_for_marshr." электрички"; $type_transport_iminit="Электричка";} 
elseif ($route->type_transport=='6') { $type_transport="речного транспорта"; $type_transport2=$type_for_marshr." речного транспорта"; $type_transport_iminit="Речного транспорт";} 
elseif ($route->type_transport=='7') { $type_transport="канатной дороги"; $type_transport2=$type_for_marshr." канатной дороги"; $type_transport_iminit="Канатная дорога";} 
else { $type_transport="автобуса"; $type_transport2=$type_for_zag." автобуса"; $type_transport_iminit="Автобус";}


//$title_str=(($route->number!='-'&&$route->number!='—')?('№'.$route->number).' ':'').$route->name;
$title_str=(($route->number!='-'&&$route->number!='—')?($route->number).' ':'').$route->name;

if ($route->type_direction=='3') {
     $this->title="Расписание ".$type_transport." ".$title_str;
} else {
    $this->title="Расписание ".$type_transport." ".$title_str." в ".$city_s[5]."";
}

if ($route->type_direction=='3') { // межгород
     $descr="Расписание движения междугороднего автобуса ".$title_str.". Маршрут следования, информация о месте отправления и автовокзале. Обновление расписания в ".date("Y")." году.";
} else {
    $descr="Маршрут следования, интервалы и время работы ".$type_transport." ".$title_str." в ".$city_s[5].". Похожие маршруты автобусов. Обновленное расписание в ".date("Y")." году.";
}
$descr=preg_replace('/[\s]{2,}/', ' ', $descr);
$descr = html_entity_decode($descr,ENT_QUOTES, 'utf-8');


$this->registerMetaTag(
  ['name' => 'description', 'content' => $descr]
);
$this->registerMetaTag(
  ['name' => 'keywords', 'content' => "расписание ".$type_transport.", остановки, маршрут, время работы, интервалы"]
);
	
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

//$this->title = $route->name;
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city])]];
$this->params['breadcrumbs'][] = ['label' => $type_transport_iminit.' '.$route->number, 'url' => [Url::toRoute(['site/route', 'id' => $route->id,'city'=>$city,'route'=>$route])]];// $route->name;

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

function ifhide($type_day,$day_week,$key_day) {
   // var_dump($type_day,$day_week,$key_day);
    $chars = preg_split("//u", $key_day, 0, PREG_SPLIT_NO_EMPTY);
    ///var_dump($chars);
    if ($type_day==1) {
        if ($day_week<6 AND $chars[0]) { return true; } 
        elseif ($day_week>5 AND $chars[5]) { return true; } else { return false; }
    }  elseif ($type_day==2) { 
        $day_week=$day_week-1;
        return $chars[$day_week];
    } elseif ($type_day==4) { 
        if ($day_week<6 AND $chars[0]) { return true; } 
        elseif ($day_week==6 AND $chars[5]) { return true; } 
        elseif ($day_week==7 AND $chars[6]) { return true; } else { return false; }
    }  elseif ($type_day==5) { 
        
    }
        
    return true;
}

$names= explode('/', $city->sklon);
if (!(is_array($names) AND count($names)>4)) {
    for($tq=0;$tq<7;$tq++) { $names[$tq]=$city->name; }
}
     //  if ($_SERVER['REMOTE_ADDR']=='5.187.70.26') {           var_dump($city->sklon); } 
?>
<div class="site-body wrapper">

<div class="site-content">
<div class="site-cont">
        <div class="route-top">
            <h1 class="subtitle">Расписание <?=$type_transport;?> <?=($route->number!='-'&&$route->number!='—')?('№'.$route->number).' ':'';?><?=$route->name?></h1>

            <ul class="breadcrumb">
                <li>
                    <a href="<?=Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city]);?>">
                       <?=$city->name;?>
                    </a>
                </li>
                <li><?=$type_transport_iminit.' '.$route->number;?></li>
            </ul>
            <div class="clear_fix"></div>
	</div><!-- .route-top route1 -->
        <? if (!$route->active) { ?>
        <div class="route-stop"><div class="item item-3" style="background: #fff5f5;border: 1px solid #ff0e51;"><div class="level">Маршрут не действует</div></div></div>
 
            <? } ?>
 
        <div class="route-map">
            <a href="#map" id="showmap" idr="<?=$route->id;?>" ><h2 class="map_bg">Маршрут на карте</h2></a>
      
        </div>
        <?// } ?>
        <noindex><!--googleoff: all-->
            <div class="route-btn-user">
                <span rel="1">Ошибка в расписании?</span><span rel="2">Жалоба на водителя?</span><span rel="3">Забыли вещи?</span><span rel="4">Предложения по маршруту?</span>
                <div class="clear_fix"></div>
            </div>
             <? ///////////////////////////////////////////////////////////////////////////// 
            // adsense
             echo $this->render('rek/_taxi'); ?>
            <? ////////////////////////////////////////////////////////////////////////////////?>
        <!--googleon: all--></noindex>    
        <div class="route-stop" id="route-stop" idi="<?=$route->id;?>">
            <h2 class="subtitle">Остановки и время отправления <?=$type_transport2;?></h2>
                      <? if (mb_strlen((string)$route->time_work)>10) {?>
            <div class="item item-1">
                <div class="level">График работы:</div>
                <div class="value"><?= nl2br($route->time_work);?></div>
            </div>
            <? } ?>
	</div><!-- .route-stop -->
       

        <div class="route-stop daytab">
            <noindex>  
                
             
                <!--googleoff: all-->
            <?  
            if ($route->type_day==1) { 
            ?>
		<div class="tab" day='<?=($day_week<6)?'1':'6';?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<6)?'class="active"':'';?> day='1'>Будни</div>
                        <div href="?day_week=6" rel="nofollow" <?=($day_week>5)?'class="active"':'';?> day='6'>Выходные</div>
		</div>
            <? } elseif ($route->type_day==2) { ?>
                <div class="tab" day='<?=$day_week;?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week==1)?'class="active"':'';?> day='1'>ПН</div>
			<div href="?day_week=2" rel="nofollow" <?=($day_week==2)?'class="active"':'';?> day='2'>ВТ</div>
                        <div href="?day_week=3" rel="nofollow" <?=($day_week==3)?'class="active"':'';?> day='3'>СР</div>
                        <div href="?day_week=4" rel="nofollow" <?=($day_week==4)?'class="active"':'';?> day='4'>ЧТ</div>
                        <div href="?day_week=5" rel="nofollow" <?=($day_week==5)?'class="active"':'';?> day='5'>ПТ</div>
                        <div href="?day_week=6" rel="nofollow" <?=($day_week==6)?'class="active"':'';?> day='6'>СБ</div>
                        <div href="?day_week=7" rel="nofollow" <?=($day_week==7)?'class="active"':'';?> day='7'>ВС</div>
		</div>
             <? } elseif ($route->type_day==4) { ?>
                <div class="tab" day='<?=($day_week<6)?'1':(($day_week==6)?'6':'7');?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<6)?'class="active"':'';?> day='1'>Будни</div>
			<div href="?day_week=6" rel="nofollow" <?=($day_week==6)?'class="active"':'';?> day='6'>Суббота</div>
                        <div href="?day_week=7" rel="nofollow" <?=($day_week==7)?'class="active"':'';?> day='7'>Воскресенье</div>
		</div>
            <? } elseif ($route->type_day==5) { ?>
                <div class="tab" day='1'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<6)?'class="active"':'';?> day='1'>Будни</div>
		</div>
            <? } elseif ($route->type_day==6) { ?>
                <div class="tab" day='6'>
			<div href="?day_week=6" rel="nofollow" <?=($day_week>5)?'class="active"':'';?> day='6'>Выходные</div>
		</div>
            <? } elseif ($route->type_day==7) { ?>
                <div class="tab" day='<?=($day_week>6)?'7':"1";?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<7)?'class="active"':'';?> day='1'>Будни и суббота</div>
                        <div href="?day_week=7" rel="nofollow" <?=($day_week==7)?'class="active"':'';?> day='7'>Воскресенье</div>
		</div>
            <? } elseif ($route->type_day==8) { ?>
                <div class="tab" day='<?=($day_week>6)?'6':"1";?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<6)?'class="active"':'';?> day='1'>Будни</div>
                        <div href="?day_week=6" rel="nofollow" <?=($day_week==6)?'class="active"':'';?> day='6'>Суббота</div>
		</div>
            <? } elseif ($route->type_day==9) { ?>
                <div class="tab" day='<?=($day_week<5)?'1':(($day_week==5)?'5':'6');?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<5)?'class="active"':'';?> day='1'>Будни</div>
                        <div href="?day_week=5" rel="nofollow" <?=($day_week==5)?'class="active"':'';?> day='5'>Пятница</div>
                        <div href="?day_week=6" rel="nofollow" <?=($day_week>5)?'class="active"':'';?> day='6'>Выходные</div>
		</div>
            <? } elseif ($route->type_day==10) { ?>
                <div class="tab" day='<?=$day_week;?>'>
			<div href="?day_week=6" rel="nofollow" <?=($day_week==6)?'class="active"':'';?> day='6'>Cуббота</div>
                        <div href="?day_week=7" rel="nofollow" <?=($day_week==7)?'class="active"':'';?> day='7'>Воскресенье</div>
		</div>
            <? }elseif ($route->type_day==11) { ?>
                <div class="tab" day='<?=($day_week>6)?'6':(($day_week<5)?'1':'5');?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<5)?'class="active"':'';?> day='1'>Будни</div>
                        <div href="?day_week=5" rel="nofollow" <?=($day_week==5)?'class="active"':'';?> day='5'>Пятница</div>
                        <div href="?day_week=6" rel="nofollow" <?=($day_week==6)?'class="active"':'';?> day='6'>Суббота</div>
		</div>
            <? }elseif ($route->type_day==12) { ?>
                <div class="tab" day='<?=($day_week>6)?(($day_week==6)?'6':'7'):(($day_week<5)?'1':'5');?>'>
			<div href="?day_week=1" rel="nofollow" <?=($day_week<5)?'class="active"':'';?> day='1'>Будни</div>
                        <div href="?day_week=5" rel="nofollow" <?=($day_week==5)?'class="active"':'';?> day='5'>Пятница</div>
                        <div href="?day_week=6" rel="nofollow" <?=($day_week==6)?'class="active"':'';?> day='6'>Суббота</div>
                        <div href="?day_week=7" rel="nofollow" <?=($day_week==7)?'class="active"':'';?> day='7'>Воскресенье</div>
		</div>
            <? }?>
                
	
            </noindex>
        </div>
        <? ///////////////////////////////////////////////////////////////////////////// 
        // РСЯ
         echo $this->render('rek/_rsya13'); ?>
        <? ////////////////////////////////////////////////////////////////////////////////?>
	<div class="route-list">
            
	<? Pjax::begin(['id'=>'routesection']); ?>
            

   

            
               echo "<div class='listdirect2 ldirect2'><span class='arvmarsh'>Варианты маршрута:</span><ul>";
                foreach ($stationsall as $ketd=>$st) {
                        ?>
                        <li <?=($ketd==0)?'class="active"':'class="noactive"';?>><a href="#direct<?=$ketd;?>"><i></i><?=$st[0]['name'];?> - <?=$st[array_key_last($st)]['name'];?></a><i class="lisq"></i></li>
                        <?
                }
                echo "</ul></div>";
                
                
               $count0=0;
                foreach ($stationsall as $keyn=>$st) {
                ?>
		<div class="item item-1" id="direct<?=$keyn;?>" style="<?=($keyn!=0)?'display:none;width: 100%;':'width: 100%;';?>">
                    <ul class='route_direction'>
                        <? 
                        $f=true;
                       
                        foreach ($st as $s)  { 
                        
                            if(!ifhide($route->type_day,$day_week,$s['key_day'])) { continue; }  
                           //    var_dump($city);
                            ?> 
                            <li idi='<?=$s['id_station_rout'];?>'>
                                <?= $this->render('_timework', [
                               's' => $s,
                               'type_day' => $route->type_day,
                               'day_week' => $day_week,
                               'f'=>$f,
                                'flag2'=>false,
                                    'city'=>$city
                            ])  ?>
                                <? 
                                $f=false;
                                ?>
                            </li>
                        <? $count0++;
                        } ?>
                    </ul>
		</div>
                <? }
              //  } ?>
            
  
            <? Pjax::end(); ?>
	</div><!-- .route-list -->

	<div class="route-share">
	   <? /////////////////////////////////////////////////////////////////////////////
                             echo $this->render('rek/_podelit'); ?>
             <? ////////////////////////////////////////////////////////////////////////////////?>
	</div>
        <div class="route-stop">
            <div class="item item3">
                    <div class="level">Дополнительная информация:</div>
                    <span class="value">
                         Маршрут <?=$type_transport2;?> <?=($route->number!='-'&&$route->number!='—')?('№'.$route->number).' ':'';?><?=$route->name?> выполняет рейсы по расписанию.                           
                            Маршрут проходит через <?=$count0;?> <?=num2word($count0, array('остановку', 'остановки', 'остановок'));?> общественного транспорта.
                            <? if ($route->type_transport!=1) { echo "Рейсы могут выполнять автобусы разного класса."; }?>
                      
                    </span>
             </div> 
        </div>
        <noindex>
	<div class="route-alert">
		Расписание предоставлено в ознакомительных целях и может отличаться от расписания в определенное время! Мы работаем над периодичным обновлением.
	</div>
        </noindex>
 <? ///////////////////////////////////////////////////////////////////////////// 
// Adsense
 echo $this->render('rek/_ggl978'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>
	<div class="city-route-item v0">
		<div class="subtitle">
			<div class="tx">Похожие маршруты</div>
		</div>
            <? //var_dump($similar); ?>
		<ul>
                    <? foreach ($similar as $s) { ?>
			<li>
				<a href="<?=Url::toRoute(['site/route', 'id' => $s['id'],'route'=>$s,'city'=>$city]);?>">
					<span class="level"><?=$s['number'];?></span>
					<span class="value"><?=$s['name'];?></span>
				</a>
			</li>
                    <? } ?>
		</ul>
	</div><!-- .city-route-item -->
                
        
<? ///////////////////////////////////////////////////////////////////////////// 
 echo $this->render('rek/_rekomend'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>
        </div>
</div>

</div>
<? if (Yii::$app->user->can('admin')) { echo "route1 new1"; } ?>

 <div style="display:none;"><noindex><!--googleoff: all--><div class="modalwin" id="errorfindmodal"></div> <!--googleon: all--></noindex></div><div style="display:none;"><noindex><!--googleoff: all-->2011 © на базу данных Go on Bus . ru</noindex></div>

 
<? /*

<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <pre><? var_dump($stations0)?></pre>
    
     <ul>
    <?/* foreach ($stations as $s)  {
        echo "<li><a href='".Url::toRoute(['site/station', 'id' => $s->id])."'>".$s->name."</a></li>";
    }
    ?>
    </ul>
    -----------------------------------------------------
         <ul>
    <? foreach ($stations0 as $s)  {
        echo "<li><a href='".Url::toRoute(['site/station', 'id' => $s->id])."'>".$s->name."</a></li>";
    }*//*
    ?>
    </ul>

</div> */ ?>

