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

Yii::$app->view->registerJsFile('/js/route1.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('/js/jquery.arcticmodal-0.3.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerCssFile('/css/jquery.arcticmodal-0.3.css');
Yii::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?render=6LcL0rQcAAAAABXq8yHHdxrN56i5D6Dk7nw4L7bT');

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

function full_trim($str){
    $str=str_replace("	", " ", $str);
    return trim(preg_replace('/\s{2,}/', ' ', $str));
}
/*if ($_SERVER['REMOTE_ADDR']=='5.187.69.205') {
    var_dump($this->item->marshrut);
}*/
$marshrut_in=[];
$marshrut_out=[];
if (is_array($marshrut) AND count($marshrut)>0) {
    foreach ($marshrut as $key => $value) {     //   var_dump($value[2],strpos($value[2],"in"));
        if (strpos($value[2],"in")>-1) { $marshrut_in[]=$value; }
        else { $marshrut_out[]=$value; }
    }
}
if (count($marshrut_out)<2) { unset($marshrut_out); } // если обратного маршрута нет, но в массив первый элемент записался
$time_flag=false;

if (count($marshrut_in)>0) { 
    foreach ($marshrut_in as $in) { 
    
        if (!is_array($in[1])) { //echo "<pre>"; var_dump($in); die();
            $time=explode("/",$in[1]);
            if (count($time)==2) { 
                if ($time_flag!='all') { $time_flag="vyh"; }
            } 
            elseif (count($time)>2) {
                $time_flag="all"; 
            }
        }
    }
}
/*********** ПРЯМОЙ МАРШРУТ *************/
function vydel($text){
    $ttl=explode("|", $text);
    $text=$ttl[0];
    $text=preg_replace('/\(([^\)(]*)( )+(([^\)(]*))\)/Uu', '($1++$3)', $text);
    $text=preg_replace('/\(([^\)(]*)( )+(([^\)(]*))\)/Uu', '($1++$3)', $text);
    $text=preg_replace('/\(([^\)(]*)( )+(([^\)(]*))\)/Uu', '($1++$3)', $text);
    $array=explode(" ", $text);
    $flag=false;
    $arr='';
   /* if ($_SERVER['REMOTE_ADDR']=='5.187.70.135') {
        var_dump($array);
    }*/
    foreach ($array as $key => $value) {
        $value= str_replace("++", " ", $value);
        if (preg_match("/(\d+)[:-](\d+)(.*)$/", $value)) {
            if ($flag) {
                $arr.="</span>";
            }
            $arr.=" <span>".$value."</span>";
            $flag=false;
        } else {
            if ($flag) {
                $arr.=$value;
            } else {
                $arr.=" <span>".$value." ";
            }
           $flag=true;
        }
    }
    if (isset($ttl[1])) {
        $arr.="<span>".$ttl[1]."</span>";
    }
    return $arr;
}
function marshrut_html($marsh) {
 $i=1;$ii=0;
 $html='';
    foreach ($marsh as $in) {
        $ii++;
        if ($in[0]!='legend') {
        if (!is_array($in[1])) { $time=explode("/",$in[1]); } else { $time=[]; }
        $html.="<li>";
        /*if ($i==0) { 
            $html.="class='odd";
            if ($ii==count($marsh)) { $html.=" last"; } 
            $html.="'";
            $i++; 
        } else { 
            if ($ii==0) { $html.="class='first'";  } 
            if ($ii==count($marsh)) { $html.="class='last'"; } 
            $i=0; 
        } */
        
      //  $html.='>';
        $time1=array();
        foreach ($time as $key1 => $tt1) {
            $tt1=full_trim($tt1);
          //  if ($_SERVER['REMOTE_ADDR']=='82.151.118.232') {
                if ($tt1=='') { $time1[$key1]=''; } else { 
                    $expl=[];//explode("*", $tt1);
                    if (count($expl)>1) { 
                        $time1[$key1]='';
                        //$tt1=explode("*", $tt1); 
                        foreach ($expl as $val_t) {
                            //if (iconv_strlen($val_t)>8) { $val_t=ex$val_t }
                          /*  $val_t=str_replace("(red)"," ",$val_t);
                            $val_t=str_replace("(green)"," ",$val_t);*/
                          
                            $time1[$key1].="<span>".$val_t."</span> ";
                        }
                    } 
                    else { $time1[$key1]=vydel($tt1); }
                }
        /*    } else {
               $time1[$key1]= str_replace(" ", "</span> <span>", $tt1);
                if (!empty($time1[$key1])) { $time1[$key1]="<span>".$time1[$key1]."</span>"; }
           }*/
        }
        $time=$time1;
        
       if (count($time)<2) {
           $html.='<div class="level">'.$in[0].'</div>';
           if (isset($time[0]) AND $time[0]!='') { $html.='<div class="hide show bud_t value"><span class="title_time">Будни</span>'.$time[0].'</div>'; }
       }
       elseif (count($time)==2) {
           $html.='<div class="level">'.$in[0].'</div><div class="hide show bud_t value"><span class="title_time">Будни</span>'.$time[0].'</div><div class="hide show vyh_t value" style="display:none;"><span class="title_time">Выходные</span>'.$time[1].'</div>';
       }
       elseif (count($time)>2) {
           $html.='<div class="level">'.$in[0].'</div><div class="hide show bud_t_week_pn value"><span class="title_time">Понедельник</span>'.$time[0].'</div>'
                   . '<div class="hide show bud_t_week_vt value" style="display:none;"><span class="title_time">Вторник</span>'.$time[1].'</div>'
                   . '<div class="hide show bud_t_week_sr value" style="display:none;"><span class="title_time">Среда</span>'.$time[2].'</div>'
                   . '<div class="hide show bud_t_week_cht value" style="display:none;"><span class="title_time">Четверг</span>'.$time[3].'</div>'
                   . '<div class="hide show bud_t_week_pt value" style="display:none;"><span class="title_time">Пятница</span>'.$time[4].'</div>'
                   . '<div class="hide show bud_t_week_sb value" style="display:none;"><span class="title_time">Суббота</span>'.$time[5].'</div>'
                   . '<div class="hide show bud_t_week_vs value" style="display:none;"><span class="title_time">Воскресенье</span>'.$time[6].'</div>'
                   . '';
       }
   // $html.='<div class="name">'.$in[0].'</div>';
    $html.="</li>";
    }
    }
    return $html;
}
$html='';$html_out='';
 if (isset($marshrut_in)) { $html=marshrut_html($marshrut_in); }
 if (isset($marshrut_out)) { $html_out=marshrut_html($marshrut_out); }
 
 /////////////////////
 //if ($_SERVER['REMOTE_ADDR']=='82.151.118.232') {
  //  var_dump($extra_fields);
 //$title_item=stripcslashes($this->item->title);
 //$title_item=str_replace("\"","",$this->item->title);
 $title_item=$route->name;//$this->item->title;
 $pos = strpos($title_item, "№");
 //@$extra_fields[1]->value=str_replace("\\", "",$extra_fields[1]->value);
 $title_str='';
 /*$city_s=explode("/",$city->sklon);//explode("/",$this->item->category->name_p);
if (count($city_s)<2) { $rtyt=$city_s[0]; $city_s=array(); for($i=0;$i<7;$i++) { $city_s[$i]=$rtyt; }  } else { $rtyt==''; }
if ($rtyt=='') {$city_s=array(); for($i=0;$i<7;$i++) { $city_s[$i]=$city->name; }   }//{ $name[$i]=$this->item->category->name; }   }
  */
 //var_dump($this->item->category);

if ($pos === false OR $route->type_direction=='3') {
     if (($route->number!='')&&($route->number!='-')&&($route->number!='—')) { $title_str.="".$route->number; } 
     $title_str.=" "; 
}
$title_str.=$title_item;

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

//$title_str=((($route->number!='-')&&($route->number!='—')&&($route->number!='-'))?('№'.$route->number).' ':'').$route->name;
if (strlen($extra_fields[4]->value)>5) {
    $extra_fields[4]->value=str_replace("\\", "", $extra_fields[4]->value);
    if ($route->type_direction==3) { 
    $inf=explode("|",$extra_fields[4]->value); 
        if (count($inf)>1) {
            $title_str.=". Отправление от ".$inf[0]."";
            $dsdsd="Маршрут следования, отправление от ".$inf[0];
        } else { $dsdsd="Маршрут следования, информация о месте отправления и автовокзале"; }
    }
} else {
    $dsdsd="Маршрут следования, информация о месте отправления и автовокзале";
}

if ($route->type_direction=='3') {
     $this->title="Расписание ".$type_transport." ".$title_str;
} else {
    $this->title="Расписание ".$type_transport." ".$title_str." в ".$city_s[5]."";
}




/*$title = html_entity_decode($title,ENT_QUOTES, 'utf-8');
//$title=$title.$app->get('sitename');
$title=preg_replace('/[\s]{2,}/', ' ', $title);*/

if ($route->type_direction=='3') {
     $descr="Расписание движения междугороднего автобуса ".$title_str.". ".$dsdsd.". Обновление расписания в ".date("Y")." году.";
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

/*
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id])]];
$this->params['breadcrumbs'][] = $route->name;
*/
$this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city])]];
$this->params['breadcrumbs'][] = ['label' => $type_transport_iminit.' '.$route->number, 'url' => [Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city])]];// $route->name;
JsonLDHelper::addBreadcrumbList();
 //}
 ////////////////////

