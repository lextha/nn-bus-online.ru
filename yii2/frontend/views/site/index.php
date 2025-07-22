<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\Breadcrumbs\BreadcrumbsUtility;
use nirvana\jsonld\JsonLDHelper;
Yii::$app->view->registerCssFile('/css/sty.css');
Yii::$app->view->registerJsFile('/js/script.js?v=3', ['depends' => [\yii\web\JqueryAsset::className()]]);

if ($city->sklon == '') {
    $city->sklon = $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name;
}
$names = explode('/', $city->sklon);

$descr = "Расписание, маршруты, местоположение автобусов, троллейбусов, трамваев и маршрутных такси в режиме онлайн на схеме и карте " . $names[1];
$this->registerMetaTag(
        ['name' => 'description', 'content' => $descr]
);
$this->title = "Расписание общественного транспорта в " . $names[5] . "";
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

//echo "<pre>"; var_dump($routes);
?>

<header class="header">
    <a href="#" class="logo">
        <img src="img/logo.svg" alt="logo">
        <h1 class="logo-title">
            <span class="logo__heading">Общественный транспорт</span>
            <span class="logo__text"><?= $city->name ?></span>
        </h1>
    </a>
    <div class="header-info">
        <span class="header-info__time">
            <img src="img/time.svg" alt="time"> 
        </span>
        <span class="header-info__weather">

        </span>
    </div>
    <div class="header-news">
        <? foreach ($news as $n) { ?>
            <a href="<?= Url::toRoute(['site/news', 'id' => $n->id]); ?>" class="listing-item is_active">
                <span class="listing__time"><?= date("d.m.Y", strtotime($n->time)); ?></span>
                <span class="listing__text"><?= $n->title2 ?></span>
            </a>
        <? } ?>
        <a href="/news" class="listing-item">
                <span class="listing__text">Все новости</span>
            </a>
        <? //var_dump($news);?>
    </div>
    <nav class="navbar">
        <? if (count($routes[1]) > 0) { ?>
            <button class="navbar-item is_active" id="route1">
                <img src="img/bus-1.svg" alt="img">
                <span class="navbar__text">Автобусы Маршрутки</span>
                <span class="navbar__online"><?= rand(0, count($routes[1])); ?> онлайн</span>
            </button>
        <? } ?>
        <? if (count($routes[2]) > 0) { ?>
            <button class="navbar-item" id="route2">
                <img src="img/bus-2.svg" alt="img">
                <span class="navbar__text">Троллейбусы</span>
                <span class="navbar__online"><?= rand(0, count($routes[2])); ?> онлайн</span>
            </button>
        <? } ?>
        <? if (count($routes[3]) > 0) { ?>
            <button class="navbar-item" id="route3">
                <img src="img/bus-3.svg" alt="img">
                <span class="navbar__text">Трамваи</span>
                <span class="navbar__online"><?= rand(0, count($routes[3])); ?> онлайн</span>
            </button>
        <? } ?>
        <? if (count($routes[5]) > 0) { ?>
            <button class="navbar-item" id="route5">
                <img src="img/bus-3.svg" alt="img">
                <span class="navbar__text">Электрички</span>
                <span class="navbar__online"><?= rand(0, count($routes[5])); ?> онлайн</span>
            </button>
        <? } ?>
    </nav>
</header>
<div class="header2">
<script async src="https://ad.mail.ru/static/ads-async.js"></script>
<ins 
    class="mrg-tag"
    style="display:inline-block;width:auto;height:300px"
    data-ad-client="ad-1817673"
    data-ad-slot="1817673">
</ins>
<script>
    (MRGtag = window.MRGtag || []).push({});
</script>
</div>
<? if (count($routes[1]) > 0) { ?>
    <main class="listing route1">
        <? foreach ($routes[1] as $route) { ?>
            <a href="<?= Url::toRoute(['site/route', 'id' => $route->id, 'route' => $route, 'city' => $city]); ?>" class="listing-item is_active">
                <span class="listing__num"><?= ($route->number == '') ? "-" : $route->number; ?></span>
                <span class="listing__text"><?= $route->name ?></span>
                <? /* <span class="listing__online">5</span> */ ?>
            </a>
        <? } ?>
    </main>
<? } ?>
<? if (count($routes[2]) > 0) { ?>
    <main class="listing route2" style="display: none;">
        <? foreach ($routes[2] as $route) { ?>
            <a href="<?= Url::toRoute(['site/route', 'id' => $route->id, 'route' => $route, 'city' => $city]); ?>" class="listing-item is_active">
                <span class="listing__num"><?= ($route->number == '') ? "-" : $route->number; ?></span>
                <span class="listing__text"><?= $route->name ?></span>
                <? /* <span class="listing__online">5</span> */ ?>
            </a>
        <? } ?>
    </main>
<? } ?>
<? if (count($routes[3]) > 0) { ?>
    <main class="listing route3" style="display: none;">
        <? foreach ($routes[3] as $route) { ?>
            <a href="<?= Url::toRoute(['site/route', 'id' => $route->id, 'route' => $route, 'city' => $city]); ?>" class="listing-item is_active">
                <span class="listing__num"><?= ($route->number == '') ? "-" : $route->number; ?></span>
                <span class="listing__text"><?= $route->name ?></span>
                <? /* <span class="listing__online">5</span> */ ?>
            </a>
        <? } ?>
    </main>
<? } ?>
<? if (count($routes[5]) > 0) { ?>
    <main class="listing route5" style="display: none;">
        <? foreach ($routes[5] as $route) { ?>
            <a href="<?= Url::toRoute(['site/route', 'id' => $route->id, 'route' => $route, 'city' => $city]); ?>" class="listing-item is_active">
                <span class="listing__num"><?= ($route->number == '') ? "-" : $route->number; ?></span>
                <span class="listing__text"><?= $route->name ?></span>
                <? /* <span class="listing__online">5</span> */ ?>
            </a>
        <? } ?>
    </main>
<? } ?>