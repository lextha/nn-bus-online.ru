<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\Route */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Маршруты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="route-view">

  <?/*  <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'update'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','/admin/routestep/update?id='.$model->id);
                    },
                  /*  'moder'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-check" aria-hidden="true"></i>','/routestep/update?id='.$model->id);
                    }*/
                ],
                'visibleButtons' => [
                    'moder'=> function ($model,$key,$url) {
                        return $key%2;
                    }
                ]
                        
            ],
            'name',
            'number',            
        ],
    ]); ?>
    
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'number',
            'alias',
            'city_id',
            'price',
            'type_transport',
            'organization',
            'info:ntext',
            'time_work:ntext',
            'route_text:ntext',
            'type_day',
            'temp_route_id',
        ],
    ]) ?>

</div>
