<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\Breadcrumbs\BreadcrumbsUtility;
use nirvana\jsonld\JsonLDHelper;
//use yii\bootstrap\ActiveForm;
//use yii\captcha\Captcha;
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

Yii::$app->view->registerJsFile('/js/city.js',['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $city->name;



if ($city->sklon=='') { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }
$names= explode('/', $city->sklon);
if (!(is_array($names) AND count($names)>4)) {
    for($tq=0;$tq<7;$tq++) { $names[$tq]=$city->name; }
}
//$this->params['breadcrumbs'][] = ['label' => "Расписание автобусов ", 'url' => [Url::toRoute(['site/city', 'id' => $city->id])]];
$this->params['breadcrumbs'][] = ['label' => "".$names[0], 'url' => [Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city])]];
$this->params['breadcrumbs'][] = ['label' => "Дачные маршруты", 'url' => [Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city])]];

JsonLDHelper::addBreadcrumbList();

$type_descr='';
$type_descr.='садово-дачных'; 

$descr="Расписание движения ".$type_descr." маршрутов общественного транспорта в ".$names[5]." с 15 апреля ".date('Y').". Список сезонных маршрутов.";

$this->registerMetaTag(
  ['name' => 'description', 'content' => $descr]
);
$this->title="Расписание дачных автобусов ".$names[1]." в ".date('Y')." году";
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);


?>
<div class="site-body wrapper">

<div class="site-content">
<div class="site-cont">
<div class="city-top">
		
        <h1 class="subtitle">Расписание дачных автобусов <?=$names[1];?></h1>
<? /* <noindex>
 <div class="custom">
                            <div class="route-stop infoep">
                                <div class="subtitle">Важная информация в связи с эпидемиологической ситуацией!</div>
                                <div class="item item-3">
                                    <div class="level">C 25 октября 2021 года возможны изменения в расписание движения общественного транспорта в <?=$names[5];?>. <a  href="/<?=$city->alias?>/nowork">Подробнее...</a></div>
                                </div>
                            </div>
                        </div>
    </noindex> */ ?>

        <div class="search">
                <div class="title">Найти расписание в <?=$names[5];?> по остановке или номеру сезонного маршрута</div>
                <div class="form">
                    <form id="search">
                        <input type="text" name="number" placeholder="№ маршрута"/>
                        <input type="text" name="text" placeholder="Остановка маршрута"/>
                        <input type="hidden" name="city_id" value="<?=$city->id;?>">
                        <button type="submit">Найти</button>
                    </form>
                </div>
        </div>
        
        <div class="block" id="find" style="display: none;"></div>

</div>
<? if (isset($city->info_dacha) && $city->info_dacha!='') { ?>
    <div class="route-stop">
                 
    
    <div class="item item-3">
        <div class="level">Дополнительная информация:</div>
        <div class="value">
            <?=$city->info_dacha;?>
        </div>
    </div> 
    </div>
<? } ?>
  <? if ($route_count==0) { ?> <noindex> 
           <div class="route-stop" style="margin-top: 0px;">
            <div class="item item-3" style="margin: 0px 30px 0px;">
                <div class="level">Информация о дачных маршрутах не полная.</div>
                <div class="value">
                    <p>
                        Мы работаем по наполнению сайта актуальной и полной информацией каждый день. Вы можете помочь в добавлении маршрутов и расписания на сайт.<br>
                        Присылайте расписание на <a href="mailto:admin@goonbus.ru">admin@goonbus.ru</a>.
                    </p>
                </div>
            </div>   
           </div></noindex>
  <? } ?>
      <? ///////////////////////////////////////////////////////////////////////////// 
