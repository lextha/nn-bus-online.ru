<?php

/* @var $this yii\web\View */

$this->title = 'Панель менеджера';
?>
<div class="site-index">

    <div class="jumbotron">
        <? /*<h1>Панель менеджера GoOnBus.ru</h1> */ ?>

        <p><a class="btn btn-lg btn-success" href="route/index">Начать работать</a></p>
    </div>

    <div class="body-content">
        <?  if (Yii::$app->user->can('admin')) { ?>
        <form action="site/refreshcitystation" method="post">
            Город <input type="text" name="city_id">
            <input type="submit" name="sub">
             <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        </form>
        <p>Для группировки остановок в одну вводим id города:</p>
        
        <p>Удалить в конце названия остановки: UPDATE `station` SET `name`=REPLACE(`name`, ' A', '') WHERE `city_id`=59 AND `name` LIKE '% A';</p>
        <? } ?>

    </div>
</div>
