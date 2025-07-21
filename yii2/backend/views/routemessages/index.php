<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RouteMessagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Route Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-messages-index">

    <h1><?= Html::encode($this->title) ?>1111111111111</h1>

    <p>
        <?= Html::a('Create Route Messages', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
            'contentOptions' => function($model) {
                return [
                        'class' => ($model->status) ? 'success' : 'danger'
                    ];
                }],
            'id',
            [
                'class' => 'yii\grid\ActionColumn',
                
                'template' => '{moder} {link} {edit}',
                'buttons' => [
                    'moder'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-check-circle" aria-hidden="true"></i>','/admin/routemessages/update?moder=1&id='.$model->id);
                    },
                    'link'=> function ($url,$model,$key) {
                        $urll=Yii::$app->urlManagerFrontend->createUrl(['site/route', 'id' => $model->route_id]);
                        return Html::a('<i class="fa fa-external-link " aria-hidden="true"></i>',$urll,['target'=>'_blank', 'data-pjax'=>"0"]);
                    },
                    'edit'=> function ($url,$model,$key) {
                        $route=\common\models\Route::findOne($model->route_id);
                        if ($route->version!=1) {
                            return Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','/admin/routestep/update?id='.$model->route_id,['target'=>'_blank', 'data-pjax'=>"0"]);
                        } else {
                            return false;
                        }
                    },
                ],
                        
            ],
            'route_id',
            'text:ntext',
            'photo:ntext',
            'date',

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
