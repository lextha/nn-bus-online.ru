<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2
/* @var $this yii\web\View */
/* @var $model common\models\Route */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="route-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city_id')->widget(Select2::classname(), [
    'data' => \yii\helpers\ArrayHelper::map(common\models\City::find()->all(),'id','name'),
        'theme' => Select2::THEME_MATERIAL,
    'options' => ['placeholder' => 'Город'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);//listBox(\yii\helpers\ArrayHelper::map(common\models\City::find()->all(),'id','name')) ?>
    <?= $form->field($model, 'active')->radioList([1=>'Да',0=>'Нет']) ?>
 <?= $form->field($model, 'type_transport')->radioList(\common\models\Route::getTypetransportList()) ?>
     <?= $form->field($model, 'type_direction')->radioList(\common\models\Route::getTypedirectionList()) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

   

    <?= $form->field($model, 'organization')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'time_work')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'route_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'type_day')->textInput() ?>
    
    <?= $form->field($model, 'version')->radioList(\common\models\Route::getVersionList()); //, ['itemOptions' => ['disabled' => true]] ?> 

    
    <?
    if ($type_form=='update' AND $model->version!=1) {
    echo $this->render('_marshrut', [
        'model' => $model,
    ]); }?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
