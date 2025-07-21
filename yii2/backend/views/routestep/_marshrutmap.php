<?  if ($stations0) { ?>
		<div class="item item-1">
			
			<div class="subtitle"><span>Прямой маршрут</span><?/*<a href="" class="print"></a>*/?></div>

			<ul class='route_direction'>
                            <? 
                            $f=true;
                            $count0=0;
                            foreach ($stations0 as $s)  {   if(!ifhide($route->type_day,$day_week,$s['key_day'])) { continue; }                             
                                ?> 
				<li idi='<?=$s['id_station_rout'];?>'>
                                    <?= $this->render('_timework', [
                                   's' => $s,
                                   'type_day' => $route->type_day,
                                   'day_week' => $day_week,
                                   'f'=>$f,
                                    'flag2'=>false
                                ])  ?>
                                    <? /*<div class="level"><?=$s['name']?></div>
                                        <? if ($f) { $f=false; ?>
                                         <div class="value">
                                             <?=(isset($s['time_work']))?TimeHelper::time_g($s['time_work'],$route->type_day,$day_week):'';?>
                                        </div> 
                                        <? } else { ?>
                                         <div class="value_get">
                                            Показать время
                                        </div> 
                                        <? }?>*/
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
			
			<div class="subtitle"><span>Обратный маршрут</span><?/*<a href="" class="print"></a>*/?></div>

			<ul class='route_direction'>
			  <? 
                            $f=true;
                            $count1=0;
                            foreach ($stations1 as $s)  { if(!ifhide($route->type_day,$day_week,$s['key_day'])) { continue; } ?> 
				<li idi='<?=$s['id_station_rout'];?>'>
                                    <?= $this->render('_timework', [
                                   's' => $s,
                                   'type_day' => $route->type_day,
                                   'day_week' => $day_week,
                                   'f'=>$f,
                                        'flag2'=>false
                                ])  ?>
                                    <? /*<div class="level"><?=$s['name']?></div>
                                        <? if ($f) { $f=false; ?>
                                         <div class="value">
                                             <?=(isset($s['time_work']))?TimeHelper::time_g($s['time_work'],$route->type_day,$day_week):'';?>
                                        </div> 
                                        <? } else { ?>
                                         <div class="value_get">
                                            Показать время
                                        </div> 
                                        <? }?>*/
                                    $f=false;
                                    ?>
				</li>
                            <? $count1++;
                            } ?>
			</ul>

		</div>
                <? } ?>
