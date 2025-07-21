<?
use common\helpers\TimeHelper;
use yii\helpers\Url; //var_dump($time_work);
$count_routes=count($routes);
if ($count_routes>0) {
?>
<div class="city-route-item">
    <div class="subtitle">
            <div class="tf">Найдено</div>
            <div class="nm">(<?=count($routes);?> маршрутов)</div>
    </div>
    <ul>
<?
foreach ($routes as $r) { 

?>

        <li>
            <a href="<?=Url::toRoute(['site/route', 'id' => $r['id']]);?>">
                <span class='level'><?=($r['number']=='')?"-":$r['number'];?></span>
                <span class="type_tran_search"><span class="<?="ver".$r['type_transport'];?>"></span></span>
                <span class="value"><?=$r['name']?></span>
            </a>
        </li>
 

<? } ?>   
    </ul>
</div>
<? } else { ?>
<div class="city-route-item">
    <div class="subtitle no_routes">
            <div class="txx">Маршрутов не найдено. Уточните поиск</div>
    </div>
</div>
<? } ?>
