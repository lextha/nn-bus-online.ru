<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model common\models\Route */
/* @var $form yii\widgets\ActiveForm */

function ifhide($type_day,$day_week,$key_day) {
   // var_dump($type_day,$day_week,$key_day);
    $chars = preg_split("//u", $key_day, 0, PREG_SPLIT_NO_EMPTY);
    ///var_dump($chars);
    if ($type_day==1) {
        if ($day_week<6 AND $chars[0]) { return true; } 
        elseif ($day_week>5 AND $chars[5]) { return true; } else { return false; }
    }  elseif ($type_day==2) { 
        $day_week=$day_week-1;
        return $chars[$day_week];
    } elseif ($type_day==4) { 
        if ($day_week<6 AND $chars[0]) { return true; } 
        elseif ($day_week==6 AND $chars[5]) { return true; } 
        elseif ($day_week==7 AND $chars[6]) { return true; } else { return false; }
    }  elseif ($type_day==5) { 
        
    }
        
    return true;
}
?>
<h3>С картой (form1)</h3>
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
        
        
        <?  if (Yii::$app->user->can('admin')) {
                echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']);
                // echo Html::input('hidden', 'savegood','1'); ?>
         <? }?>
                   <?=Html::a( 'Импорт', "#", ['class' => 'btn btn-warning','id'=>'importdiv']); ?>

        
    </div>
        <div class="importdiv" style="display: none;">
            <textarea name="expyand_json"></textarea>
            <? echo Html::submitButton('Импорт', ['class' => 'btn btn-success','name'=>'expyand','value'=>'1']) ?>
        </div> 
       

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= Html::a('Найти в Яндексе', 
            'https://yandex.ru/search/?text='.urlencode($model->getTypetransport()." ".$model->number." ".$model->name." ".$model->getCityname()), 
            ['class'=>'btn btn-primary','target'=>'_blank']) ?>
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
            
     //var_dump($cr,$route_id);
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
    $extra_fields=$model->getExtraFields(); //   var_dump($extra_fields); ?>
     <?= $form->field($model, 'time_work')->textarea(['rows' => 6,'value'=>$extra_fields[4]->value."\n\n".$extra_fields[5]->value]); ?>
    <? //$form->field($model, 'route_text')->textarea(['rows' => 6,'value'=>$extra_fields[6]->value]); ?>
 <? } else { ?>
    <?= $form->field($model, 'time_work')->textarea(['rows' => 6]) ?>
    <? // $form->field($model, 'route_text')->textarea(['rows' => 6]) ?>
<? } ?>
    
    <?= $form->field($model, 'version')->hiddenInput(\common\models\Route::getVersionList())->label(false); //, ['itemOptions' => ['disabled' => true]] ?> 

  <?= $form->field($model, 'price_edit')->hiddenInput(['value'=> ((isset($model->price_edit)&&$model->price_edit>0)?$model->price_edit:5)])->label(false);  ?> 
    
    <?= $form->field($model, 'type_day')->radioList(\common\models\Route::getTypedayList(),['class'=>'change_r']); //, ['itemOptions' => ['disabled' => true]] ?> 
    
