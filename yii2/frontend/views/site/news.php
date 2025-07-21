<?

use yii\helpers\Html;
use yii\helpers\Url;
use nirvana\jsonld\JsonLDHelper;

Yii::$app->view->registerJsFile('/js/script.js?v=3', ['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->view->registerCssFile('/css/sty.css');
if ($city->sklon == '') {
    $city->sklon = $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name . "/" . $city->name;
}
$names = explode('/', $city->sklon);
if (!(is_array($names) AND count($names) > 4)) {
    for ($tq = 0; $tq < 7; $tq++) {
        $names[$tq] = $city->name;
    }
}


$this->title = $news->title;
$descr = $news->descr;
$this->registerMetaTag(
        ['name' => 'description', 'content' => $descr]
);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

//$this->title = $route->name;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => "/news"];
$this->params['breadcrumbs'][] = ['label' => $news->title2, 'url' => [Url::toRoute(['site/news', 'id' => $news->id])]]; // $route->name;

JsonLDHelper::addBreadcrumbList();
?><header class="header news">
    <a href="/" class="logo">
        <img src="/img/logo.svg" alt="logo">
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
        <span class="title__heading"><?= $news->title2; ?></span>
    </h1>
    <div class="time__heading">
        <span><?= date("d.m.Y", strtotime($news->time)); ?></span>
        <span class="all__heading"><a href="/news">Все новости</a></span>
    </div>
</header>

<main class="stops-section news">
<?= $news->text2; ?>

    <i> Источник: <?= $news->source; ?></i>
</main>
