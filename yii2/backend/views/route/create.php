<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Route */

$this->title = 'Добавить маршрут';
$this->params['breadcrumbs'][] = ['label' => 'Маршруты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-create">

 <?/*   <h1><?= Html::encode($this->title) ?></h1>*/?>

    <?= $this->render('_form', [
        'model' => $model,
        'type_form' =>'create'
    ]) ?>
</div>
