<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Маршруты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-index">

 <?/*   <h1><?= Html::encode($this->title) ?></h1>*/?>

    <p>
        <?= Html::a('Добавить маршрут', ['/routestep/create?version=3'], ['class' => 'btn btn-success']) ?>
        <?// Html::a('Добавить маршрут с картой', ['/routestep/create?version=1'], ['class' => 'btn btn-success']) ?>
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
                        'class' => (!$model->getEditstatus()) ? 'success' : 'danger'
                    ];
                }],
            [
                'class' => 'yii\grid\ActionColumn',
                
                'template' => '{link} {update} {delete}',
                'buttons' => [
                    'link'=> function ($url,$model,$key) {
                        $urll=Yii::$app->urlManagerFrontend->createUrl(['site/route', 'id' => $model->id]);
                        return Html::a('<i class="fa fa-external-link " aria-hidden="true"></i>',$urll,['target'=>'_blank', 'data-pjax'=>"0"]);
                    },
                    'delete'=> function ($url,$model,$key) {
                        if (Yii::$app->user->can('admin')) {
                            return Html::a('<i class="fa fa-trash" aria-hidden="true"></i>',$url,['data' => ['confirm' => 'Вы уверены что хотите удалить маршрут?']]);
                        }
                    },
                    'update'=> function ($url,$model,$key) {
                        $editstatus=$model->getEditstatus();
                        if (!$editstatus) { $urll='/admin/routestep/update?id='.$model->id; } else { $urll='/admin/routestep/update?edit=1&id='.$editstatus['id']; }
                        return Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>',$urll);
                    },
                  /*  'moder'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-check" aria-hidden="true"></i>','/routestep/update?id='.$model->id);
                    }*/
                ],
                
                        
            ],
            'id',
            'name',
            'number',
             ['attribute'=>'active','filter'=> \common\models\Route::getActiveList(),'value'=>function($model) {if($model->active==1){return 'Действует';}else{return 'Не действует';}}],
          //  'alias',
           // ['attribute'=>'alias','format'=>'url'],
            ['attribute'=>'city_id','filter'=> \common\models\City::getCities($editor_citis),'value'=>'cityname'],
            //'price',
            ['attribute'=>'type_transport','filter'=> \common\models\Route::getTypetransportList(),'value'=>'typetransport'],
            ['attribute'=>'type_direction','filter'=> \common\models\Route::getTypedirectionList(),'value'=>'typedirection'],
            ['attribute'=>'version','filter'=> \common\models\Route::getVersionList(),'value'=>'versionname'],
            
            //'organization',
            //'info:ntext',
            //'time_work:ntext',
            //'route_text:ntext',
            //'type_day',
            //'temp_route_id',
            ['attribute'=>'has_time','filter'=> \common\models\Route::getYesList(),'value'=>function($model) {if($model->has_time==1){return 'Да';}else{return 'Нет';}}],
            'lastmod',
            
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
