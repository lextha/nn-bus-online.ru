<header class="header">
    <a href="index.html" class="logo">
        <img src="img/logo.svg" alt="logo">
        <h1 class="logo-title">
            <span class="logo__heading">Общественный транспорт</span>
            <span class="logo__text">Нижний Новгород</span>
        </h1>
    </a>
    <div class="header-info">
        <span class="header-info__time">
            <img src="img/time.svg" alt="time"> 12:26
        </span>
        <span class="header-info__weather">
            <img src="img/sun.svg" alt="weather"> -11°
        </span>
    </div>
    <nav class="navbar">
        <button class="navbar-item is_active">
            <img src="img/bus-1.svg" alt="img">
            <span class="navbar__text">Автобусы Маршрутки</span>
            <span class="navbar__online">12 онлайн</span>
        </button>
        <button class="navbar-item">
            <img src="img/bus-2.svg" alt="img">
            <span class="navbar__text">Троллейбусы</span>
            <span class="navbar__online">12 онлайн</span>
        </button>
        <button class="navbar-item">
            <img src="img/bus-3.svg" alt="img">
            <span class="navbar__text">Трамваи</span>
            <span class="navbar__online">12 онлайн</span>
        </button>
        <button class="navbar-item">
            <img src="img/bus-3.svg" alt="img">
            <span class="navbar__text">Электрички</span>
            <span class="navbar__online">12 онлайн</span>
        </button>
    </nav>
</header>

<main class="listing">
    <a href="./route.html" class="listing-item is_active">
        <span class="listing__num">1</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">1</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">1а</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">2</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">3</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">4-1</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">4-2</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">8А</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">10</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">12</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">15Б</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">25</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">110</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">115А</span>
        <span class="listing__text"></span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__text">ул. Морозова - АТП Центральн...</span>
        <span class="listing__online">5</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">1</span>
        <span class="listing__text">ул. Морозова - АТП Центральн...</span>
    </a>
    <a href="./route.html" class="listing-item">
        <span class="listing__num">24</span>
        <span class="listing__text">Скорая помощь - Магазин Цвет...</span>
    </a>
</main>

