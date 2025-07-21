<? /* <span class="bus__anim"></span> */ ?>
<span class="stops__text"><?= $s['name'] ?></span>
<button class="btn" data-modal="route">Расписание</button>
<?
/*
use common\helpers\TimeHelper;
use yii\helpers\Url; //var_dump($time_work);

if ($flag2) {
    ?>
    <? if ($f) { ?>
        <div class="value">
            <?
            if (isset($s['time_work']) AND $s['time_work'] != '' AND $s['time_work'] != false) {
                //  var_dump($s['time_work']);
                $it = TimeHelper::time_g($s['time_work'], $type_day, $day_week);
                if (mb_strlen($it) > 5) {
                    echo $it;
                } else {
                    echo '<span class="d78">Нет времени. Скорей всего маршрут не работает в этот день.</span>';
                }
            } else {
                echo '<span class="d78">Нет времени. Скорей всего маршрут не работает в этот день.</span>';
            }
            ?>
        </div> 
    <? } else { ?>
        <div class="value" style='font-weight: normal;-webkit-mask-image:-webkit-gradient(linear, left top, right bottom, from(rgba(0,0,0,1)), to(rgba(0,0,0,0)))'>
            <?
            if (isset($time_work)) {
                $it = TimeHelper::time_g($time_work, $type_day, $day_week);
                $it = preg_replace("/<div[^>]+class='.*psinfo.*'[^>]*>(.*)/", '', $it);
                // var_dump($string)
                if (mb_strlen($it) > 5) {
                    $str = nl2br($it);
                    $array = [];
                    preg_match_all('#<([a-z]+)[^/>]*(?:/>|>(?:.+\1>))#Uis', $str, $array);
                    //var_dump($array[0]);
                    $str = '';
                    if (count($array[0]) < 5) {
                        $c = 1;
                    } elseif (count($array[0]) < 20) {
                        $c = 5;
                    } elseif (count($array[0]) >= 20) {
                        $c = 12;
                    }
                    for ($i = 0; $i < $c; $i++) {
                        $str .= $array[0][$i];
                    }
                    echo $str;
                    ?>

                    <?
                } else {
                    echo '<span class="d78">Нет времени. Скорей всего маршрут не работает в этот день.</span>';
                }
            } else {
                //echo '<span class="d78">Нет времени. Скорей всего маршрут не работает в этот день.</span>';
            }
            ?>

        </div> 
        <div class="value_get"> Показать время
        </div>
    <? } ?>
    <?
} else {
    ?><div class="level">
    <? //if ($s['style']=='') {   ?>
        <a href="<?= Url::toRoute(['site/station', 'id' => $s['id'], 'st' => $s, 'city' => $city]); ?>"  <?= (isset($s['style']) && $s['style'] != '') ? 'style="' . $s['style'] . '"' : '' ?>><?= $s['name'] ?></a>
      
    </div>
    <? //if ($s['style']=='') {   ?>
    <?
    if ($f) {
        if (isset($ajax)) {
            ?>
            <div class="value">
                <?
                if (isset($s['time_work'])) {
                    $awer = nl2br(TimeHelper::time_g($s['time_work'], $type_day, $day_week));
                    if ($awer == '') {
                        //echo '<span class="d78">Нет времени. Скорей всего маршрут не работает в этот день.</span>';
                    } else {
                        echo $awer;
                    }
                } else {
                    //echo '<span class="d78">Нет времени. Скорей всего маршрут не работает в этот день.</span>';
                }
                ?>
            </div> 
        <? } else {
            ?>
            <div class="value" style="font-weight: normal;-webkit-mask-image:-webkit-gradient(linear, left top, right bottom, from(rgba(0,0,0,1)), to(rgba(0,0,0,0)))">
                <?
                if (isset($s['time_work'])) {
                    $it = TimeHelper::time_g($s['time_work'], $type_day, $day_week);
                    $it = preg_replace("/<div[^>]+class='.*psinfo.*'[^>]*>(.*)/", '', $it);
                    // var_dump($string)
                    if (mb_strlen($it) > 5) {
                        $str = nl2br($it);
                        $array = [];
                        preg_match_all('#<([a-z]+)[^/>]*(?:/>|>(?:.+\1>))#Uis', $str, $array);
                        //var_dump($array[0]);
                        $str = '';
                        if (count($array[0]) < 5) {
                            $c = 1;
                        } elseif (count($array[0]) < 20) {
                            $c = 5;
                        } elseif (count($array[0]) >= 20) {
                            $c = 12;
                        }
                        for ($i = 0; $i < $c; $i++) {
                            $str .= $array[0][$i];
                        }
                        echo $str;
                        ?>

                        <?
                    }
                } else {
                    echo '';
                }
                ?>

            </div> 
            <div class="value_get"> Показать время
            </div>
        <? }
        ?>
    <? } else { ?>
        <div class="value_get">
            <? //echo TimeHelper::time_g($s['time_work'],$type_day,$day_week); ?>
          
            <?
            if (isset($s['time_work'])) {
                if ((TimeHelper::time_g($s['time_work'], $type_day, $day_week)) != '') {
                    echo 'Показать время';
                }
            }
            ?>
        </div> 
    <? } ?>
    <? //}   ?>
<? } */ ?>