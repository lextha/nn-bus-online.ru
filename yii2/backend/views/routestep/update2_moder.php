<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RouteStep */

$this->title = 'Update Route Step: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Route Steps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
if (!isset($create)) { $create=0; }

?>
<div class="route-step-update">
<? if (!isset($route)) { 
    echo $this->render('_form2_moder_newroute', [
        'model' => $model,
        'create'=>$create,
        'route_id' => $route_id,
    ]);
} else {
    echo $this->render('_form2_moder', [
        'model' => $model,
        'create'=>$create,
        'route'=>$route,
        'route_id' => $route_id,
    ]); 
} ?>

</div>
