<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RouteStep */

$this->title = 'Create Route Step';
$this->params['breadcrumbs'][] = ['label' => 'Route Steps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-step-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
