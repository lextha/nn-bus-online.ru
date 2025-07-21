<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RouteMessages */

$this->title = 'Update Route Messages: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Route Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="route-messages-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
