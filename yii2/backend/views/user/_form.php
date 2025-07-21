<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
<? //var_dump($model->citis); ?>
    
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->textInput() ?>
     <?= $form->field($model, 'username')->textInput() ?>
     <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'email')->textInput() ?>
     <?= $form->field($model, 'password2')->passwordInput() ?>
    
    <?=$form->field($model, 'citis')->widget(Select2::classname(), [
    'data' => \yii\helpers\ArrayHelper::map(common\models\City::find()->all(),'id','name'),
         'theme' => Select2::THEME_MATERIAL,
    'options' => ['placeholder' => 'Выбор городов ...', 'multiple' => true],
    'pluginOptions' => [
        'tags' => true,
        'maximumInputLength' => 10
    ],
])->label('Города');
             
            
?>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
