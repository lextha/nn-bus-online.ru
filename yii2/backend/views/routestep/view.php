<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RouteStep */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Route Steps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="route-step-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'route_id',
            'name',
            'number',
            'alias',
            'city_id',
            'price',
            'type_transport',
            'type_direction',
            'organization',
            'info:ntext',
            'time_work:ntext',
            'route_text:ntext',
            'type_day',
            'temp_route_id:ntext',
            'active',
            'lastmod',
            'version',
            'marshruts_value:ntext',
            'user_id',
            'date',
            'status',
        ],
    ]) ?>

</div>
