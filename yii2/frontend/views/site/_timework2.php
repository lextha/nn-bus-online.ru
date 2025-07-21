<?

use common\helpers\TimeHelper;
use yii\helpers\Url; //var_dump($time_work);
/*
?>

		<table class="table">
			<tr>
				<th>час</th>
				<th colspan="11">минуты</th>
			</tr>
			<tr>
				<th>08</th>
				<td class="table__fade">03</td>
				<td class="table__fade">15</td>
				<td class="table__fade">20</td>
				<td class="table__fade">22</td>
				<td class="table__fade">30</td>
				<td class="table__fade">35</td>
				<td class="table__fade">42</td>
				<td class="table__fade">43</td>
				<td class="table__fade">50</td>
				<td class="table__fade">51</td>
				<td class="table__fade">55</td>
			</tr>
			<tr>
				<th>09</th>
				<td class="table__fade">03</td>
				<td class="table__fade">15</td>
				<td class="table__fade">20</td>
				<td class="is_active">20</td>
				<td>30</td>
				<td>35</td>
				<td>42</td>
				<td>43</td>
				<td>50</td>
				<td>51</td>
				<td>55</td>
			</tr>
			<tr>
				<th>19</th>
				<td>03</td>
				<td>15</td>
				<td>20</td>
				<td>20</td>
				<td>30</td>
				<td>35</td>
				<td>42</td>
				<td>43</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th>19</th>
				<td>03</td>
				<td>15</td>
				<td>20</td>
				<td>20</td>
				<td>30</td>
				<td>35</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th>19</th>
				<td class="red">03</td>
				<td>15</td>
				<td>20</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>

<?*/
?>
                <?
                if (isset($s['time_work'])) {
                    $awer = nl2br(TimeHelper::time_g($s['time_work'], $type_day, $day_week));
                    if ($awer == '') {
                        echo '<span class="notime">Маршрут не работает в этот день или расписание не доступно, попробуйте посмотреть маршрут онлайн на схеме.</span>';
                    } else {
                        echo $awer;
                    }
                } else {
                    echo '<span class="notime">Маршрут не работает в этот день или расписание не доступно, попробуйте посмотреть маршрут онлайн на схеме.</span>';
                }
                ?>