// adsense
 echo $this->render('rek/_rsya_dacha'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>
<div class="city-route">
    <div class="tab">
            <a href="/<?=$city->alias?>" class="m-5 m1">Городские <br>маршруты</a> 
            <a href="/<?=$city->alias?>" class="m-6 m2">Пригородные <br>маршруты</a>  
            <a href="/<?=$city->alias?>" class="m-7 m3">Междугородние <br>маршруты</a>    
            <a href="#" class="m-4 active">Дачные <br>маршруты</a>   
    </div>
<? $fff=false; // флаг наличия маршрутво в городе 
  if (isset($routesd) && count($routesd)>0) { $fff=true; } ?>
        <?  
        if ($fff) {
            $flag=true;
            $rek=0; ?>
            <div class="block">
                <? if (isset($routesd) && count($routesd)>0) { ?>

                    <? /*if ($i==3) { ?>
                        <script src="//tp.media/content?promo_id=4576&shmarker=125311&campaign_id=45&trs=42228&locale=ru&powered_by=false&border_radius=5&show_logo=false&plain=false&color_background=%23ffffff&color_border=%23E71E43&color_button=%23E71E43&color_button_text=%23ffffff" charset="utf-8"></script>
                    <? }*/ ?>

                    <div class="city-route-item">
                        <div class="subtitle">
                                <h2 class="tx">Расписание автобусов</h2>
                                <div class="nm">(<?=count($routesd);?> <?=num2word(count($routesd), array('маршрут', 'маршрута', 'маршрутов'));?>)</div>
                        </div>
                        <ul>
                            <?
                            foreach ($routesd as $route)  { ?>
                                <li>
                                    <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
                                        <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
                                        <span class="value"><?=$route->name?></span>
                                    </a>
                                </li>
                                <? 
                                if ($rek==10) { /// РСЯ после 10 маршрутов
                                     echo $this->render('rek/_rsya12');  
                                }

                                if ($rek==30 AND $flag) { /// РСЯ после 10 маршрутов
                                     echo $this->render('rek/_rsya16');  $flag=false;
                                }


                                $rek++;
                            } ?>
                            <? //////////////////// ?>
                        </ul>
                    </div><!-- .city-route-item -->
                <? } ?>

            </div>
        <? } else { // нет маршрутов ?>
        <div class="route-stop" style="margin-top: 0px;">
            <div class="item item-3" style="margin: 0px 30px 0px;">
                <div class="level">Информация о маршрутах общественного транспорта отсутствует.</div>
                <div class="value">
                    <p>
                        Мы работаем по наполнению сайта актуальной и полной информацией каждый день. Вы можете помочь в добавлении маршрутов и расписания на сайт.<br>
                        По всем вопросам и предложениям пишите на почту <a href="mailto:admin@goonbus.ru">admin@goonbus.ru</a>.
                    </p>
                </div>
            </div>                                                                                                         </div>
        <? } ?>
</div>
    
    <? /* <div class="city-top bredc">
             <?= Breadcrumbs::widget([
                'homeLink' => BreadcrumbsUtility::getHome('Главная', Yii::$app->getHomeUrl()), // получаем главную страницу с микроданными
              'links' => isset($this->params['breadcrumbs']) ? BreadcrumbsUtility::UseMicroData($this->params['breadcrumbs']) : [], // получаем остальные хлебные крошки с микроданными     
              'options' => [ // назначаем контейнеру разметку BreadcrumbList  
                  'class' => 'breadcrumb',         
                  'itemscope itemtype' => 'http://schema.org/BreadcrumbList'
                  ],
]) ?></div> */ ?>
    
    </div>
</div>

</div>
 <div style="display:none;"><noindex><!--googleoff: all--><div class="modalwin" id="errorfindmodal"></div> <!--googleon: all--></noindex></div><div style="display:none;"><noindex><!--googleoff: all-->2011 © на базу данных Go on Bus . ru<!--googleon: all--></noindex></div>

<? /*
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul>
    <? foreach ($routes as $route)  {
        echo "<li><a href='".Url::toRoute(['site/route', 'id' => $route->id])."'>".$route->name."</a></li>";
    }
    ?>
    </ul>

</div>*/?>
