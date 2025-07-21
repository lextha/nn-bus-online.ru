<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Route */
/* @var $form yii\widgets\ActiveForm */
/*if ((Yii::$app->user->can('admin'))) {
var_dump($route);
 echo $this->render('_marshrutadmin', [
        'model' => $route,
        'create'=>$create
    ]);
 }*/

?>

<div class="route-form">

    <?php $form = ActiveForm::begin(); ?>
    <table class='moder_table'>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    <?= Html::a( 'Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']); ?>
                    <? if (Yii::$app->user->can('updateStep',['author_id'=>$model->user_id])) {
                            echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']); 
                            // echo Html::input('hidden', 'savegood','1'); 
                    } ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?=$form->field($model, 'otklon')->textarea(['rows' => 6]) ?></td>
            <td><? echo Html::submitButton('Отклонить', ['class' => 'btn btn-warning','name'=>'savebad','value'=>'1']); ?></td>
        </tr>
        <tr>
            <td><?= $form->field($model, 'version')->radioList(\common\models\Route::getVersionList()); //, ['itemOptions' => ['disabled' => true]] ?> </td><td></td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
              
            </td>
        </tr>
        <tr>
            <td>
                <?=$form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                
            </td>
        </tr>
        <tr>
            <td>
                <?=$form->field($model, 'city_id')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(common\models\City::find()->all(),'id','name'),
                    'theme' => Select2::THEME_MATERIAL,
                'options' => ['placeholder' => 'Город'],
                'pluginOptions' => [
                    'allowClear' => true
                ],]); ?>
            </td>
            <td>
              
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'active')->radioList([1=>'Да',0=>'Нет']) ?>
            </td>
            <td>
              
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'redirect')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
               
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'type_transport')->radioList(\common\models\Route::getTypetransportList()) ?>
            </td>
            <td>
               
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'type_direction')->radioList(\common\models\Route::getTypedirectionList()) ?>
            </td>
            <td>
               
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'organization')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>
            </td>
            <td>
             
            </td>
        </tr>
        <tr>
            <td>
               <?= $form->field($model, 'season')->radioList([1=>'Да',0=>'Нет',2=>'Дачный']) ?>
            </td>
            <td>
             
            </td>
        </tr>       
        <tr>
            <td>
                <?= $form->field($model, 'time_work')->textarea(['rows' => 6]) ?>
            </td>
            <td>
               
            </td>
        </tr>  
        <tr>
            <td>
                <?= $form->field($model, 'route_text')->textarea(['rows' => 6]) ?>
            </td>
            <td>
               
            </td>
        </tr>  
        <tr>
            <td>
                <? if (isset($edit)) { echo Html::hiddenInput('edit', '1'); } ?>
                <? 
                 echo $form->field($model, 'user_id')->hiddenInput(['value'=> (($model->user_id)?$model->user_id:Yii::$app->user->id)])->label(false);
                if ($model->version==2 OR $model->version==3) {
                    echo $this->render('_marshrut', [
                        'model' => $model,
                        'create'=>$create
                    ]);  
                } ?>
            </td>
            <td style='padding: 50px 0 0 50px;'>
             
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'source')->textarea(['rows' => 6]) ?>
            </td>
            <td>
               
            </td>
        </tr>  
         <tr>
            <td colspan="2"> <?= $form->field($model, 'price_edit')->textInput(['maxlength' => true]) ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    <? if (Yii::$app->user->can('updateStep',['author_id'=>$model->user_id])) {
                            echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']); 
                    } ?>
                </div>
            </td>
        </tr>
    </table>
    <?php ActiveForm::end(); ?>

</div>
