<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RouteStepSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Редактирование правок';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-step-index">

    <? /*  <h1><?= Html::encode($this->title) ?></h1>

  <p>
        <?= Html::a('Create Route Step', ['create'], ['class' => 'btn btn-success']) ?>
    </p> */ 
     if (Yii::$app->user->can('admin'))  {
            ?>
          <table style="width: 100%">
              <tr>
                  <td>
                      <?=Html::beginForm(['routestep/pay'],'post');?>
                      <?=Html::dropDownList('action','',[''=>'Выбор действия: ','nc'=>'Оплатить','delete'=>'Удалить'],['class'=>'dropdown form-control',])?>
                  </td>
                  <td>
                      <? echo Html::submitButton('Обновить', ['class' => 'btn btn-info', "onclick" => 'if(confirm("Are you sure you want to delete this item?")){
                           return true;
                          }else{
                           return false;
                          }']);
                      ?>
                  </td>
                  <td style="width:450px; text-align: right;">
                      <? echo Html::Button('Посчитать', ['class' => 'btn btn-info raschet']);?>
                     
                  </td>
                  <td style="width:150px; text-align: right;"><div id="itog_price"></div></td>
              </tr>
          </table>
          <?php 
     }
    // echo $this->render('_search', ['model' => $searchModel]); ?>
<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn','visible' => Yii::$app->user->can('admin')],
            ['class' => 'yii\grid\SerialColumn'],
[
                'class' => 'yii\grid\ActionColumn',
                'template' => '{link} {update} {delete} {moder}',
                'buttons' => [
                    'link'=> function ($url,$model,$key) {
                        $urll=Yii::$app->urlManagerFrontend->createUrl(['site/route', 'id' => $model->route_id]);
                        return Html::a('<i class="fa fa-external-link " aria-hidden="true"></i>',$urll,['target'=>'_blank', 'data-pjax'=>"0"]);
                    },
                    'update'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','/admin/routestep/update?edit=1&id='.$model->id);
                    },
                    'moder'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-check" aria-hidden="true"></i>','/admin/routestep/update?moder=1&edit=1&id='.$model->id);
                    },
                    'approve'=> function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-paperclip" aria-hidden="true"></i>','/admin/routestep/update?moder=1&edit=1&id='.$model->id);
                    }
                ],
                'visibleButtons' => [
                    'update'=> function ($model,$key,$url) {
                        return ($model->status!=1)?true:false;
                    },
                    'delete'=> function ($model,$key,$url) {
                        return ($model->status!=1)?true:false;
                    },
                    'moder'=> function ($model,$key,$url) {
                        if ((Yii::$app->user->can('admin')) AND $model->status!=1) { return true; } else { return false; }
                    },
                    'approve'=> function ($model,$key,$url) {
                        if ((Yii::$app->user->can('admin')) AND $model->status!=1) { return true; } else { return false; }
                    },
                ]
                        
            ],
         //   'id',
            'route_id',
            ['attribute' => 'date', 'format' => ['date', 'php:d-m-Y H:i:s']],
            ['attribute' => 'lastmod', 'format' => ['date', 'php:d-m-Y H:i:s'],'visible' => Yii::$app->user->can('admin'),],
            ['attribute'=>'user_id','filter'=> app\models\RouteStep::getUserall(),'value'=>'username','visible' => Yii::$app->user->can('admin')],
            ['attribute'=>'status','filter'=> app\models\RouteStep::getStatustype(),'value'=>'statusname'],
            'name',
            'number',
         //   'alias',
            //'city_id',
            ['attribute'=>'city_id','value'=>'cityname', 'visible'=>(!Yii::$app->user->can('admin'))],
            ['attribute'=>'city_id','filter'=> \common\models\City::getCities(),'value'=>'cityname', 'visible'=>Yii::$app->user->can('admin')],
            ['attribute'=>'pay','filter'=> [0=>'Не оплачен',1=>'Оплачен'],'value'=>'pay','visible' => Yii::$app->user->can('admin')],
            ['attribute'=>'price_edit'],
          //  ['attribute'=>'type_transport','filter'=> \common\models\Route::getTypetransportList(),'value'=>'typetransport'],
            //'price',
            //'type_transport',
            //'type_direction',
            //'organization',
            //'info:ntext',
            //'time_work:ntext',
            //'route_text:ntext',
            //'type_day',
            //'temp_route_id:ntext',
            //'active',
            //'lastmod',
            //'version',
            //'marshruts_value:ntext',
            //'user_id',
            //'date',
            

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
 <?php Pjax::end(); ?>
<? if (Yii::$app->user->can('admin'))  {
    echo Html::endForm();
}
?> 
</div>

