<?php
function nl2br2($string) {
$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
return $string;
}

function num2word($num, $words) //echo num2word(50, array('год', 'года', 'лет'));
{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1: {
            return($words[0]);
        }
        case 2: case 3: case 4: {
            return($words[1]);
        }
        default: {
            return($words[2]);
        }
    }
}


function full_trim($str){
    $str=str_replace("	", " ", $str);
    return trim(preg_replace('/\s{2,}/', ' ', $str));
}

$marshrut_in=[];
$marshrut_out=[];
if (is_array($marshrut) AND count($marshrut)>0) {
    foreach ($marshrut as $key => $value) {     //   var_dump($value[2],strpos($value[2],"in"));
        if (strpos($value[2],"in")>-1) { $marshrut_in[]=$value; }
        else { $marshrut_out[]=$value; }
    }
}
if (count($marshrut_out)<2) { unset($marshrut_out); } // если обратного маршрута нет, но в массив первый элемент записался
$time_flag=false;

if (count($marshrut_in)>0) { 
    foreach ($marshrut_in as $in) { 
    
        if (!is_array($in[1])) { //echo "<pre>"; var_dump($in); die();
            $time=explode("/",$in[1]);
            if (count($time)==2) { 
                if ($time_flag!='all') { $time_flag="vyh"; }
            } 
            elseif (count($time)>2) {
                $time_flag="all"; 
            }
        }
    }
}
/*********** ПРЯМОЙ МАРШРУТ *************/
function vydel($text){
    $ttl=explode("|", $text);
    $text= full_trim($ttl[0]);
    $text=preg_replace('/\(([^\)(]*)( )+(([^\)(]*))\)/Uu', '($1++$3)', $text);
    $text=preg_replace('/\(([^\)(]*)( )+(([^\)(]*))\)/Uu', '($1++$3)', $text);
    $text=preg_replace('/\(([^\)(]*)( )+(([^\)(]*))\)/Uu', '($1++$3)', $text);
    $array=explode(" ", $text);
    $flag=false;
    $arr='';
   /* if ($_SERVER['REMOTE_ADDR']=='5.187.69.69') {
        var_dump($ttl);
    }*/
    foreach ($array as $key => $value) {
        if ($value!='') {
            $value= str_replace("++", " ", $value);
            if (preg_match("/(\d+)[:-](\d+)(.*)$/", $value)) {
                if ($flag) {
                    $arr.="</span>";
                }
                $arr.=" <span>".$value."</span>";
                $flag=false;
            } else {
                if ($flag) {
                    $arr.=$value;
                } else {
                    $arr.=" <span>".$value." ";
                }
               $flag=true;
            }
        }
    }
    if (isset($ttl[1])) {
        $arr.="<span class='d78'>".nl2br2($ttl[1])."</span>";
    }
    return $arr;
}
function marshrut_html($marsh) {
 $i=1;$ii=0;
 $html='';
    foreach ($marsh as $in) { 
        $ii++;
        if ($in[0]!='legend') {
        if (!is_array($in[1])) { $time=explode("/",$in[1]); } else { $time=[]; }
        $html.="<li>";
        /*if ($i==0) { 
            $html.="class='odd";
            if ($ii==count($marsh)) { $html.=" last"; } 
            $html.="'";
            $i++; 
        } else { 
            if ($ii==0) { $html.="class='first'";  } 
            if ($ii==count($marsh)) { $html.="class='last'"; } 
            $i=0; 
        } */
        
      //  $html.='>';
        $time1=array();
        foreach ($time as $key1 => $tt1) {
            //$tt1=full_trim($tt1);
          //  if ($_SERVER['REMOTE_ADDR']=='82.151.118.232') {
                if ($tt1=='') { $time1[$key1]=''; } else { 
                    $expl=[];//explode("*", $tt1);
                    if (count($expl)>1) { 
                        $time1[$key1]='';
                        //$tt1=explode("*", $tt1); 
                        foreach ($expl as $val_t) {
                            //if (iconv_strlen($val_t)>8) { $val_t=ex$val_t }
                          /*  $val_t=str_replace("(red)"," ",$val_t);
                            $val_t=str_replace("(green)"," ",$val_t);*/
                          
                            $time1[$key1].="<span>".$val_t."</span> ";
                        }
                    } 
                    else { $time1[$key1]=vydel($tt1); }
                }
        /*    } else {
               $time1[$key1]= str_replace(" ", "</span> <span>", $tt1);
                if (!empty($time1[$key1])) { $time1[$key1]="<span>".$time1[$key1]."</span>"; }
           }*/
        }
        $time=$time1;
       // if ($_SERVER['REMOTE_ADDR']=='5.187.69.69') { echo "<pre>"; var_dump($time); echo "</pre>"; }
       if (count($time)<2) {
           $html.='<div class="level">'.$in[0].'</div>';
           if ($time[0]!='') { $html.='<div class="hide show bud_t value"><span class="title_time">Будни</span>'.$time[0].'</div>'; }
       }
       elseif (count($time)==2) {
           $html.='<div class="level">'.$in[0].'</div><div class="hide show bud_t value"><span class="title_time">Будни</span>'.$time[0].'</div><div class="hide show vyh_t value" style="display:none;"><span class="title_time">Выходные</span>'.$time[1].'</div>';
       }
       elseif (count($time)>2) {
           $html.='<div class="level">'.$in[0].'</div><div class="hide show bud_t_week_pn value"><span class="title_time">Понедельник</span>'.$time[0].'</div>'
                   . '<div class="hide show bud_t_week_vt value" style="display:none;"><span class="title_time">Вторник</span>'.$time[1].'</div>'
                   . '<div class="hide show bud_t_week_sr value" style="display:none;"><span class="title_time">Среда</span>'.$time[2].'</div>'
                   . '<div class="hide show bud_t_week_cht value" style="display:none;"><span class="title_time">Четверг</span>'.$time[3].'</div>'
                   . '<div class="hide show bud_t_week_pt value" style="display:none;"><span class="title_time">Пятница</span>'.$time[4].'</div>'
                   . '<div class="hide show bud_t_week_sb value" style="display:none;"><span class="title_time">Суббота</span>'.$time[5].'</div>'
                   . '<div class="hide show bud_t_week_vs value" style="display:none;"><span class="title_time">Воскресенье</span>'.$time[6].'</div>'
                   . '';
       }
   // $html.='<div class="name">'.$in[0].'</div>';
    $html.="</li>";
    }
    }
    return $html;
}
$html='';$html_out='';
if (isset($marshrut_in)) { $html=marshrut_html($marshrut_in); }
if (isset($marshrut_out)) { $html_out=marshrut_html($marshrut_out); }
 
 /////////////////////

 //}
 ////////////////////
 ?>
				<div class="route-list">
				<? if (isset($marshrut_in) AND count($marshrut_in)>1) { ?>
                                        <div class="item item-1" <? if (!isset($marshrut_out)) { echo "style='width:100%'"; }?>>
                                                    <div class="subtitle"><span>Прямой маршрут</span><?/* <a href="#" class="print"  onclick="window.print();"></a>*/ ?></div>
							<ul class="route-list-ul">
									<?/*<div class="name"><a href="#">ул.Королева</a></div>
									<div class="hide show">
										<span>5:28</span> <span>6:01</span> <span>6:18</span> <span>6:21</span> <span>6:35</span> <span>10:30</span> <span>11:20</span>
										<span>12:10</span> <span>13:00</span> <span>20:00</span> <span>20:40</span> <span>21:20</span> <span>22:00</span>
										<div class="clear"></div>
										<span>5:28</span> <span>6:01</span> <span>6:18</span> <span>6:21</span> <span>6:35</span> <span>10:30</span> <span>11:20</span>
										<span>12:10</span> <span>13:00</span> <span>20:00</span> <span>20:40</span> <span>21:20</span> <span>22:00</span>
									</div>*/
 
$html=str_replace("(red)"," ",$html); 
$html=str_replace("(green)"," ",$html); 
$html=str_replace("(darkblue)"," ",$html);
$html=str_replace("(#ff69b4)"," ",$html);


 echo $html; ?>
                                                        </ul>		
					</div><!-- #column -->
                                <? } ?>

				<? if (isset($marshrut_out) AND count($marshrut_out)>1) { ?>	
					<div class="item item-2">
                                                        <div class="subtitle"><span>Обратный маршрут</span><? /* <a href="#" class="print" onclick="window.print();"></a>*/ ?></div>
							
                                                        <ul class="route-list-ul">
								<? 
                                                                $html_out=str_replace("(red)"," ",$html_out); 
$html_out=str_replace("(green)"," ",$html_out); 
$html_out=str_replace("(darkblue)"," ",$html_out);
$html_out=str_replace("(#ff69b4)"," ",$html_out);



echo $html_out?>
							</ul>		
					</div><!-- #column -->
                                <? } ?>
                                         
				</div><!-- #budni -->			
			
			