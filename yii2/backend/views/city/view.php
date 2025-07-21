<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="city-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    <form method="get" action="hastime">
        <input type="text" name="offset" value="0" id="offset">
        <input type="text" name="limit" value="100">
        <input name="id" type="hidden" value="<?=$model->id?>">
        <button type="submit">Заполнить без расписания</button>
    </form>
        
        <?= Html::a('Заполнить без расписания', ['hastime', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Очистить кеш', ['delcache', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <h2>Дубли маршрутов</h2>
    <table class="table">
        <tr><td>id</td><td>номер</td><td>название</td><td>действует</td><td>тип транспортв</td><td>тип направления</td><td>просмотры</td><td>версия 1-новые с картой, 2 - старые без карты, 3 - новые без карты, 4 - старые с картой</td><td><a href="?id=<?=$model->id?>&gd=all" onclick="return confirm('Уверены ВСЕ?') ? true : false;">Одобрить ВСЕ</a></td></tr>
    <?foreach ($routes as $r) {
        $i=0;
        foreach ($r as $rr) {
        ?>
        <tr <? if ($i==0) { ?>style="background: #feabff;" <? }?>><td><?=$rr->id;?></td><td><?=$rr->number;?></td><td><?=$rr->name;?></td><td><?=$rr->active;?></td><td><?=$rr->type_transport;?></td><td><?=$rr->type_direction;?></td><td><?=$rr->views;?></td><td><?=$rr->version;?></td><td><? if ($i==0) { ?><a href="?id=<?=$model->id?>&gd=<?=$rr->id?>"  onclick="return confirm('Уверены ВСЕ?') ? true : false;">Одобрить</a><? } ?></td></tr>
        <?
        $i++;
          /*  ?>
            <tr style="background: #feabff;"><td><?=$r[1]->number;?></td><td><?=$r[1]->name;?></td><td><?=$r[1]->active;?></td><td><?=$r[1]->type_transport;?></td><td><?=$r[1]->type_direction;?></td><td><?=$r[1]->views;?></td><td><?=$r[1]->version;?></td></tr>
            <? */
        }
    }
    ?>
    </table>
    <? var_dump($many_dubl); ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'alias',
            'description:ntext',
            'sklon',
            'count_rout',
        ],
    ]) ?>

</div>
