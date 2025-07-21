<?
/*var_dump($route);
die();*/
foreach ($stations as $rr) {
    ?> 
    <ul class="scheme-list"> <? foreach ($rr as $r) {
        if (isset($r['bus_arrive']) AND $r['bus_arrive']) {
            ?>
                <li class="scheme-list-bus">
                    <span class="bus__anim"></span>
                    <span class="scheme-list__mark">
                        <?=$route['number'];?><?/* <br>
                        10 км/ч*/ ?>
                    </span>
                </li>
        <? } ?>
            <li class="scheme-list-item">
                <span class="scheme-list__text">
                    <b><?= $r['name'] ?></b>
                </span>
                <? if (isset($r['time'])) { ?>
                    <span class="scheme-list__mark" style='margin-left: 10px;'>
                        <?=$r['time'];?>
                    </span>
                <? } ?> 
            </li>


        <? }
        ?> </ul><?
}
?>
       

