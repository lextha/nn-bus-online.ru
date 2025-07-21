<?
//$marshrut
//var_dump($model); die();
$marshrut=$model->getMarshrut();

if ($create) { $marshrut=[]; }
if ((is_array($marshrut) AND count($marshrut)>0) OR $create) {
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

        <div class="simpleTabsContent" id="k2Tab86">
            
            <table>
                <th>Туда</th>
                <th></th>
                <th>Обратно</th>
                <tr>
                    <td><? //var_dump($marshrut); ?>
                <table id="table_in2">
                    <? 
                    $i=0;
                    if ($marshrut) {
                        foreach ($marshrut as $marsh) { 
                            $marsh['type']= trim($marsh['type']); if ($marsh['type']=='in') { $i++; } }
                    }
                    if ($i<1) {
                        echo "<tr><td><input type='text' class='text_area' value='' />"
                        . "<br>"
                        . "<textarea></textarea>";
                    }
                    if ($marshrut) {
                        foreach ($marshrut as $marsh) {  $marsh['type']= trim($marsh['type']); if ($marsh['type']=='in') { ?>
                        <tr>
                            <td>
                                <input type="text" class="text_area" value="<?=htmlspecialchars($marsh['name']);?>" /><br>
                                <textarea><?=((is_array($marsh['time']))?print_r($marsh['time']):$marsh['time'])?></textarea>
                            </td>
                        </tr>
                        <? } }
                    } ?>
                </table>
            </td>
            <td></td>
            <td>
                <table id="table_out2">
                    <? 
                    $i=0;
                    if ($marshrut) { 
                        foreach ($marshrut as $marsh) {  $marsh['type']= trim($marsh['type']); if ($marsh['type']=='out') { $i++; } }
                    }
                    if ($i<1) {
                        echo "<tr><td><input type='text' class='text_area' value='' />"
                        . "<br>"
                        . "<textarea></textarea>";
                    }
                    if ($marshrut) {
                        foreach ($marshrut as $marsh) { $marsh['type']= trim($marsh['type']); if ($marsh['type']=='out') { ?>
                        <tr>
                            <td>
                                <input type="text" class="text_area" value="<?=htmlspecialchars($marsh['name']);?>" /><br>
                                <textarea><?=((is_array($marsh['time']))?print_r($marsh['time']):$marsh['time'])?></textarea>
                            </td>
                        </tr>
                        <? } } 
                    } ?>
                </table>
                </td>
                </tr>
            </table>
        </div>
<? } ?>