<?= $form->field($model, 'season')->radioList([1=>'Да',0=>'Нет',2=>'Дачный']) ?>
    <? if ($edit) { echo Html::hiddenInput('edit', '1'); } 
     echo $form->field($model, 'user_id')->hiddenInput(['value'=> (($model->user_id)?$model->user_id:Yii::$app->user->id)])->label(false);
    
    ?>
    <div class='route-list'>
        
    <?
    if ($model->version==1 OR $model->version==4) {
        Pjax::begin(['enablePushState' => false,'id' => 'w05']);
        ?><ul class="nav nav-tabs"> <?
        foreach ($stations_all as $key=>$st) { 
            echo "<li class='".(($key==0)?'active':'')."'><a href='#route_direction_".$key."'>".$st[0]['name']." - ".$st[array_key_last($st)]['name']."</a></li>";
        }
        ?>
        </ul>
        <div class='tab-content'>
            <?
        foreach ($stations_all as $key=>$st) { ?>
            <div class="item item-1" id="route_direction_<?=$key?>" style="<?=(($key!=0)?'display: none;':'');?>">
			
			<div class="subtitle"><span><?=$st[0]['name'];?> - <?=$st[array_key_last($st)]['name'];?></span></div>

			<ul class='route_direction'>
                            <? 
                            $f=true;
                            $count0=0;
                            $day_week=1;
                            foreach ($st as $s)  {   if(!ifhide($model->type_day,$day_week,$s['key_day'])) { continue; }                             
                                ?> 
				<li idi='<?=$s['id_station_rout'];?>'>
                                    <div class="level">
                                            <?=$s['name']?>
                                    </div>
                                    <?= $this->render('_timework', [
                                   's' => $s,
                                   'type_day' => $type_day,
                                   'day_week' => $day_week,
                                   'f'=>$f,
                                    'flag2'=>false
                                ])  ?>
                                    <? 
                                    $f=false;
                                    ?>
				</li>
                            <? $count0++;
                            } ?>
			</ul>

		</div>
        <?
            
        } ?>
    </div>
        <?/*
        if ($stations0) { ?>
		<div class="item item-1">
			
			<div class="subtitle"><span>Прямой маршрут</span></div>

			<ul class='route_direction'>
                            <? 
                            $f=true;
                            $count0=0;
                            $day_week=1;
                            foreach ($stations0 as $s)  {   if(!ifhide($model->type_day,$day_week,$s['key_day'])) { continue; }                             
                                ?> 
				<li idi='<?=$s['id_station_rout'];?>'>
                                    <div class="level">
                                            <?=$s['name']?>
                                    </div>
                                    <?= $this->render('_timework', [
                                   's' => $s,
                                   'type_day' => $type_day,
                                   'day_week' => $day_week,
                                   'f'=>$f,
                                    'flag2'=>false
                                ])  ?>
                                   <?
                                    $f=false;
                                    ?>
				</li>
                            <? $count0++;
                            } ?>
			</ul>

		</div>
            <? } ?>
		<? if ($stations1) { ?>
		<div class="item item-2">
			
			<div class="subtitle"><span>Обратный маршрут</span></div>

			<ul class='route_direction'>
			  <? 
                            $f=true;
                            $count1=0;
                            foreach ($stations1 as $s)  { if(!ifhide($model->type_day,$day_week,$s['key_day'])) { continue; } ?> 
				<li idi='<?=$s['id_station_rout'];?>'>
                                    <div class="level">
                                            <?=$s['name']?>
                                    </div>
                                    <?= $this->render('_timework', [
                                   's' => $s,
                                   'type_day' => $type_day,
                                   'day_week' => $day_week,
                                   'f'=>$f,
                                        'flag2'=>false
                                ])  ?>
                                    <?
                                    $f=false;
                                    ?>
				</li>
                            <? $count1++;
                            } ?>
			</ul>

		</div>
                <? }*/ ?>
        
   <? /*echo $this->render('_marshrutmap', [
        'model' => $model,
    ]); */
   
        Pjax::end();
        } ?>
    </div><div class='clear'></div>
     <?= Html::a( 'Показать старое расписание', "#", ['class' => 'btn btn-warning','id'=>'displaylast']); ?>
    <div class="displaylast" style="display: none;"> 
   <?   
   
    echo $this->render('_marshrut', [
        'model' => $model,
        'create'=>1
    ]);  ?>
    </div>
    <? //echo "<pre>"; var_dump($model->source); ?>
    <?= $form->field($model, 'source')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?> 
       <? if (Yii::$app->user->can('admin')) {
                echo Html::submitButton('Сохранить и одобрить', ['class' => 'btn btn-success','name'=>'savegood','value'=>'1']);
        }?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
