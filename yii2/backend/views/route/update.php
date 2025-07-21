<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Route */

$this->title = 'Update Route: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Маршруты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="route-update">

   <?/* <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <?= $this->render('_form', [
        'model' => $model,
        'type_form' =>'update'
    ]) ?>

</div>
