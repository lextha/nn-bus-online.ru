<?
use common\helpers\TimeHelper;
use yii\helpers\Url; //var_dump($time_work);
$count=count($cities);
if ($count>0) {
?>

<div class="city-list">
		
    <div class="subtitle">Найдено:</div>

    <div class="block">
        <ul class="findindex">
        <? foreach ($cities as $c) { ?>
              <li>
            <a href="<?=Url::toRoute(['site/city', 'id' => $c['id']]);?>"><?=$c['name']?></a>
        </li>
        <? } ?>
        </ul>
    </div>
</div>
<? } else { ?>
<div class="city-list">
		
    <div class="subtitle">Не найдено. Уточните поиск</div>

</div>
<? } ?>
