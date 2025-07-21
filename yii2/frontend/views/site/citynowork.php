<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
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
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id])]];
$this->params['breadcrumbs'][] = 'Расписание в нерабочие дни';

if ($city->sklon=='') { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }
$names= explode('/', $city->sklon);
if (!(is_array($names) AND count($names)>4)) {
    for($tq=0;$tq<7;$tq++) { $names[$tq]=$city->name; }
}

$type_descr='';

$descr="Расписание движения в нерабочие дни общественного транспорта в ".$names[5].".";

$this->registerMetaTag(
  ['name' => 'description', 'content' => $descr]
);
$this->title="Расписание автобусов ".$names[1]." в нерабочие дни";
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

?>
<div class="site-body wrapper">

    <div class="site-content">
        <div class="site-cont">
                <div class="city-top">

                        <h1 class="subtitle">Расписание автобусов <?=$names[1];?> в нерабочие дни</h1>
<? if (isset($city->info_nowork) AND $city->info_nowork!='') {?>
                        <div style='margin: 15px;line-height: 22px;'>
                            <span>
                                <?=$city->info_nowork;?>                            
                            </span>
                        </div>
                        <?} else { ?>
                        <div style='margin: 15px;line-height: 22px;'><span>С 30 октября(с 25 октября в некоторых регионах) по 7 ноября объявлены нерабочими днями на территории Российской Федерации, в связи со сложной эпидемиологической обстановкой. 
                                В указанный период в расписание общественного транспорта <?=$names[1];?> возможны изменения. В большинстве случаев маршруты работают по расписанию выходного дня. 
                                Будьте внимательны при ознакомлении с расписанием. Уточняйте информацию у перевозчиков.</span></div>
<? } ?>
                  <? ///////////////////////////////////////////////////////////////////////////// 
                // adsense
                 //echo $this->render('rek/_ggl771'); ?>
                <? ////////////////////////////////////////////////////////////////////////////////?>

                        <div class="search">
                                <div class="title">Найти расписание в <?=$names[5];?> по остановке или номеру маршрута</div>
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
            
      <? ///////////////////////////////////////////////////////////////////////////// 
// adsense
 echo $this->render('rek/_ggl771'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>      
            
<div class="city-route">
      <div class="block">
               
                <div class="city-route-item">
                    <div class="subtitle">
                            <h2 class="tx">Популярные маршруты <?=$names[1];?></h2>
                    </div>
                    <ul>
                        <? $i=0;
                       // $routes=shuffle($routes);
                        foreach ($routes as $route)  { if ($i>10){break;} ?>
                            <li>
                                <a href="<?=Url::toRoute(['site/route', 'id' => $route->id]);?>">
                                    <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
                                    <span class="value"><?=$route->name?></span>
                                </a>
                            </li>
                        <? $i++;
                        
                        } ?>
                    </ul>
                </div><!-- .city-route-item -->
      </div>
</div>
            
            </div>
    </div>
</div>

<div style="display:none;"><noindex><!--googleoff: all--><div class="modalwin" id="errorfindmodal"></div> <!--googleon: all--></noindex></div><div style="display:none;"><noindex><!--googleoff: all-->2011 © на базу данных Go on Bus . ru</noindex></div>

