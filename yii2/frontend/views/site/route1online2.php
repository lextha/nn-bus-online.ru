<?

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\Pjax;
use nirvana\jsonld\JsonLDHelper;


Yii::$app->view->registerJsFile('/js/script.js?v=3', ['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=2270d43a-3606-43eb-a583-974f7519ba49',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerCssFile('/css/sty.css');
if ($city->sklon == '') {
    $city->sklon = $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name;
}
$city_s = explode('/', $city->sklon);
if ($route->type_direction == '3') {
    $type_for_zag = 'междугороднего';
    $type_for_marshr = 'междугородней';
} elseif ($route->type_direction == '2') {
    $type_for_zag = 'пригородного';
    $type_for_marshr = 'пригородной';
} else {
    $type_for_zag = 'городского';
    $type_for_marshr = 'городской';
}

if ($route->type_transport=='2') { $type_transport="троллейбуса"; $type_transport2=$type_for_zag." троллейбуса"; $type_transport_iminit="Троллейбус"; $type_transport_iminit_many="Троллейбусы"; } 
elseif ($route->type_transport=='4') { $type_transport="маршрутки"; $type_transport2=$type_for_marshr." маршрутки"; $type_transport_iminit="Маршрутка"; $type_transport_iminit_many="Маршрутки";} 
elseif ($route->type_transport=='3') { $type_transport="трамвая"; $type_transport2=$type_for_zag." трамвая"; $type_transport_iminit="Трамвай"; $type_transport_iminit_many="Трамваи";} 
elseif ($route->type_transport=='5') { $type_transport="электрички"; $type_transport2=$type_for_marshr." электрички"; $type_transport_iminit="Электричка";$type_transport_iminit_many="Электрички";} 
elseif ($route->type_transport=='6') { $type_transport="речного транспорта"; $type_transport2=$type_for_marshr." речного транспорта"; $type_transport_iminit="Речного транспорт"; $type_transport_iminit_many="Речной транспорт";} 
elseif ($route->type_transport=='7') { $type_transport="канатной дороги"; $type_transport2=$type_for_marshr." канатной дороги"; $type_transport_iminit="Канатная дорога"; $type_transport_iminit_many="Канатная дорога";} 
else { $type_transport="автобуса"; $type_transport2=$type_for_zag." автобуса"; $type_transport_iminit="Автобус"; $type_transport_iminit_many="Автобусы";}


//$title_str=(($route->number!='-'&&$route->number!='—')?('№'.$route->number).' ':'').$route->name;
$title_str = (($route->number != '-' && $route->number != '—') ? ($route->number) . ' ' : '') . $route->name;

if ($route->type_direction == '3') {
    $this->title = "Расписание " . $type_transport . " " . $title_str;
} else {
    $this->title = $type_transport_iminit . " " . $route->number . " - расписание, отслеживание онлайн в " . $city_s[5]; //$this->title="Расписание ".$type_transport." ".$title_str." в ".$city_s[5]."";
}

if ($route->type_direction == '3') { // межгород
    $descr = "Расписание движения междугороднего автобуса " . $title_str . ". Маршрут следования, информация о месте отправления и автовокзале. Обновление расписания в " . date("Y") . " году.";
} else {
    $descr = "Расписание " . $type_transport . " " . $title_str . " в " . $city_s[5] . ". В режиме онлайн на карте города и схеме маршрута.";
}
$descr = preg_replace('/[\s]{2,}/', ' ', $descr);
$descr = html_entity_decode($descr, ENT_QUOTES, 'utf-8');

$this->registerMetaTag(
        ['name' => 'description', 'content' => $descr]
);
$this->registerMetaTag(
        ['name' => 'keywords', 'content' => "расписание " . $type_transport . ", расписание, маршрут, время работы, онлайн, на карте, на схеме"]
);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);


$this->params['breadcrumbs'][] = ['label' => $type_transport_iminit . ' ' . $route->number, 'url' => [Url::toRoute(['site/route', 'id' => $route->id, 'route' => $route, 'city' => $city])]]; // $route->name;

JsonLDHelper::addBreadcrumbList();


//$this->title = $route->name;
/*
  $this->params['breadcrumbs'][] = ['label' => $city->name, 'url' => [Url::toRoute(['site/city', 'id' => $city->id])]];
  $this->params['breadcrumbs'][] = ['label' => $type_transport_iminit.' '.$route->number, 'url' => [Url::toRoute(['site/route', 'id' => $route->id])]];// $route->name;

  JsonLDHelper::addBreadcrumbList();
 */
function num2word($num, $words) { //echo num2word(50, array('год', 'года', 'лет'));
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

function ifhide($type_day, $day_week, $key_day) {
    // var_dump($type_day,$day_week,$key_day);
    $chars = preg_split("//u", $key_day, 0, PREG_SPLIT_NO_EMPTY);
    ///var_dump($chars);
    if ($type_day == 1) {
        if ($day_week < 6 AND $chars[0]) {
            return true;
        } elseif ($day_week > 5 AND $chars[5]) {
            return true;
        } else {
            return false;
        }
    } elseif ($type_day == 2) {
        $day_week = $day_week - 1;
        return $chars[$day_week];
    } elseif ($type_day == 4) {
        if ($day_week < 6 AND $chars[0]) {
            return true;
        } elseif ($day_week == 6 AND $chars[5]) {
            return true;
        } elseif ($day_week == 7 AND $chars[6]) {
            return true;
        } else {
            return false;
        }
    } elseif ($type_day == 5) {
        
    }

    return true;
}

$route_n = ($route->number != '-' && $route->number != '—') ? ('' . $route->number) . ' ' : '';

if ($city->sklon == '') {
    $city->sklon = $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name;
}
$names = explode('/', $city->sklon);
if (!(is_array($names) AND count($names) > 4)) {
    for ($tq = 0; $tq < 7; $tq++) {
        $names[$tq] = $city->name;
    }
}
?><header class="header">
    <a href="/" class="logo">
        <img src="img/logo.svg" alt="logo">
        <div class="logo-title">
            <span class="logo__heading">Общественный транспорт</span>
            <span class="logo__text"><?=$names[0]?></span>
        </div>
    </a>
    <div class="header-info">
       <span class="header-info__time">
            <img src="/img/time.svg" alt="time"> 
        </span>
        <span class="header-info__weather">
            
        </span>
    </div>
    <h1 class="title">
        <span class="title__heading"><?= $type_transport_iminit; ?> <b><?= $route_n; ?></b></span>
        <span class="title__address"><?= $route->name ?></span>
        <? /* <span class="title__address">Магазин Цветы по центру</span> */ ?>
    </h1>
    <div class="dropdown">
        <button class="dropdown__btn">Другие направления маршрута</button>


        <div class="dropdown-content">  
            <? foreach ($stationsall as $ketd => $st) {
                ?>
                <button class="dropdown__item <?= ($ketd == 0) ? 'is_active' : ''; ?>" val="<?=$ketd;?>"><?= $st[0]['name']; ?> - <?= $st[array_key_last($st)]['name']; ?></button>

            <? }
            ?>

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

    <? foreach ($stationsall as $keyn => $st) {
        ?>
        <ul class="stops" id="direct<?= $keyn; ?>" style="<?= ($keyn != 0) ? 'display:none;' : ''; ?>">

            <?
            $f = true;
            $count0 = 0;
            $countst=count($st);
            if ($countst>9) { $allst=true;}else { $allst=false; }
            foreach ($st as $s) {
                if (!ifhide($route->type_day, $day_week, $s['key_day'])) {
                    continue;
                }
                ?> 
                <? /* <span class="bus__anim"></span> */ ?>
                <? if ($count0==3) { echo '<li class="stops-item --pink">
					<button class="stops__show">Все остановки ('.($countst-6).')</button>
				</li>'; }?>
                <li class="stops-item<?=($allst&&($count0>2)&&($count0<($countst-3)))?" --hidden":""?>" stationid="<?= $s['id_station_rout']; ?>" data-modal="route">
                    <span class="stops__text"><?= $s['name'] ?></span>
                    <button class="btn" >Расписание</button>
                </li>
                <?
                /*
                  <li class="stops-item" idi='<?= $s['id_station_rout']; ?>'>
                  <?=
                  $this->render('_timework', [
                  's' => $s,
                  'type_day' => $route->type_day,
                  'day_week' => $day_week,
                  'f' => $f,
                  'flag2' => false,
                  'city' => $city
                  ])
                  ?>
                  <?
                  $f = false;
                  ?>
                  </li>
                  <? */$count0++;
            }
            ?>

        </ul>
    <? } ?>
</main>

<? /* <img src="img/map.jpg" alt="map"> */ ?>


<div class="modal" id="route" stationid="0" boreins="<?= $route->id ?>">
    <div class="modal-header">
        <button class="modal__back"><img src="img/modal-back.svg" alt="back"></button>
        <span class="modal__num"><?= $route_n; ?></span>
        <div class="modal-title">
            <span class="modal__heading"></span>
            <span class="modal__addres">
                <img src="img/modal-arrow.svg" alt="modal">
                <i></i>
            </span>
        </div>
    </div>
    <nav class="nav">
        <h2 class="nav__item is_active modal_close">Расписание</h2>
        <h2 class="nav__item" data-modal="scheme">Онлайн схема</h2>
        <h2 class="nav__item" data-modal="map">Онлайн карта</h2>
    </nav>
    <div class="tab day_week" day="<?=(date("N"));?>">
            <a href="#" class="tab__btn<?=(date("N")==1)?' is_active':'';?>" d="1">ПН</a>
            <a href="#" class="tab__btn<?=(date("N")==2)?' is_active':'';?>" d="2">ВТ</a>
            <a href="#" class="tab__btn<?=(date("N")==3)?' is_active':'';?>" d="3">СР</a>
            <a href="#" class="tab__btn<?=(date("N")==4)?' is_active':'';?>" d="4">ЧТ</a>
            <a href="#" class="tab__btn<?=(date("N")==5)?' is_active':'';?>" d="5">ПТ</a>
            <a href="#" class="tab__btn<?=(date("N")==6)?' is_active':'';?>" d="6">СБ</a>
            <a href="#" class="tab__btn<?=(date("N")==7)?' is_active':'';?>" d="7">ВС</a>
    </div>
    <div id="schedule">
        <span class="loader"></span> 
    </div>
</div>


	<div class="modal" id="scheme">
		<div class="modal-header">
			<button class="modal__back"><img src="img/modal-back.svg" alt="back"></button>
			<span class="modal__num"><?= $route_n; ?></span>
			<div class="modal-title">
				<span class="modal__heading"><?= $route->name ?></span>
			</div>
		</div>
		<div class="loaderinfo modal-online">
			<?=$type_transport_iminit_many?> онлайн <b><span class="loader"></span></b>
		</div>
		<nav class="nav">
			<h2 class="nav__item modal_close">Расписание</h2>
			<h2 class="nav__item is_active" data-modal="scheme">Онлайн схема</h2>
			<h2 class="nav__item" data-modal="map">Онлайн карта</h2>
		</nav>
		<div class="scheme-section">
			
		</div>
	</div>

	<div class="modal" id="map" cawe="">
		<div class="modal-header">
			<button class="modal__back"><img src="img/modal-back.svg" alt="back"></button>
			<span class="modal__num"><?= $route_n; ?></span>
			<div class="modal-title">
				<span class="modal__heading"><?= $route->name ?></span>
			</div>
		</div>
		<div class="loaderinfo modal-online">
			Обновление информации
			<span class="loader"></span>
		</div>
		<nav class="nav">
			<h2 class="nav__item modal_close">Расписание</h2>
			<h2 class="nav__item" data-modal="scheme">Онлайн схема</h2>
			<h2 class="nav__item is_active" data-modal="map">Онлайн карта</h2>
		</nav>
                <div class="maponline"></div>
	</div>