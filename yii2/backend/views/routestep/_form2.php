<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Route */
/* @var $form yii\widgets\ActiveForm */

?>

<h3>Без карты (form2)</h3>

<div class="route-form">
    <? if (isset($model->otklon) AND ($model->otklon!='')) { ?>
    <div class="mess_error"><h5>Причина отклонения правки:</h5>
            <? echo $model->otklon; ?>
    </div>
    <? }  ?>  
   <?
    $mess=\common\models\RouteMessages::find()->where('route_id='.$route_id)->all();
    if (count($mess)>0) {
        echo "<div class='mess_error'><h5>Сообщения пользователей об ошибках</h5>";
        foreach ($mess as $m) {
            echo "<div>".$m->date." - ".$m->text."</div>";
        }
        echo "</div>";
    }
    //var_dump($mess);
    ?>
    <?php $form = ActiveForm::begin(); ?>
        <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a( 'Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']); ?>
        <? if (Yii::$app->user->can('admin')) {
                echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']); 
                // echo Html::input('hidden', 'savegood','1'); 
       ?>  <? } ?>
        <?  if (isset($model->id) && $model->id!=0) { echo Html::a( 'Импорт', "#", ['class' => 'btn btn-warning','id'=>'importdiv']); } ?>
        </div>
        <div class="importdiv" style="display: none;">
            <textarea name="expyand_json"></textarea>
            <? echo Html::submitButton('Импорт', ['class' => 'btn btn-success','name'=>'expyand','value'=>'1']) ?>
        </div>
  
   <?  echo $form->field($model, 'version')->hiddenInput(['value'=> $model->version])->label(false); // $form->field($model, 'version')->radioList(\common\models\Route::getVersionList()); //, ['itemOptions' => ['disabled' => true]] ?> 
 <?= $form->field($model, 'price_edit')->hiddenInput(['value'=> ((isset($model->price_edit)&&$model->price_edit>0)?$model->price_edit:5)])->label(false);  ?> 
   <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= Html::a('Найти в Яндексе', 
            'https://yandex.ru/search/?text='.urlencode($model->getTypetransport()." ".$model->number." ".$model->name." ".$model->getCityname()), 
            ['class'=>'btn btn-primary','target'=>'_blank']) ?>
    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?   if (Yii::$app->user->can('admin')) { 
        echo $form->field($model, 'alias')->textInput(['maxlength' => true]);
    } else { 
    echo $form->field($model, 'alias')->hiddenInput(['value'=> $model->alias])->label(false);} ?>

    <?= $form->field($model, 'city_id')->widget(Select2::classname(), [
    'data' => \yii\helpers\ArrayHelper::map(common\models\City::find()->all(),'id','name'),
        'theme' => Select2::THEME_MATERIAL,
    'options' => ['placeholder' => 'Город'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);//listBox(\yii\helpers\ArrayHelper::map(common\models\City::find()->all(),'id','name')) ?>
    <?= $form->field($model, 'active')->radioList([1=>'Да',0=>'Нет']) ?> 
    <div class="redirect-form" style="<?=($model->active)?"display:none":"";?>">
        <?= $form->field($model, 'redirect')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
        <?
        $all_route=common\models\Route::find()->where(['city_id'=>$model->city_id,'active'=>'1'])->all();
        $new_routes=[];
        $old_routes=[];
        $clon_routes=[];
        foreach ($all_route as $a) {
            if ($a['id']!=$route_id) {
                if ($a['version']==1 OR $a['version']==3) {
                    $a['number']="*".$a['number'];
                    $new_routes[]=$a;
                } else {
                    $old_routes[]=$a;
                }
            }
            $anumber=$a->number;
            $modelnumber=$model->number;
            $anumber = preg_replace('/[^0-9]/', '', $anumber);
            $modelnumber = preg_replace('/[^0-9]/', '', $modelnumber);
            if ($modelnumber==$anumber AND $a->id!=$route_id) {
               $clon_routes[]=$a;
            }
        }
        $all_route= array_merge($new_routes,$old_routes);
        
        echo Select2::widget([
            'name'=>'asada',
        'data' =>common\helpers\ArrayHelper::map2($all_route,'alias','name','number'),
        'theme' => Select2::THEME_MATERIAL,
        'options' => ['placeholder' => 'Адрес'],
        'pluginOptions' => [
           'allowClear' => true
        ],
            'pluginEvents' => [
                            "select2:select" => 'function() { 
                                     $("input[id*=\'-redirect\'").val($(this).val());
                                }'],
            ]); ?>
        </div>
    </div>
    <?
       
    ?>
    <div class="form-group clon-form" style="<?=(count($clon_routes)<1)?"display:none":"";?>">
        <label class="control-label">Возможные дубли маршрута</label><br>
        <table>
        <? foreach ($clon_routes as $cr) { 
            
     //var_dump($cr);
            if ($cr->id!=$route_id) {
                $url=Yii::$app->urlManagerFrontend->createUrl(['site/route', 'id' => $cr->id]);
                echo "<tr><td>".Html::a($cr->number,$url,['target'=>'_blank', 'data-pjax'=>"0"])."</td><td>"
                        .$cr->name."</td><td>"
                        .\common\models\Route::getTypetransportList()[$cr->type_transport]."</td><td>"
                        .\common\models\Route::getTypedirectionList()[$cr->type_direction]."</td><td>"
                        ."<a href='#' class='redirectto' ids='".$cr->alias."'><i class='fa fa-sign-out' aria-hidden='true'></i></a></td><td>"
                        ."<a href='#' class='redirectfrom' idfrom='".$cr->id."' idto='".$route_id."'><i class='fa fa-sign-in' aria-hidden='true'></i></a></td></tr>";
            }
        } ?>
        </table>
    </div>
    
<?= $form->field($model, 'type_transport')->radioList(\common\models\Route::getTypetransportList()) ?>
     <?= $form->field($model, 'type_direction')->radioList(\common\models\Route::getTypedirectionList()) ?>
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

   

    <?= $form->field($model, 'organization')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>
<? if ($model->version==2)  { 
   /* if ($_SERVER['REMOTE_ADDR']=='5.187.71.113') {
        var_dump($model); die();
    }*/
    $extra_fields=$model->getExtraFields(); //   var_dump($extra_fields); ?>
     <? if (count($extra_fields)>1) { 
        echo $form->field($model, 'time_work')->textarea(['rows' => 6,'value'=>$extra_fields[4]->value."\n\n".str_replace("<br>", "\n\n", $extra_fields[5]->value)]); 
     } else {
        echo $form->field($model, 'time_work')->textarea(['rows' => 6,'value'=>'']); 
     }
?>
    <? // $form->field($model, 'route_text')->textarea(['rows' => 6,'value'=>$extra_fields[6]->value]); ?>
 <? } else { ?>
    <?= $form->field($model, 'time_work')->textarea(['rows' => 6]) ?>
    <? // $form->field($model, 'route_text')->textarea(['rows' => 6]) ?>
<? } ?>
    
<?= $form->field($model, 'season')->radioList([1=>'Да',0=>'Нет',2=>'Дачный']) ?>

    <? if (isset($edit)) { echo Html::hiddenInput('edit', '1'); } ?>
    <? 
     echo $form->field($model, 'user_id')->hiddenInput(['value'=> (($model->user_id)?$model->user_id:Yii::$app->user->id)])->label(false);
    if ($model->version==2 OR $model->version==3) {
        
    echo $this->render('_marshrut', [
        'model' => $model,
        'create'=>$create
    ]); } ?>
<?= $form->field($model, 'source')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <? if (Yii::$app->user->can('admin')) {
                echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']); 
        } ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
