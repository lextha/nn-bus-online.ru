<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RouteMessages */

$this->title = 'Create Route Messages';
$this->params['breadcrumbs'][] = ['label' => 'Route Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-messages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
