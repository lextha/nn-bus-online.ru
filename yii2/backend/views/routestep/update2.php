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

    <?= $this->render('_form2', [
        'model' => $model,
        'create'=>$create,
        'route_id' => ((isset($route_id))?$route_id:0),
    ]) ?>

</div>
