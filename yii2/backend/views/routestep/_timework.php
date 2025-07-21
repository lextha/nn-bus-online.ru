<?
use common\helpers\TimeHelper;
use yii\helpers\Url; //var_dump($time_work);
use yii\bootstrap\Modal;
use yii\bootstrap\Html;

$days=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
$days2=['ПН','ВТ','СР','ЧТ','ПТ','СБ','ВС'];
$arr_days=\common\models\Route::getTypedayFromList($type_day);
//var_dump($type_day);
foreach ($days as $k=>$d) {
    if (isset($arr_days[$k+1])) {
        $days2[$k]=$arr_days[$k+1];
        $time=['',''];
        if (isset($s['time_work'][$d])) { $time= TimeHelper::time_helper2($s['time_work'][$d]); }
        Modal::begin([
            'header' => '<h2>'.$days2[$k].' - остановка "'.$s['name'].'"</h2>',
            'toggleButton' => [
                'label' => $days2[$k],
                'tag' => 'button',
                'class' => 'btn btn-sm'.(($time[0]!='')?" btn-success":" btn-outline-info"),
            ],
            'footer' => '',
        ]);
     /*   if ($s['time_work'][$d]!='') {   */         ?>

<p><b>Замена символов:</b></p>
            <table>
                <tr>
                    <th>Что заменяем?</th>
                    <th>На что заменяем?</th>
                    <th></th>
                </tr>
                <tr>
                    <td><input id="zam_mark1_<?="tw_".$d."_".$s['id_station_rout']."";?>" type="text" value=""></td>
                    <td><input id="zam_mark2_<?="tw_".$d."_".$s['id_station_rout']."";?>" type="text" value=""></td>
                    <td><button class="zam_button" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>Заменить</button></td>
                    </tr>
                    <tr><td colspan="3"><label>Стандартные замены</label>
                        <button class="replace_1_4" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>дефис,минус,тире на :</button>
                        <button class="replace_3_4" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>точка на :</button>
                        <button class="replace_2_4" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>удалить запятые</button>
                        <button class="replace_4_4" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>заменить переносы на !</button>
                        <button class="replace_5_4" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>удалить пробелы</button>
                        <button class="replace_6_4" rel='<?="tw_".$d."_".$s['id_station_rout']."";?>'>комплекс ! ; : .</button>
                    </td>
                </tr>
            </table> 
        <div class="value">
            <p> <?=Html::label("Время");?><br>
            <?=Html::textarea("tw[".$d."][".$s['id_station_rout']."]",$time[0],['rows' => 10, 'cols' => 70,'rel'=>"tw_".$d."_".$s['id_station_rout']]);?>
            </p>
            <p>
            <?=Html::label("Легенда");?><br>
            <?=Html::textarea("twps[".$d."][".$s['id_station_rout']."]",$time[1],['rows' => 3, 'cols' => 70]);?>
            </p>
       </div> 
    <?/* } else {
        ?>
      <p> <?=Html::label("Время");?><br>
            <?=Html::textarea("tw[".$d."][".$s['id_station_rout']."]",'',['rows' => 10, 'cols' => 70,'rel'=>"tw_".$d."_".$s['id_station_rout']]);?>
            </p>
            <p>
             <?=Html::label("Легенда");?><br>
            <?=Html::textarea("twps[".$d."][".$s['id_station_rout']."]",'',['rows' => 3, 'cols' => 70]);?>
            </p>
    <?
    }*/
        Modal::end();
    }    
}?>