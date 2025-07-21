<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RouteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="route-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'alias') ?>

    <?= $form->field($model, 'city_id') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'type_transport') ?>

    <?php // echo $form->field($model, 'organization') ?>

    <?php // echo $form->field($model, 'info') ?>

    <?php // echo $form->field($model, 'time_work') ?>

    <?php // echo $form->field($model, 'route_text') ?>

    <?php // echo $form->field($model, 'type_day') ?>

    <?php // echo $form->field($model, 'temp_route_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