if ($city->sklon=='') { $city->sklon=$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name."/".$city->name; }
$names= explode('/', $city->sklon);
if (!(is_array($names) AND count($names)>4)) {
    for($tq=0;$tq<7;$tq++) { $names[$tq]=$city->name; }
}

 ?>
<div class="site-body wrapper">

<div class="site-content">
<div class="site-cont">
	<div class="route-top">
		
		<h1 class="subtitle">Расписание <?=$type_transport;?> <? if ($pos === false) { if ($route->number!='') { echo "№"; } ?><?=$route->number;?> <?php } echo $title_item; ?></h1>
 <? /* <noindex>
                <div class="custom">
                            <div class="infoep">
                                <div class="subtitle">Важная информация в связи с эпидемиологической ситуацией!</div>
                                <div class="item item-3">
                                    <div class="level">C 25 октября 2021 года возможны изменения в расписание движения общественного транспорта в <?=$names[5];?>. <a href="/<?=$city->alias?>/nowork">Подробнее...</a></div>
                                </div>
                            </div>
                        </div>
     </noindex> */ ?>
             <?  /*$jsona=json_decode($this->item->params);
     //var_dump($jsona->catMetaAuthor);            
    
                if ($user->id) { echo "<div class='adminform'><a target='_blank' href='https://goonbus.ru/administrator/index.php?option=com_k2&view=item&cid=".$this->item->id."'>РЕДАКТИРОВАТЬ</a></div>"; }
  ?>
                                <? if ($jsona->catMetaAuthor!='') { ?>
                    <div class="custom">
                            <div class="route-stop infoep">
                                <div class="subtitle"><a href="<?=$jsona->catMetaAuthor?>">Последние новости общественного транспорта в <?php echo $city_s[5]; ?></a></div>
                            </div>
                        </div>
<? } */?>
                <?php
            /*    $active       = $app->getMenu()->getActive();
                 if ((strpos($active->alias, '-kz')==false) AND (strpos($active->alias, '-ua')==false) AND (strpos($active->alias, '-by')==false)) {
                jimport( 'joomla.application.module.helper' ); // подключаем нужный класс, один раз на странице, перед первым выводом
                $module = JModuleHelper::getModules('top2'); // получаем в массив все модули из заданной позиции
                $attribs['style'] = 'none'; // задаём, если нужно, оболочку модулей (module chrome)
              
                
                    if ($module[0]) {
                echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                } else {
                  
      
                    ?>
                <div class="custom">
                    <div class="route-stop infoep">
                        <div class="subtitle">Важная информация в связи с эпидемиологической ситуацией!</div>
                        <div class="item item-3">
                            <div class="level">C 28 марта 2020 года внесены изменения в расписание движения общественного транспорта в <?php echo $city_s[5]; ?>. <a href="/ogranicheniya/<?=$active->alias;?>">Подробнее...</a></div>
                        </div>
                    </div>
                </div>
                <?
                }
                 }*/
                ?>
                
