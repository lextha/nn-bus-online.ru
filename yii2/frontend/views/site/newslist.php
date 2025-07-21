<?

use yii\helpers\Html;
use yii\helpers\Url;
use nirvana\jsonld\JsonLDHelper;
Yii::$app->view->registerJsFile('/js/script.js?v=3', ['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerCssFile('/css/sty.css?v=2');
if ($city->sklon == '') {
    $city->sklon = $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name;
}
$names = explode('/', $city->sklon);
if (!(is_array($names) AND count($names) > 4)) {
    for ($tq = 0; $tq < 7; $tq++) {
        $names[$tq] = $city->name;
    }
}


$this->title = "Новости транспорта ". $names[0];
$descr = "Все новости про общественный транспорт в ".$names[5].". Информация об изменениях в расписании или маршрутах следования.";
$this->registerMetaTag(
        ['name' => 'description', 'content' => $descr]
);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

//$this->title = $route->name;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => "/news"];

JsonLDHelper::addBreadcrumbList();

?><header class="header news">
    <a href="/" class="logo">
        <img src="img/logo.svg" alt="logo">
        <div class="logo-title">
            <span class="logo__heading">Общественный транспорт</span>
            <span class="logo__text"><?= $names[0] ?></span>
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
        <span class="title__heading">Новости <?= $names[0] ?></span>
    </h1>

</header>

<main class="stops-section newsall">
    <? foreach ($news as $n) { ?>

        <a href="<?= Url::toRoute(['site/news', 'id' => $n->id]); ?>" class="listing-item is_active">
            <span class="listing__time"><?= date("d.m.Y", strtotime($n->time)); ?></span>
            <span class="listing__text"><?= $n->title2 ?></span>
        </a>
    <? } ?>
</main>
