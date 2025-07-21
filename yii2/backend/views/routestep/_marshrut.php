<?
//$marshrut
$marshrut=$model->getMarshrut();
//var_dump($marshrut);
if ($create AND $marshrut==NULL) { $marshrut=[]; }
if (is_array($marshrut) OR $create) {
$marsh_array=[];
$i=0;
foreach ($marshrut as $marsh) {
                        $marsh_array[$i]['name']=$marsh[0];
                        $marsh_array[$i]['time']=$marsh[1];
                        $marsh_array[$i]['type']=$marsh[2];
                        $i++;
                    }
$marshrut=$marsh_array;
//var_dump($marshrut);
?> 


<div id="boxes">  
    <div id="red" class="window">
        <div class="top"><button id="button_save" class="btn btn-small"><span class="icon-save"></span>Cохранить</button><a href="#" class="link close"/>X</a></div>
        <div class="content">
            <p><b>Замена символов:<b></p>
            <table>
                <tr>
                    <th>Что заменяем?</th>
                    <th>На что заменяем?</th>
                    <th></th>
                </tr>
                <tr>
                    <td><input id="zam_mark1" type="text" name="zam_mark1" value=""></td>
                    <td><input id="zam_mark2" type="text" name="zam_mark2" value=""></td>
                    <td><button id="zam_button_mark">Заменить</button></td>
                    </tr>
                    <tr><td colspan="3"><label>Стандартные замены</label>
                        <button id="replace_1_3">дефис,минус,тире на :</button>
                        <button id="replace_3_3">точка на :</button>
                        <button id="replace_2_3">удалить запятые</button>
                        <button id="replace_4_3">заменить переносы на !</button>
                        <button id="replace_5_3">удалить пробелы</button>
                        <button class="sort_1">сортировка</button>
                    </td>
                </tr>
            </table> 
            <table class="title_red">
                <tr>
                    <td><input type="radio" class="type_ned0" name="type_ned" val="all" checked="checked">Одно расписание</td>
                    <td><input type="radio" class="type_ned1" name="type_ned" val="vyh">Будни/Выходные</td>
                    <td><input type="radio" class="type_ned2" name="type_ned" val="bud">Пн/Вт/Ср/Чт/Пт/Сб/Вс</td>
                   <?/* <td><input type="radio" class="type_ned3" name="type_ned" val="proiz">Произвольно</td>*/?>
                </tr>
            </table>
            <div id="all_div">
                <span class="areatit">Время</span>
                <textarea name="area0" style="height: 100px"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area0_l"></textarea>
            </div>
            <div id="vyh_div"  style="display: none;">
                <b>Будни</b><br>
                <span class="areatit">Время</span>
                <textarea name="area1" style="height: 100px"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area1_l"></textarea>
                <b>Выходные</b><br>
                <span class="areatit">Время</span>
                <textarea name="area2" style="height: 100px"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area2_l"></textarea>
            </div>
            <div id="bud_div" style="display: none;overflow: scroll; height: 500px;">
                <b>Понедельник</b><br><span class="areatit">Время</span>
                <textarea name="area3"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area3_l"></textarea>
                <b>Вторник</b><br><span class="areatit">Время</span>
                <textarea name="area4"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area4_l"></textarea>
                <b>Среда</b><br><span class="areatit">Время</span>
                <textarea name="area5"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area5_l"></textarea>
                <b>Четверг</b><br><span class="areatit">Время</span>
                <textarea name="area6"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area6_l"></textarea>
                <b>Пятница</b><br><span class="areatit">Время</span>
                <textarea name="area7"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area7_l"></textarea>
                <b>Суббота</b><br><span class="areatit">Время</span>
                <textarea name="area8"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area8_l"></textarea>
                <b>Воскресенье</b><br><span class="areatit">Время</span>
                <textarea name="area9"></textarea>
                <span class="areatit">Легенда</span>
                <textarea name="area9_l"></textarea>
            </div>
        <?/*    <div id="proiz_div" style="display: none;">
                <b>Блок 1</b>
                <textarea name="area10"></textarea>
                <b>Блок 2</b>
                <textarea name="area11"></textarea>
                <b>Блок 3</b>
                <textarea name="area12"></textarea>
                <b>Блок 4</b>
                <textarea name="area13"></textarea>
                <b>Блок 5</b>
                <textarea name="area14"></textarea>
                <b>Блок 6</b>
                <textarea name="area15"></textarea>
                <b>Блок 7</b>
                <textarea name="area16"></textarea>
            </div>*/?>
            
        </div>
    </div>
</div>
<div id="mask"></div>
        <div class="simpleTabsContent" id="k2Tab8">
            <?  if ($marshrut) { ?>
            <button class="btn btn-small btn-border" id="displayred45" style="margin: 10px; font-size: 20px;padding: 10px;">Редактирование</button>
            <button class="btn btn-small btn-border" id="del_all" style="margin: 10px; font-size: 20px;padding: 10px;">Очистить маршрут</button>
                <? } ?>

            <div class="textmarsh" style="border: 1px solid #ccc; padding: 10px;<?  if (isset($marshrut[0]['type'])) { ?>display: none;<? } ?>">
            <p>Список остановок текстом:</p>
            Туда
            <textarea style="width: 406px;" cols="50" rows="5" name="addmarsh" id="addmarsh"></textarea>
            Обратно
            <textarea style="width: 406px;" cols="50" rows="5" name="addmarsh_out" id="addmarsh_out"></textarea>
            <br>
            <p>Замена символов:</p>
            с <input id="zam1" type="text" name="zam1" value=""> на <input id="zam2" type="text" name="zam2" value=""><button id="zam_button" class="btn btn-small btn-border">Заменить</button>
            <br>


            <div class="grid10 sm6 xs12 inpf">
                <label>Стандартные замены</label>
                <button id="replace_1_2" class="btn btn-small btn-border">дефис,минус,тире на :</button>
                <button id="replace_3_2" class="btn btn-small btn-border">точка на :</button>
                <button id="replace_2_2" class="btn btn-small btn-border">удалить запятые</button>
                <button id="replace_4_2" class="btn btn-small btn-border">заменить переносы на !</button>
                <button id="replace_5_2" class="btn btn-small btn-border">удалить пробелы</button>
            </div><br>
            <label>Разделитель остановок в тексте(возможные символы подряд без разделителя):</label>
            <input id="razdel" type="text" name="razdel" value=""><br><button id="addmarsh_button" class="btn btn-small btn-border">Добавить</button>
            <br>
            </div>


            <table>
                <th>Туда</th>
                <th></th>
                <th>Обратно</th>
                <tr>
                    <td><? //var_dump($marshrut); ?>
                <table id="table_in">
                    <? 
                    $i=0;
                    if ($marshrut) {
                        foreach ($marshrut as $marsh) { 
                            $marsh['type']= trim($marsh['type']); if ($marsh['type']=='in') { $i++; } }
                    }
                    if ($i<1) {
                        echo "<tr><td><input type='text' name='marshrut_name_in[]' class='text_area' value='' />"
                        . "<br>"
                        . "<textarea name='marshrut_time_in[]'></textarea>"
                        . "<a href='#red' class='red_marsh' name='modal'><span class=\"glyphicon glyphicon-pencil\"></span></a>"
                        . "<a href='#del' class='del_marsh'><span class=\"glyphicon glyphicon-trash\"></span></a></td></tr>";
                    }
                    if ($marshrut) {
                        foreach ($marshrut as $marsh) {  $marsh['type']= trim($marsh['type']); if ($marsh['type']=='in') { ?>
                        <tr>
                            <td>
                                <input type="text" name="marshrut_name_in[]" class="text_area" value="<?=htmlspecialchars($marsh['name']);?>" /><br>
                                <? //var_dump($marsh); die();?>
                                <textarea name="marshrut_time_in[]"><?=(is_array($marsh['time']))?print_r($marsh['time']):$marsh['time'];?></textarea>
                                <a href="#red" class="red_marsh" name="modal"><span class="glyphicon glyphicon-pencil"></a>
                                <a href="#del" class="del_marsh"><span class="glyphicon glyphicon-trash"></a>
                            </td>
                        </tr>
                        <? } }
                    } ?>
                </table>
                <button id="add_in">Добавить</button>
            </td>
            <td width='10'><button id="otraz"><></button></td>
            <td>
                <table id="table_out">
                    <? 
                    $i=0;
                    if ($marshrut) { 
                        foreach ($marshrut as $marsh) {  $marsh['type']= trim($marsh['type']); if ($marsh['type']=='out') { $i++; } }
                    }
                    if ($i<1) {
                        echo "<tr><td><input type='text' name='marshrut_name_out[]' class='text_area' value='' />"
                        . "<br>"
                        . "<textarea name='marshrut_time_out[]'></textarea>"
                        . "<a href='#red' class='red_marsh' name='modal'><span class=\"glyphicon glyphicon-pencil\"></a>"
                        . "<a href='#del' class='del_marsh'><span class=\"glyphicon glyphicon-trash\"></td></tr>";
                    }
                    if ($marshrut) {
                        foreach ($marshrut as $marsh) { $marsh['type']= trim($marsh['type']); if ($marsh['type']=='out') {    ?>
                        <tr>
                            <td>
                                <input type="text" name="marshrut_name_out[]" class="text_area" value="<?=htmlspecialchars($marsh['name']);?>" /><br>
                                <textarea name="marshrut_time_out[]"><?=(is_array($marsh['time']))?print_r($marsh['time']):$marsh['time'];?></textarea>
                                <a href="#red" class="red_marsh" name="modal"><span class="glyphicon glyphicon-pencil"></a>
                                <a href="#del" class="del_marsh"><span class="glyphicon glyphicon-trash"></a>
                            </td>
                        </tr>
                        <? } } 
                    } ?>
                </table>
                <button id="add_out">Добавить</button>
                </td>
                </tr>
            </table>
        </div>
<? } ?>