<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */
/*
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
  $this->params['breadcrumbs'][] = ['label' => "Общественный транспорт ".$names[1], 'url' => [Url::toRoute(['site/city', 'id' => $city->id,'city'=>$city])]];

  JsonLDHelper::addBreadcrumbList();

  $type_descr='';
  $type_descr.='городских';
  if (isset($routes[2]) && count($routes[2])>0) { $type_descr.=', пригородных'; }
  if (isset($routes[3]) && count($routes[3])>0) { $type_descr.=', междугородних'; }

  $descr="Расписание движения ".$type_descr." маршрутов общественного транспорта с автовокзалов и автостанций в ".$names[5].". Информация с обновлением в ".date('Y')." году.";

  $this->registerMetaTag(
  ['name' => 'description', 'content' => $descr]
  );
  $this->title="Расписание автобусов ".$names[1]."";
  $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

  for ($i=1;$i<4;$i++) { /// перемещаем маршруты с длинным номером в конец списка
  if (isset($routes[$i])) {
  foreach ($routes[$i] as $kret=>$r)  {
  foreach ($r as $ket=>$route)  {
  if (mb_strlen($route->number)>4) {
  $v = $route;
  unset($routes[$i][$kret][$ket]);
  $routes[$i][$kret][] = $v;
  }

  }
  }
  }
  }

  ?>
  <div class="site-body wrapper">

  <div class="site-content">
  <div class="site-cont">
  <div class="city-top">

  <h1 class="subtitle">Расписание автобусов <?=$names[1];?></h1>

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
  //echo $this->render('rek/_rsya13'); ?>
  <? ////////////////////////////////////////////////////////////////////////////////?>
  <div class="city-route">
  <? $fff=false; // флаг наличия маршрутво в городе

  $active_gor="";$active_prig="";$active_meg="";
  $fori=1;
  if (isset($routes[1]) && count($routes[1])>0) { $active_gor="active"; $fori=1; }
  elseif (isset($routes[2]) && count($routes[2])>0) { $active_prig="active"; $fori=2; }
  elseif (isset($routes[3]) && count($routes[3])>0) { $active_meg="active"; $fori=3; }

  ?>
  <div class="tab">
  <? if (isset($routes[1]) && count($routes[1])>0) { ?><a href="" class="m-1 <?=($fori==1)?'active':'';?>">Городские <br/>маршруты</a><? $fff=true; } ?>
  <? if (isset($routes[2]) && count($routes[2])>0) { ?><a href="" class="m-2 <?=($fori==2)?'active':'';?>">Пригородные <br/>маршруты</a><? $fff=true;} ?>
  <? if (isset($routes[3]) && count($routes[3])>0) { ?><a href="" class="m-3 <?=($fori==3)?'active':'';?>">Междугородние <br/>маршруты</a><? $fff=true; } ?>
  <? if (isset($dacha) && $dacha>0) { ?><a href="<?=$city->alias;?>/dacha" class="m-4">Дачные <br/>маршруты</a><? $fff=true; } ?>
  </div>

  <?
  if ($fff) {
  $flag=true;
  for ($i=$fori;$i<4;$i++) { $rek=0; ?>
  <div class="block" id="tabs-<?=$i;?>"<?=($fori!=$i)?"":"";?>>
  <? if (isset($routes[$i][1]) && count($routes[$i][1])>0) { ?>



  <div class="city-route-item">
  <div class="subtitle">
  <h2 class="tx"><? if ($i==1) { echo "Городские"; }
  elseif($i==2) { echo "Пригородные"; }
  elseif($i==3) { echo "Междугородние"; }
  ?> автобусы</h2>
  <div class="nm">(<?=count($routes[$i][1]);?> <?=num2word(count($routes[$i][1]), array('маршрут', 'маршрута', 'маршрутов'));?>)</div>
  </div>
  <ul>
  <?
  foreach ($routes[$i][1] as $route)  { ?>
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

  if ($i==2 AND $rek==5) { /// РСЯ для межгорода или пригород
  //       echo $this->render('rek/_rsya13');
  }

  $rek++;
  } ?>
  <? // не действующие ?>
  <? if (isset($routes_no_work[$i][1]) && count($routes_no_work[$i][1])>0) {
  ?>
  <li>
  <div class="nowork">Показать недействующие маршруты</div>
  </li>
  <?
  foreach ($routes_no_work[$i][1] as $route)  {                            //    var_dump($route); die(); ?>
  <li class="nowork">
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <? }
  } ?>
  <? //////////////////// ?>
  </ul>
  </div><!-- .city-route-item -->
  <? } ?>
  <?/// РСЯ после списка автобусов, если автобусов больше 25
  if ($rek>25) { echo $this->render('rek/_rsya15'); }
  ////////////////?>
  <? if (isset($routes[$i][4]) && count($routes[$i][4])>0) { ?>
  <div class="city-route-item v4">
  <div class="subtitle">
  <h2 class="tx"><? if ($i==1) { echo "Городские"; }
  elseif($i==2) { echo "Пригородные"; }
  elseif($i==3) { echo "Междугородние"; }
  ?> маршрутки</h2>
  <div class="nm">(<?=count($routes[$i][4]);?> <?=num2word(count($routes[$i][4]), array('маршрут', 'маршрута', 'маршрутов'));?>)</div>
  </div>
  <ul>
  <? foreach ($routes[$i][4] as $route)  { ?>
  <li>
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <?
  if ($rek==30 AND $flag) { /// РСЯ после 10 маршрутов
  echo $this->render('rek/_rsya16');  $flag=false;
  }
  $rek++;
  } ?>
  </ul>
  </div><!-- .city-route-item -->
  <? } ?>
  <? if (isset($routes[$i][2]) && count($routes[$i][2])>0) { ?>
  <div class="city-route-item v3">
  <div class="subtitle">
  <h2 class="tx"><? if ($i==1) { echo "Городские"; }
  elseif($i==2) { echo "Пригородные"; }
  elseif($i==3) { echo "Междугородние"; }
  ?> троллейбусы</h2>
  <div class="nm">(<?=count($routes[$i][2]);?> <?=num2word(count($routes[$i][2]), array('маршрут', 'маршрута', 'маршрутов'));?>)</div>
  </div>
  <ul>
  <? foreach ($routes[$i][2] as $route)  { ?>
  <li>
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <?
  if ($rek==30 AND $flag) { /// РСЯ после 10 маршрутов
  echo $this->render('rek/_rsya16');  $flag=false;
  }
  $rek++;
  } ?>

  <? // не действующие ?>
  <? if (isset($routes_no_work[$i][2]) && count($routes_no_work[$i][2])>0) {
  ?>
  <li>
  <div class="nowork">Показать недействующие маршруты</div>
  </li>
  <?
  foreach ($routes_no_work[$i][2] as $route)  {                            //    var_dump($route); die(); ?>
  <li class="nowork">
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <? }
  } ?>
  <? //////////////////// ?>

  </ul>
  </div><!-- .city-route-item -->
  <? } ?>
  <? if (isset($routes[$i][3]) && count($routes[$i][3])>0) { ?>
  <div class="city-route-item v2">
  <div class="subtitle">
  <h2 class="tx"><? if ($i==1) { echo "Городские"; }
  elseif($i==2) { echo "Пригородные"; }
  elseif($i==3) { echo "Междугородние"; }
  ?> трамваи</h2>
  <div class="nm">(<?=count($routes[1][3])?> <?=num2word(count($routes[$i][3]), array('маршрут', 'маршрута', 'маршрутов'));?>)</div>
  </div>
  <ul>
  <? foreach ($routes[$i][3] as $route)  { ?>
  <li>
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <?
  if ($rek==30 AND $flag) { /// РСЯ после 10 маршрутов
  echo $this->render('rek/_rsya16');  $flag=false;
  }
  $rek++;
  } ?>
  <? // не действующие ?>
  <? if (isset($routes_no_work[$i][3]) && count($routes_no_work[$i][3])>0) {
  ?>
  <li>
  <div class="nowork">Показать недействующие маршруты</div>
  </li>
  <?
  foreach ($routes_no_work[$i][3] as $route)  {                            //    var_dump($route); die(); ?>
  <li class="nowork">
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <? }
  } ?>
  <? //////////////////// ?>
  </ul>
  </div><!-- .city-route-item -->
  <? } ?>
  <? if (isset($routes[$i][5]) && count($routes[$i][5])>0) { ?>
  <div class="city-route-item v5">
  <div class="subtitle">
  <h2 class="tx"><? if ($i==1) { echo "Городские"; }
  elseif($i==2) { echo "Пригородные"; }
  elseif($i==3) { echo "Междугородние"; }
  ?> электрички</h2>
  <div class="nm">(<?=count($routes[$i][5])?> <?=num2word(count($routes[$i][5]), array('маршрут', 'маршрута', 'маршрутов'));?>)</div>
  </div>
  <ul>
  <? foreach ($routes[$i][5] as $route)  { ?>
  <li>
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <?
  if ($rek==30 AND $flag) { /// РСЯ после 10 маршрутов
  echo $this->render('rek/_rsya16');  $flag=false;
  }
  $rek++;
  } ?>
  <? // не действующие ?>
  <? if (isset($routes_no_work[$i][5]) && count($routes_no_work[$i][5])>0) {
  ?>
  <li>
  <div class="nowork">Показать недействующие маршруты</div>
  </li>
  <?
  foreach ($routes_no_work[$i][5] as $route)  {                            //    var_dump($route); die(); ?>
  <li class="nowork">
  <a href="<?=Url::toRoute(['site/route', 'id' => $route->id,'route'=>$route,'city'=>$city]);?>">
  <span class='level'><?=($route->number=='')?"-":$route->number;?></span>
  <span class="value"><?=$route->name?></span>
  </a>
  </li>
  <? }
  } ?>
  <? //////////////////// ?>
  </ul>
  </div><!-- .city-route-item -->
  <? } ?>
  </div>
  <? } ?>
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

  </div> */
?>