<? ///////////////////////////////////////////////////////////////////////////// 
// adsense
 echo $this->render('rek/_ggl771'); ?>
                <? /*if ($route->type_direction=='3') { ?>
                    <script src="//tp.media/content?promo_id=4576&shmarker=125311&campaign_id=45&trs=42228&locale=ru&powered_by=false&border_radius=5&show_logo=false&plain=false&color_background=%23ffffff&color_border=%23E71E43&color_button=%23E71E43&color_button_text=%23ffffff" charset="utf-8"></script>
               <? }*/
?>
<? ////////////////////////////////////////////////////////////////////////////////?>

<? /* Breadcrumbs::widget([
                'homeLink' => BreadcrumbsUtility::getHome('Главная', Yii::$app->getHomeUrl()), // получаем главную страницу с микроданными
              'links' => isset($this->params['breadcrumbs']) ? BreadcrumbsUtility::UseMicroData($this->params['breadcrumbs']) : [], // получаем остальные хлебные крошки с микроданными     
              'options' => [ // назначаем контейнеру разметку BreadcrumbList  
                  'class' => 'breadcrumb',         
                  'itemscope itemtype' => 'http://schema.org/BreadcrumbList'
                  ],
]) */?>
                <ul class="breadcrumb">
                <li>
                    <a href="<?=Url::toRoute([Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city])]);?>">
                       <?=$city->name;?>
                    </a>
                </li>
                <li><?=$type_transport_iminit.' '.$route->number;?></li>
            </ul>
                <div class="clear_fix"></div>
	</div><!-- .route-top route2 -->

                

		<div class="route-stop" id="route-stop" idi="<?=$route->id;?>">
                            
			<h2 class="subtitle">Остановки и время отправления <?=$type_transport2;?></h2>
              
                        <? if (!$route->active) { ?><div class="item item-3" style="background: #fff5f5;border: 1px solid #ff0e51;"><div class="level">Маршрут не действует</div></div><? } ?>
            
			
					
                               
                                 
                                    <? if (strlen($extra_fields[4]->value)>5) { $extra_fields[4]->value=str_replace("\\", "", $extra_fields[4]->value);?>
                                    <? 
                                    $inf=explode("|",$extra_fields[4]->value); 
                                    if ($route->type_direction==3 AND (count($inf)>1)) { ?>
                                     <div class="item item-1">
                                            <div class="level">Информация о месте отправления:</div>
                                            <div class="value">
                                        <? $inf=explode("|",$extra_fields[4]->value); 
                                            echo $inf[0]."<br>";
                                            echo "Телефоны: ";
                                            $tel=explode(";",$inf[3]);
                                            foreach ($tel as $t) { echo $t; if (next($tel)) { echo ", "; } }
                                            echo "<br>";
                                            echo "Адрес: ".$inf[4];
                                        ?></div>
                                     </div>
                                    <? } else { ?>
                                        <div class="item item-1">
                                            <div class="level">График работы:</div>
                                            <div class="value"><?=nl2br(trim($extra_fields[4]->value));?></div>
                                           <?/* <noindex><!--googleoff: all--><div class="finderr" rel="1"><a href="#">Нашли ошибку?</a></div><!--googleon: all--></noindex>*/?>
                                        </div>
                                    <? } ?>
                                    <? } ?>
                        
                                    <? if (strlen($extra_fields[5]->value)>5) { $extra_fields[5]->value=str_replace("\\", "", $extra_fields[5]->value); ?>
                                    <div class="item item-2">
			<div class="level">Расписание или интервал движения:</div>
                        <div class="value"><?
                                        $str=nl2br($extra_fields[5]->value);
                                        $len= intdiv(mb_strlen($str),3);
                                        $str2=mb_strimwidth($str, 0, $len, '...'); 
                                        
                                        if ($str!=$str2) {
                                            echo "<span style='font-weight: normal;-webkit-mask-image:-webkit-gradient(linear, left top, right bottom, from(rgba(0,0,0,1)), to(rgba(0,0,0,0)))'>".$str2."</span>";
                                            echo " <div><span class='getrasp' id='".$route->id."'>Показать расписание</span></div>"; 
                                        } else {
                                            echo $str2.".";
                                        }
                          ?></div>
                                 <?/*   <noindex><!--googleoff: all--><div class="finderr" rel="2"><a href="#">Нашли ошибку?</a></div><!--googleon: all--></noindex>*/?>
                                    </div>
                                    <? }?>    
                        
                                    <? if (strlen($extra_fields[6]->value)>5) { $extra_fields[6]->value=str_replace("\\", "", $extra_fields[6]->value); ?>
                                               <div class="item item-4">
                                   <div class="level">Маршрут движения:</div>
                                               <div class="value"><?=nl2br($extra_fields[6]->value)?></div>
                                             <?/*  <noindex><!--googleoff: all--><div class="finderr" rel="3"><a href="#">Нашли ошибку?</a></div><!--googleon: all--></noindex>*/?>
                                               </div>
                                    <? }?>    
                        
                                    <? //if ($_SERVER['REMOTE_ADDR']=='5.187.78.115') { 
                                        if (count($marshrut_in)>0) {
                                            echo '<div class="item item-3"><div class="level">Дополнительная информация:</div><div class="value">';
                                           if ($route->type_direction==3) {
                                               echo "<p>Регулярный междугородний маршрут ".$title_str." выполняет рейсы по расписанию.".((@$inf[0]!='')?" Автобус отправляется от станции «".$inf[0]."»</p>":"");// Автобус отправляется от станции «".$inf[0]."»</p>"; 
                                           } elseif ($route->type_direction==2) {
                                               echo "<p>Пригородный маршрут ".$title_str." выполняет рейсы по расписанию. Уточняйте сезонность перевозок этого маршрута."; 
                                           } else {
                                               $trans_predpr='';
                                               //var_dump($extra_fields);
                                               if (strlen($extra_fields[7]->value)>3) { $trans_predpr='Обслуживает это направление '.str_replace("\\", "", $extra_fields[7]->value).". ";}
                                               if ($pos === false) { $num_a=" ".$route->number; } else { $num_a=''; }
                                               
                                               if (count($marshrut_in)>0) { $count_t=" Маршрут проходит через ".count($marshrut_in)." ".num2word(count($marshrut_in), array('остановку', 'остановки', 'остановок'))." общественного транспорта."; } else { $count_t=''; }
                                               echo "<p>Маршрут".$num_a." ".$type_transport." ".$title_item." выполняет рейсы по расписанию. ".$trans_predpr."".$count_t."</p>"; 
                                           }
                                           echo "</div></div>";
                                        } 
                                 //   }
                                    ?>
                                    
                                </div><!-- .route-stop --> 
                                    
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
                                <div class="route-stop daytab">
                                <? if ($time_flag=="vyh") { ?> 
                                        <div class="tab buttons_d">
						<a href="#" class="bud active">Будни</a>
                                                <a href="#" class="vyh">Выходные</a>
					</div>
                                        <? } elseif ($time_flag=="all") { ?>
                                        <div class="tab buttons_all">
                                                     <a href="#pn" class="week_pn active">Пн</a>
                                                     <a href="#vt" class="week_vt">Вт</a>
                                                     <a href="#sr" class="week_sr">Ср</a>
                                                     <a href="#cht" class="week_cht">Чт</a>
                                                     <a href="#pt" class="week_pt">Пт</a>
                                                     <a href="#sb" class="week_sb">Сб</a>
                                                     <a href="#vs" class="week_vs">Вс</a>
                                        </div>
                                        <? } ?>
                                </div>
                                <? ///////////////////////////////////////////////////////////////////////////// 
                                    // РСЯ
                                     echo $this->render('rek/_rsya13'); ?>
                                    <? ////////////////////////////////////////////////////////////////////////////////?>
				<div class="route-list" atr='false'>
				<? if (isset($marshrut_in) AND count($marshrut_in)>0) { ?>
                                        <div class="item item-1" <? if (!isset($marshrut_out)) { echo "style='width:100%'"; }?>>
                                                    <div class="subtitle"><span>Прямой маршрут</span><?/* <a href="#" class="print"  onclick="window.print();"></a>*/ ?></div>
							<ul class="route-list-ul">
									<?/*<div class="name"><a href="#">ул.Королева</a></div>
									<div class="hide show">
										<span>5:28</span> <span>6:01</span> <span>6:18</span> <span>6:21</span> <span>6:35</span> <span>10:30</span> <span>11:20</span>
										<span>12:10</span> <span>13:00</span> <span>20:00</span> <span>20:40</span> <span>21:20</span> <span>22:00</span>
										<div class="clear"></div>
										<span>5:28</span> <span>6:01</span> <span>6:18</span> <span>6:21</span> <span>6:35</span> <span>10:30</span> <span>11:20</span>
										<span>12:10</span> <span>13:00</span> <span>20:00</span> <span>20:40</span> <span>21:20</span> <span>22:00</span>
									</div>*/
 
$html=str_replace("(red)"," ",$html); 
$html=str_replace("(green)"," ",$html); 
$html=str_replace("(darkblue)"," ",$html);
$html=str_replace("(#ff69b4)"," ",$html);


 echo $html; ?>
                                                        </ul>		
					</div><!-- #column -->
                                <? } ?>

				<? if (isset($marshrut_out) AND count($marshrut_out)>0) { 
                                    echo $this->render('rek/_megdu'); ?>	
					<div class="item item-2">
                                                        <div class="subtitle"><span>Обратный маршрут</span><? /* <a href="#" class="print" onclick="window.print();"></a>*/ ?></div>
							
                                                        <ul class="route-list-ul">
								<? 
                                                                $html_out=str_replace("(red)"," ",$html_out); 
$html_out=str_replace("(green)"," ",$html_out); 
$html_out=str_replace("(darkblue)"," ",$html_out);
$html_out=str_replace("(#ff69b4)"," ",$html_out);



echo $html_out?>
							</ul>		
					</div><!-- #column -->
                                <? } ?>
                                          <? /* <noindex><!--googleoff: all--><div class="finderr" rel="5"><a href="#">Нашли ошибку?</a></div><!--googleon: all--></noindex>*/ ?>
				</div><!-- #budni -->			
			
			
			<div class="route-share">
                           
                            <? ///////////////////////////////////////////////////////////////////////////// 
                             echo $this->render('rek/_podelit'); ?>
                            <? ////////////////////////////////////////////////////////////////////////////////?>
                        </div>
		
			  <?php //echo $this->item->event->K2CommentsBlock; ?>
                <noindex>
                    <div class="route-alert">
                        Расписание предоставлено в ознакомительных целях и может отличаться от расписания в определенное время!
