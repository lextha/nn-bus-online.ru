<?php

use frontend\assets\AppAsset;
use yii\helpers\Html;
use nirvana\jsonld\JsonLDHelper;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->registerCsrfMetaTags() ?>
        <?php JsonLDHelper::registerScripts(); ?>
        <?php $this->head() ?>
        <meta name="theme-color" content="#14ACAF">
        <link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400..900&display=swap" rel="stylesheet">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="app">
            <?= $content ?>
            <footer class="footer">
                <p class="footer-text">© arh-bus.ru</p>
                <nav>
                    <noindex><!--googleoff: all-->
                        <div style='text-align: right;margin: 20px;color:#97989d;'>
                            Сайт предназначен исключительно для ознакомительных целей. <br>
                            Все данные предоставляются без гарантий и могут быть изменены в любое время.</div>
                        <? /* <a href="#">Cоглашение</a>
                          <a href="#">Помощь</a>
                          <a href="mailto:support@site-bus.ru">support@site-bus.ru</a> */ ?>
                        <!--googleon: all-->
                    </noindex>
                </nav>
            </footer>
        </div>
        <?php $this->endBody() ?>
       <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(97004382, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/97004382" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
    </body>

</html>

<?php $this->endPage(); ?>
<?php
/* @var $this \yii\web\View */
/* @var $content string */

/* $O1=['o','о'];
  $B1=['B','В'];
  $z1=['_','-','*','`',' ','—',' ','–'];
  $this->copyright_goonbus='<div style="display:none;"><noindex><!--googleoff: all--><div class="modalwin" id="errorfindmodal"></div> <!--googleon: all--></noindex></div><div style="display:none;"><noindex><!--googleoff: all-->2011 © на базу данных G'.$O1[array_rand($O1)].$z1[array_rand($z1)].$O1[array_rand($O1)].'n'.$z1[array_rand($z1)].$B1[array_rand($B1)].'us .'.$z1[array_rand($z1)].'ru</noindex></div>';
 */

/*
  use yii\helpers\Html;
  //use yii\bootstrap\Nav;
  //use yii\bootstrap\NavBar;

  use frontend\assets\AppAsset;
  use common\widgets\Alert;
  use common\widgets\City;
  use common\widgets\Full;
  use app\components\TranslitWidget;
  use nirvana\jsonld\JsonLDHelper;

  AppAsset::register($this);
  Yii::$app->view->registerJsFile('/js/site.js?v=3',['depends' => [\yii\web\JqueryAsset::className()]]);
  Yii::$app->view->registerJsFile('https://yastatic.net/jquery/cookie/1.0/jquery.cookie.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
  //echo "<pre>"; var_dump($this->context->actionParams['city']->name); echo "</pre>";
  ?>
  <?php $this->beginPage() ?>
  <!DOCTYPE html>
  <html lang="<?= Yii::$app->language ?>">
  <head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $this->registerCsrfMetaTags() ?>
  <title><?= Html::encode($this->title) ?></title>
  <?php JsonLDHelper::registerScripts(); ?>
  <?php $this->head() ?>
  <link rel="apple-touch-icon" sizes="180x180" href="/ico/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/ico/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/ico/favicon-16x16.png">
  <link rel="manifest" href="/ico/site.webmanifest">
  <link rel="mask-icon" href="/ico/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
  <? if ((Yii::$app->controller->id == 'site') and (Yii::$app->controller->action->id == 'index'))  {
  // тут код
  } else { ?>
  <script>window.yaContextCb = window.yaContextCb || []</script>
  <script src='https://yandex.ru/ads/system/context.js' async></script>
  <? if (Yii::$app->controller->action->id == 'routemap')  {
  ?>
  <script>window.yaContextCb.push(()=>{
  Ya.Context.AdvManager.render({
  type: 'topAd',
  blockId: 'R-A-196619-31'
  })
  })</script>
  <?
  } elseif (Yii::$app->controller->action->id == 'citydacha') {
  ?>
  <script>window.yaContextCb.push(()=>{
  Ya.Context.AdvManager.render({
  type: 'topAd',
  blockId: 'R-A-196619-30'
  })
  })</script>
  <?
  } else { ?>
  <script>window.yaContextCb.push(()=>{
  Ya.Context.AdvManager.render({
  type: 'fullscreen',
  blockId: 'R-A-196619-28'
  })
  });</script>

  <script>window.yaContextCb.push(()=>{
  Ya.Context.AdvManager.render({
  type: 'floorAd',
  blockId: 'R-A-196619-25'
  })
  })</script>
  <? } ?>
  <script async src="https://ad.mail.ru/static/ads-async.js"></script>
  <script>(MRGtag = window.MRGtag || []).push({})</script>
  <? } ?>
  </head>
  <body><? //if ($_SERVER['REMOTE_ADDR']=='5.187.71.217') { var_dump(Yii::$app->controller->action->id); }?>
  <?php $this->beginBody() ?>
  <? if ((Yii::$app->controller->id == 'site') and (Yii::$app->controller->action->id == 'index'))  {
  // тут код
  } else {
  } ?>

  <div class="viewport-wrapper">

  <div class="site-header">

  <div class="wrapper">

  <div class="logo">
  <a href="/"><div class="logo2"></div></a>
  </div>
  <? if (isset($this->context->actionParams['city']->name)) { ?>
  <div class="r">
  <div class="city">
  <? echo City::widget(['city'=>$this->context->actionParams['city']]); ?>
  </div>
  <div class="clear_fix"></div>
  </div>
  <? } ?>
  <div class="clear_fix"></div>
  </div>

  </div><!-- .site-header -->



  <?= Alert::widget() ?>
  <?= $content ?>



  <div class="site-footer">

  <div class="wrapper">

  <div class="copy">
  GoOnBus.ru &copy; 2011-<?=date('Y');?>
  </div>

  </div>

  </div><!-- .site-footer -->

  </div><!-- .viewport-wrapper -->

  <?php $this->endBody() ?>
  <? if ((Yii::$app->controller->id == 'site') and (Yii::$app->controller->action->id == 'index'))  {
  // тут код
  } else {
  } ?>

  </body>
  </html>
  <?php $this->endPage() */?>