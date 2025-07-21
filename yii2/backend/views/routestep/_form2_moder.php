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
 <?
    $mess=\common\models\RouteMessages::find()->where('route_id='.$route_id)->all();
    if (count($mess)>0) {
        echo "<div class='mess_error'><h4>Сообщения об ошибках</h4>";
        foreach ($mess as $m) {
            echo "<div>".$m->date." - ".$m->text."</div>";
        }
        echo "</div>";
    }
    //var_dump($mess);
    ?>
    <?php $form = ActiveForm::begin(); ?>
    <table class='moder_table'>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    <?= Html::a( 'Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']); ?>
                    <? if (Yii::$app->user->can('updateStep',['author_id'=>$model->user_id])) {
                            echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']); 
                         //   echo Html::input('hidden', 'savegood','1'); 
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
        <tr class="<?=($model->name!=$route->name)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?=$route->name;?>
                <?= Html::a('Найти в Яндексе', 
            'https://yandex.ru/search/?text='.urlencode($model->getTypetransport()." ".$model->number." ".$model->name." ".$model->getCityname()), 
            ['class'=>'btn btn-primary','target'=>'_blank']) ?>
            </td>
        </tr>
        <tr class="<?=($model->number!=$route->number)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?=$route->number;?>
            </td>
        </tr>
        <tr>
            <td>
                <?=$form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?=$route->alias;?>
            </td>
        </tr>
        <tr class="<?=($model->city_id!=$route->city_id)?"diff":"";?>">
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
                <?=(common\models\City::findOne(["id"=>$route->city_id]))->name;?>
            </td>
        </tr>
        <tr class="<?=($model->active!=$route->active)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'active')->radioList([1=>'Да',0=>'Нет']) ?>
            </td>
            <td>
                <?=$route->active?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
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
                $modelnumber=$route->number;
                $anumber = preg_replace('/[^0-9]/', '', $anumber);
                $modelnumber = preg_replace('/[^0-9]/', '', $modelnumber);
                if ($modelnumber==$anumber AND $route->id!=$a->id) {
                   $clon_routes[]=$a;
                  // var_dump($route->id,$a->id);
                }
            }
            $all_route= array_merge($new_routes,$old_routes);
?>
            <div class="form-group clon-form" style="<?=(count($clon_routes)<1)?"display:none":"";?>">
                <label class="control-label">Возможные дубли маршрута</label><br>
                <table>
                <? foreach ($clon_routes as $cr) { 

             //var_dump($cr);
                    if ($cr->id!=$model->id) {
                        $url=Yii::$app->urlManagerFrontend->createUrl(['site/route', 'id' => $cr->id]);
                        echo "<tr><td>".Html::a($cr->number,$url,['target'=>'_blank', 'data-pjax'=>"0"])."</td><td>"
                                .$cr->name."</td><td>"
                                .\common\models\Route::getTypetransportList()[$cr->type_transport]."</td><td>"
                                .\common\models\Route::getTypedirectionList()[$cr->type_direction]."</td><td>"
                                ."<a href='#' class='redirectto' ids='".$cr->alias."'><i class='fa fa-sign-out' aria-hidden='true'></i></a></td><td>"
                                ."<a href='#' class='redirectfrom' idfrom='".$cr->id."' idto='".$route->id."'><i class='fa fa-sign-in' aria-hidden='true'></i></a></td></tr>";
                    }
                } ?>
                </table>
            </div>

            </td>
        </tr>
        <tr class="<?=($model->redirect!=$route->redirect)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'redirect')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?=$route->redirect?>
            </td>
        </tr>
        <tr class="<?=($model->type_transport!=$route->type_transport)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'type_transport')->radioList(\common\models\Route::getTypetransportList()) ?>
            </td>
            <td>
                <?=(\common\models\Route::getTypetransportList())[$route->type_transport]?>
            </td>
        </tr>
        <tr class="<?=($model->type_direction!=$route->type_direction)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'type_direction')->radioList(\common\models\Route::getTypedirectionList()) ?>
            </td>
            <td>
                <?=(\common\models\Route::getTypedirectionList())[$route->type_direction]?>
            </td>
        </tr>
        <tr class="<?=($model->price!=$route->price)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                 <?=$route->price?>
            </td>
        </tr>
        <tr class="<?=($model->organization!=$route->organization)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'organization')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                 <?=$route->organization?>
            </td>
        </tr>
        <tr class="<?=($model->info!=$route->info)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>
            </td>
            <td>
                <?=$route->info?>
            </td>
        </tr>
        
        <tr class="<?=($model->season!=$route->season)?"diff":"";?>">
            <td>
               <?= $form->field($model, 'season')->radioList([1=>'Да',0=>'Нет',2=>'Дачный']) ?>
            </td>
            <td>
                <?=$route->season?>
            </td>
        </tr>
        
        <? $old_time_work=((is_numeric($route->time_work))?(($route->getExtraFields())[4]->value.'\n\n'.($route->getExtraFields())[5]->value):$route->time_work);?>
        <tr class="<?=($model->time_work!=$old_time_work)?"diff":"";?>">
            <td>
               <?/* <div><? var_dump($model->time_work); ?></div>
                <div><? var_dump($old_time_work); ?></div>*/ ?>
                <?= $form->field($model, 'time_work')->textarea(['rows' => 6]) ?>
            </td>
            <td>
                 <?=$old_time_work;?>
            </td>
        </tr>  
        <tr class="<?=($model->route_text!=$route->route_text)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'route_text')->textarea(['rows' => 6]) ?>
            </td>
            <td>
                <?=$route->route_text?>
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
               <? echo $this->render('_marshrutadmin', [
                    'model' => $route,
                    'create'=>$create
                ]); ?>
            </td>
        </tr>
        <tr class="<?=($model->source!=$route->source)?"diff":"";?>">
            <td>
                <?= $form->field($model, 'source')->textarea(['rows' => 6]) ?>
            </td>
            <td>
                <?=$route->source?>
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