Мы работаем над периодичным обновлением.
                    </div>
                </noindex>
                                
 <? ///////////////////////////////////////////////////////////////////////////// 
// Adsense
 echo $this->render('rek/_ggl978'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>

                                <? if (is_array($similar) AND count($similar)>0) { ?>
	<div class="city-route-item v0">
		<div class="subtitle">
			<div class="tx">Похожие маршруты</div>
			<? /* <div class="nm">(10 маршрутов)</div> */?>
		</div>
            <? //var_dump($similar); ?>
		<ul>
                    <? foreach ($similar as $s) { ?>
			<li>
				<a href="<?=Url::toRoute(['site/route', 'id' => $s['id'],'city'=>$city,'route'=>$s]);?>">
					<span class="level"><?=$s['number'];?></span>
					<span class="value"><?=$s['name'];?></span>
				</a>
			</li>
                    <? } ?>
		</ul>
	</div><!-- .city-route-item -->
                                <? } ?>
                 <? /*   {module 116} 
                    {module 140}*/ ?>
                    
                   <? //if ($_SERVER['REMOTE_ADDR']=='82.151.118.232') { 
                   /* ?>
                    <div class="city-route-item v0">
		<div class="subtitle">
			<div class="tx">Подобные маршруты</div>
		</div>
                        <ul>
                             <?for ($i=1;$i<10;$i++) {
                                
                                ?>
                                <li><a href="<?=$this->item->pohozhie[$i]->link;?>">
                                    <span class="level"><?=$this->item->pohozhie[$i]->ef[1]->value;?></span>
                                    <span class="value"><?=$this->item->pohozhie[$i]->title;?></span>
                                    </a>
                                </li>
                                <?
                            }
                        ?> 
                        </ul>
                    </div>
                   <? */ // } ?>
		<?/*	<p>Наш сайт – это один из лучших ресурсов в русскоязычном интернете, где вы сможете найти всю актуальную информацию о движении автобусов в своем городе, представленную в удобной и простой для понимания форме. Для того, чтобы ваша поездка оказалась максимально приятной, мы предусмотрели возможность распечатать интересующее расписание. Теперь вы никогда не опоздаете на нужный автобус!</p>		
		*/?>    

<? ///////////////////////////////////////////////////////////////////////////// 
 echo $this->render('rek/_rekomend'); ?>
<? ////////////////////////////////////////////////////////////////////////////////?>
    <div style="display:none;">
            <div class="modalwin" id="info">
                <div class="pagetitle">Информация по остановке <span class="title-ost"></span></div>
                    <p class="info-interval"></p>

      <? ///////////////////////////////////////////////////////////////////////////// 
    // adsense
     echo $this->render('rek/_ggl774'); ?>
    <? ////////////////////////////////////////////////////////////////////////////////?>

                    <p class="info-time"></p>
                    <div class="info-pohozhie"></div>
                    <a href="#" class="close arcticmodal-close"></a>
            </div>
    </div>
    <div style="display:none;"><noindex><!--googleoff: all--><div class="modalwin" id="errorfindmodal"></div> <!--googleon: all--></noindex></div><div style="display:none;"><noindex><!--googleoff: all-->2011 © на базу данных Go on Bus . ru <? echo "route2 new";?></noindex></div>
</div>
</div>
</div><? if (Yii::$app->user->can('admin')) { echo "route2 new";  } ?>