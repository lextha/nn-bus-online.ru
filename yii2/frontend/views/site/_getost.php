<?
use common\helpers\TimeHelper;
use yii\helpers\Url; //var_dump($time_work);

//var_dump($s);
if (count($s)>0) {
?>
		<div class="city-route-item v0">
		<div class="subtitle">
			<div class="tx">Маршруты через остановку</div>
		</div>
		<ul>
                    <? foreach ($s as $ss) { ?>
			<li>
				<a href="<?=Url::toRoute(['site/route', 'id' => $ss['id']]);?>">
					<span class="level"><?=($ss['number']=='')?"-":$ss['number'];?></span>
					<span class="value"><?=$ss['name'];?></span>
				</a>
			</li>
                    <? } ?>
		</ul>
	</div>
<? } else { ?>
        <div class="city-route-item v0">
		<div class="subtitle">
			<div class="tx" style="color:red;">Маршрутов не найдено</div>
			<? /* <div class="nm">(10 маршрутов)</div> */?>
		</div>
          
	</div>
<? } ?>
