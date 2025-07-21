<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */
use yii\helpers\Url;
?>
    Link: <a href='<?=Url::toRoute(['site/route', 'id' => $route_id]);?>'>Ссылка</a>

   Текст: <?=$errortext?>