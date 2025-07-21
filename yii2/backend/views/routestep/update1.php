<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RouteStep */

$this->title = 'Update Route Step: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Route Steps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="route-step-update">

 <? /*    <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <?=$this->render('_form1', [
        'type_day'=>$type_day,
        'model' => $model,
        'edit'=>$edit,
        'route_id'=>$route_id,
        'stations'=>$stations,'stations0'=>$stations0,'stations1'=>$stations1,'stations_all'=>$stations_all,
    ]) ?>

</div>
