<?php

namespace console\controllers;
use Yii;
use console\helpers\FuncHelper;

use common\models\Station;
use common\models\Route;
use common\models\City;
use common\models\MapRout;
use common\models\TimeWork; 
/**
 * 
 */

class CarlController extends \yii\console\Controller
{
    public $color=['A'=>["red","Красные"],'B'=>["blue","Синие"],'C'=>["Green","Зеленые"],'E'=>["Gold","Желтые"],'F'=>["Brown","Коричневые"],'G'=>["SkyBlue","Голубые"],'I'=>["BlueViolet","Фиолетовые"],
        'K'=>["Orange","Оранжевые"],'M'=>["MediumVioletRed","Розовые"],'O'=>["Gray","Серые"],'Q'=>["Olive","Оливковый"],'S'=>["DarkRed","Темно-красный"],'U'=>["DarkGreen","Темно-зеленый"],'W'=>["DarkBlue","Темно-синий"],'Y'=>["RosyBrown","Розовый"]];
    /* public $color2=[0=>["red","Красные"],1=>["blue","Синие"],2=>["Green","Зеленые"],3=>["Gold","Желтые"],4=>["Brown","Коричневые"],5=>["SkyBlue","Голубые"],6=>["BlueViolet","Фиолетовые"],
        7=>["Orange","Оранжевые"],8=>["MediumVioletRed","Розовые"],9=>["Gray","Серые"]];*/
    public $citis=['ryazan'=>224,'saransk'=>230,'sevastopol'=>234,'saratov'=>232,'irkutsk'=>106];
    
    
    private function translit($name) {
         $string=$name;
                    $string = mb_strtolower($string, 'UTF-8');
                    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
                    $string = strip_tags($string);
                    $string = preg_replace('/[\r\n\t]+/', ' ', $string);

                    $table = [
                            'а' => 'a','б' => 'b','в' => 'v','г' => 'g',
                            'д' => 'd','е' => 'e','ё' => 'yo','ж' => 'zh',
                            'з' => 'z','и' => 'i','й' => 'j','к' => 'k',
                            'л' => 'l','м' => 'm','н' => 'n','о' => 'o',
                            'п' => 'p','р' => 'r','с' => 's','т' => 't',
                            'у' => 'u','ф' => 'f','х' => 'h','ц' => 'c',
                            'ч' => 'ch','ш' => 'sh','щ' => 'csh','ь' => '',
                            'ы' => 'y','ъ' => '','э' => 'e','ю' => 'yu',
                            'я' => 'ya',' ' => '-','«' => "",'»' => "",'№'=>""
                    ];

                    $output = str_replace(
                            array_keys($table),
                            array_values($table),$string
                    );

                    $output = str_replace("--","-",$output);
                    $output = str_replace("--","-",$output);
                    $output = str_replace("--","-",$output);
                    $output = str_replace("--","-",$output);

                    $output = preg_replace('/|[^\w-]+|/u','',$output);//var_dump($output);
                   // $output = preg_replace('/[\x00-\x1F\x7F]/u', '', $output);

                    $output = str_replace("--","-",$output);
                    $output = str_replace("--","-",$output);

                    $output = trim($output,"-");
                    return $output;
    }

    private function set_time($row) {
        if (!is_array($row)) { return; }
           //  var_dump($row);
            $k=["monday"=>0,"tuesday"=>1,"wednesday"=>2,"thursday"=>3,"friday"=>4,"saturday"=>5,"sunday"=>6];
            $out=['','','','','','',''];
            foreach ($row as $key=>$rr) { // по дням ["monday"]=>string(814) "[["05",["34","46","59"]],["06",["11","18","25","31","37","43","49","55"]],["07",["01","07","13","26","39","46","53","59"]],["08",["05","11","17","23","29","35","41","47","54"]],["09",["01","08","14","21","27","34","45","57"]],[10,["0
              
                if ($key!='station_rout_id') {  //var_dump($rr); die('+*');
                    $time=[]; 
                    $rr_a=json_decode($rr); //var_dump($rr_a); 
                    if (isset($rr_a) AND is_array($rr_a)) {
                        foreach ($rr_a as $krak=>$r) { // по часам ["05",["34","46","59"]]

                            if ($krak=='ps') {
                                $itog=$r[0];
                                $arr=[];
                                preg_match_all('/<div><strong><div style="color: ([a-zA-z]*);" class="div-text">.*<\/div>:<\/strong>(.*)<\/div>/U', $r[0], $arr);
                               // var_dump($arr);
                                foreach ($arr[0] as $ki=>$a) {
                                     $itog=str_replace($a, '', $itog); 
                                } 
                                if (strlen($itog)>3) {
                                    $time[]="interval*".":".$itog;
                                }
                               // $time[]="legend*".":(".$a.") ".$arr[2][$ki];
                                foreach ($arr[1] as $ki=>$a) {
                                     $time[]="legend*".":(".$a.") ".$arr[2][$ki];
                                } 

                            }
                            if (isset($r[1])) {
                                foreach ($r[1] as $m) {
                                     $time[]=$r[0].":".$m;
                                }
                            }
                        }
                    }
                    $out[$k[$key]]= implode("|", $time);
                }
            }
            $text= implode("/", $out);
          // var_dump($text); die();
            return $text;
    }
    public function update_busget($r) {
        
   //    error_reporting(E_ERROR | E_PARSE);
        
        $new_date=date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s').' -766000 seconds'));
        $rt=Route::find()->where(['id'=>$r->id])->one();
        
        $catid_arr=[207=>747,284=>1115,138=>488,32=>66,295=>298,596=>326,117=>384,1124=>1071,1082=>1019,37=>95,455=>109,293=>242,1165=>1134,305=>428,388=>530,
        375=>574,8=>584, 809=>617,13=>671,212=>787,872=>725,
        1166=>1136,1029=>944,1036=>953,1045=>970,1167=>1137,281=>1103,
        1164=>1132,410=>42,1169=>1138,283=>1110,20=>35,440=>87,1171=>97, 120=>406, 407=>34, 225=>826,234=>848,239=>871,269=>1021]; /// goonbus=>busget
        /*
         * goonbus 1-автобус,2-троллейбус, 3-трамвай, 4-маршрутки, 5-электрички
         * busget  1 - Автобусы 2 - Маршрутки 3-Троллейбусы 4-Трамваи 5-Электрички 6-Водный транспорт 7-Монорельс 8-Фуникулёр 10-Другое !!!!! / 1- город,2 - пригор,3-межг
         */
        $type_transport=[1=>1,2=>3,3=>4,4=>2,5=>5];
        
        $catid=$catid_arr[$rt->city_id];
       // $connection = Yii::$app->dbbusget;      
      //  $connection->createCommand()->select('qcp4s_content', ['introtext' => $route->id], 'id = '.$r['id'])->execute();
        $created_by_alias=$type_transport[$rt->type_transport]."/".$rt->type_direction;
        $q="SELECT * FROM qcp4s_content WHERE `introtext`='".$rt->number."' AND catid=".$catid." AND created_by_alias='".$created_by_alias."'";
        $routeb=Yii::$app->dbbusget->createCommand($q)->queryOne();
        if (!$routeb) {// если маршрута не найдено     
            $ost_text='';
            $new_date1=date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s').' -536050 seconds'));
            $new_date2=date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s').' -360000 seconds'));
            Yii::$app->dbbusget->createCommand("SET SQL_MODE='ALLOW_INVALID_DATES'")->execute();   
            $query ="INSERT INTO `qcp4s_content` (`id`, `asset_id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `created_by`, 
                `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, 
                `version`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `featured`, `language`, `xreference`, `note`) 
                VALUES ('0', '8115', '".$rt->name."', '". $this->translit($rt->number.'-'.$rt->name)."', '".$rt->number."', '".$ost_text."', '1', '".$catid."', '".$new_date."', '22', '".$created_by_alias."', 
                    '".$new_date1."', '0', '0', '000-00-00 00:00:00', 
                    '".$new_date2."', '000-00-00 00:00:00', '{\"image_intro\":\"\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}', '{\"urla\":\"\",\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":\"\",\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":\"\",\"urlctext\":\"\",\"targetc\":\"\"}', '{\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}', '1', '13', '', '', '1', '0',
                    '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', '0', '*', '', '')";
          //  var_dump($query); die();
            Yii::$app->dbbusget->createCommand($query)->execute();    
            $q="SELECT * FROM qcp4s_content WHERE `introtext`='".$rt->number."' AND catid=".$catid." AND created_by_alias='".$created_by_alias."'";
            $routeb=Yii::$app->dbbusget->createCommand($q)->queryOne();
        }
        $exit=[];
        $st=$rt->getStationRouteAll($rt->id);
        $key=0;
        $map_route=Route::getMaproute($rt->id);
     //  var_dump($map_route); die();
        foreach ($st[0] as $s) {
            $exit['in']['ostanovki'][$key]['id']=$s['temp_id'];
            $exit['in']['ostanovki'][$key]['name']=$s['name']; /////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! модернизация
            $exit['in']['ostanovki'][$key]['x']=$s['y'];
            $exit['in']['ostanovki'][$key]['y']=$s['x'];
            $row = Yii::$app->db->createCommand('SELECT * FROM time_work WHERE station_rout_id='.$s['id_station_rout'].'')->queryOne();
            //var_dump($exit,$row); die();            
            $exit['in']['ostanovki'][$key]['time']= $this->set_time($row);
            $exit['in']['route']= json_decode($map_route[0]['line']);
            $key++;
        }
        $key=0;
        foreach ($st[1] as $s) {
            $exit['out']['ostanovki'][$key]['id']=$s['temp_id'];
            $exit['out']['ostanovki'][$key]['name']=$s['name']; /////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! модернизация
            $exit['out']['ostanovki'][$key]['x']=$s['y'];
            $exit['out']['ostanovki'][$key]['y']=$s['x'];
            $row = Yii::$app->db->createCommand('SELECT * FROM time_work WHERE station_rout_id='.$s['id_station_rout'].'')->queryOne();
            $exit['out']['ostanovki'][$key]['time']=$this->set_time($row);
            $exit['out']['route']=json_decode($map_route[0]['line']);
            $key++;
        }
        
        /////////////////////
         /////////000000000000 Удаляем старую инфу маршрута
        
        $qer="DELETE FROM qcp4s_map_marsh_ost WHERE id_marshrut=".$routeb['id'];
        Yii::$app->dbbusget->createCommand($qer)->execute(); 
       // var_dump($qer,$qqqw); die();
        ///////////////START 111111111 Удалили и записали маршрут (линию) туда-назад        
        $qer="DELETE FROM qcp4s_map_marshrut WHERE id_marshrut=".$routeb['id'];
        $rrt=Yii::$app->dbbusget->createCommand($qer)->execute(); 
       // echo $rrt."-del".PHP_EOL;
        if (!isset($exit['in']['route'])) { $exit['in']['route']=''; }
        if (!isset($exit['out']['route'])) { $exit['out']['route']=''; }

        $route_a= json_encode(array($exit['in']['route'],$exit['out']['route']));

        $qer="INSERT INTO qcp4s_map_marshrut (id_marshrut, line) VALUES (".$routeb['id'].",'".$route_a."')";
        $rrt=Yii::$app->dbbusget->createCommand($qer)->execute();
        //echo $rrt."-ins".PHP_EOL;
        /***********************************************************************/
        
        ///////////START222222222222222222222222222
            
     
        $ost_arr=[]; $ost_arr2=[];
        $sort=1;
        if (isset($exit['in']['ostanovki']) AND count($exit['in']['ostanovki'])>1) {
            foreach ($exit['in']['ostanovki'] as $ost) {
                $ost_arr[]=$ost['name'];
                $query ="SELECT * FROM qcp4s_map_ostanovka WHERE id_msk='".$ost['id']."' AND city_id=".$catid;
                $ost_base = Yii::$app->dbbusget->createCommand($query)->queryAll();
                //var_dump($ost_base);
                if($ost_base) {
                    $id_new_ost=$ost_base[0]['id'];
                    $qer="UPDATE `qcp4s_map_ostanovka` SET `name` = '".$ost['name']."', x='".$ost['x']."',y='".$ost['y']."' WHERE `id` = ".$id_new_ost.";";
                    Yii::$app->dbbusget->createCommand($qer)->execute();
                } else {
                    $query="INSERT INTO qcp4s_map_ostanovka (id, name,x,y,city_id,id_msk) VALUES (NULL,'".$ost['name']."','".$ost['x']."','".$ost['y']."','".$catid."',".$ost['id'].")";
                    Yii::$app->dbbusget->createCommand($query)->execute();
                    $id_new_ost=Yii::$app->dbbusget->getLastInsertID();
                }
                $query="INSERT INTO qcp4s_map_marsh_ost (id_marshrut, id_ostanovka,sort, type, time) VALUES ('".$routeb['id']."','".$id_new_ost."','".$sort."','in','".$ost['time']."')";
                Yii::$app->dbbusget->createCommand($query)->execute();
                $sort++;
            }
           // $marsh2='out';
        } else { // маршрута туда нет, значит делаем маршрут обратно туда
           // $marsh2='in';
        }

        $sort=1;
        if (isset($exit['out']['ostanovki']) AND count($exit['out']['ostanovki'])>1) {
            foreach ($exit['out']['ostanovki'] as $ost) {
                $ost_arr2[]=$ost['name'];
                $query ="SELECT * FROM qcp4s_map_ostanovka WHERE id_msk='".$ost['id']."' AND city_id=".$catid;
                $ost_base = Yii::$app->dbbusget->createCommand($query)->queryOne();
                if($ost_base) {
                    $id_new_ost=$ost_base['id'];
                    $qer="UPDATE `qcp4s_map_ostanovka` SET `name` = '".$ost['name']."', x='".$ost['x']."',y='".$ost['y']."' WHERE `id` = ".$id_new_ost.";";
                    Yii::$app->dbbusget->createCommand($qer)->execute();
                } else {
                    $query="INSERT INTO qcp4s_map_ostanovka (id, name,x,y,city_id,id_msk) VALUES (NULL,'".$ost['name']."','".$ost['x']."','".$ost['y']."','".$catid."',".$ost['id'].")";
                    Yii::$app->dbbusget->createCommand($query)->execute();
                    $id_new_ost=Yii::$app->dbbusget->getLastInsertID();
                }
                $query="INSERT INTO qcp4s_map_marsh_ost (id_marshrut, id_ostanovka,sort, type, time) VALUES ('".$routeb['id']."','".$id_new_ost."','".$sort."','out','".$ost['time']."')";
                Yii::$app->dbbusget->createCommand($query)->execute();
                $sort++;
            }
           // $marsh2='out';
        } else { // маршрута туда нет, значит делаем маршрут обратно туда
           // $marsh2='in';
        }
            
        ///////////END22222222222222222222222222222
        // обновляем название маршрута
       $ost_text='';
       $nameb=$rt->name;
        if (count($ost_arr)>1) {  
            $ost_text=implode(" ",$ost_arr);
           $nameb=$ost_arr[0]." - ".$ost_arr[count($ost_arr)-1]; 
        } elseif(count($ost_arr2)>1) {  
            $ost_text=implode(" ",$ost_arr2);
            $nameb=$ost_arr2[0]." - ".$ost_arr2[count($ost_arr2)-1]; 
        } 
            
        $qer="UPDATE `qcp4s_content` SET `title` = '".$nameb."',modified='".$new_date."',state=1,created_by_alias='".$created_by_alias."',`fulltext`='".$ost_text."' WHERE `id` = ".$routeb['id'].";";
        Yii::$app->dbbusget->createCommand($qer)->execute();
        echo "busget-".$routeb['id']." - ".$nameb.PHP_EOL;
       // var_dump($exit); die();
    }
    
    public function actionUpdatebusget() { // yii carl/updatebusget - обновить города из goonbus в busget

    }

    public function actionUpdatepohozhie() { // yii carl/updatepohozhie - обновить похожие
        /* переделать обновление */
        $city_ids=[1134,1135,1136,1137,1138,1132,42,95,109,242,404,428,1138,530,574,584,617,671,725,787,1136,944,953,970,1137,1103,1132];
        foreach ($city_ids as $id_city) {
            
            $query ="SELECT * FROM qcp4s_categories WHERE id=".$id_city;
            $city = Yii::$app->dbbusget->createCommand($query)->queryOne();
            
          /*  $queryt = $db->getQuery(true);
            $queryt->select('*')->from('#__content')->where('catid='.$id_city); /// ГОРОД категория // 1134,1135,1136,1137,1138,1132,42,95,109,242,404,428,1138,530,574,584,617,671,725,787,1136,944,953,970,1137,1103,1132
            $db->setQuery($queryt);
            $rows = $db->loadObjectList();*/
        // var_dump($city); die();
            $query ="SELECT * FROM qcp4s_content WHERE catid=".$id_city;
            $rows = Yii::$app->dbbusget->createCommand($query)->queryAll();
            
            foreach ($rows as $r) {
                    $query2="DELETE FROM qcp4s_mod_pohozhie WHERE item_id=".$r['id'];
                    Yii::$app->dbbusget->createCommand($query2)->execute();
            }
            echo 'удалили '.$id_city."-".$city['alias'].PHP_EOL;
        
        
            $post='';
            $result = FuncHelper::curllocal("http://busget/".$city['alias'],$post);
            preg_match_all("/<[Aa][ \r\n\t]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\n\r\t]*([^ \"'>\r\n\t#]+)[^>]*>/",$result['content'],$url);
            //var_dump($url); 
            foreach ($url[1] as $u) {
                $result = FuncHelper::curllocal("http://busget".$u,$post);
                if ($result) {
                    echo $u." good ".PHP_EOL;
                }
            }
            
        }
        die('good');
    }
    
    public function actionTestproxy() { // yii carl/testproxy
        $post='';
        $result = FuncHelper::curlj("https://schoolme.ru/1.php",$post); 
        var_dump($result); 
        die('777');
    }
    
    public function actionRouteactive() { // проверяем активность маршрутов из источника yii carl/routeactive
         /*  $rows = (new \yii\db\Query())
                ->select('*')
                ->from('route_w')
                ->where('number LIKE "%(%" OR number LIKE "%)%" ')//->limit(100)
                ->all(); 
        foreach($rows as $r){
                $value = preg_replace('/(\(.+\))+/', '', $r['number']);
                  // echo $value.PHP_EOL;
                $connection = Yii::$app->db;      
               $connection->createCommand()->update('route_w', ['number' => $value], 'id = '.$r['id'])->execute();
         }
        die();
        */ 
        
            /////////////////TEMP
            //
            /*   $rt= \common\models\RouteRedirect::find()->all(); 
                foreach ($rt as $r) {
                    $route= Route::findOne(['id'=>$r->route_id]);
                    if ($route) {
                      //  var_dump($route); die();
                     //    echo $route->id.PHP_EOL;
                        if ($route->active==1) {
                            $route->active=0;
                            $route->save();
                            echo $route->id."+".PHP_EOL;
                        }
                    }
                }
                die();
            */
            //
            //
      //  $today = date("Y-m-d", mktime(0, 0, 0, 11, 1, 2022)); // время раньше которого обновляем
     //   $today_time = strtotime($today);
        $routes= Route::find()->where('id>0 AND id<100000')->all();
        foreach ($routes as $r) {
            
            
           
            //
        //    var_dump($r->city['name']); die();
           
           // $expire_time = strtotime($r->lastmod); 
            if ($r->type_transport==4) {$r->type_transport=1; }
           // echo $r->number."--------------------------".$expire_time."---".$today_time.PHP_EOL;
            $number2=preg_replace('/\s+/', '', $r->number);
           // if ($expire_time < $today_time) {
                $search_l="c.id=r.city_id AND c.city='".$r->city['name']."' AND (r.number='".$r->number."' OR r.number='".$number2."') AND r.type='".$r->type_transport."';";
                $rows = (new \yii\db\Query())
                ->select('r.id,r.active')
                ->from('route_w as r, city_w as c')
                ->where($search_l)
                ->all(); 
               
                if (count($rows)>0) {
                //    var_dump($rows);
                    $active=0;
                    foreach ($rows as $row) {
                        if ($row['active']==1) {
                            $active=1;
                        }
                    }
                    if ($r->active!=$active AND $active==0) {
                        $r->active=$active;
                        
                        $r->save();
                        echo $r->id." - ".$active."|";
                    //    echo $search_l.PHP_EOL;
                    //  echo "f=".$r->id."-".$r->number."-".$active."|";
                    // $connection = Yii::$app->db;      
                    // $connection->createCommand()->update('route', ['active' => $active,'lastmod'=>date('Y-m-d H:i:s')], 'id = '.$r->id)->execute();
                    }
                    
                } else {
                   //  echo "nf=".$r->id."|";
                }
          /*  } else {
              //  echo "nw=".$r->id."-".$r->number."|";
            }*/
           
        } //die();
      //  var_dump($routes);
        die('ok');
    }
    
    public function actionGobus62($city) {
        
       
        
        $cache = Yii::$app->cache;
        $cache->flush();
       // die();
        $post='';
        $city_id= $this->citis[$city];
        //var_dump($this->citis); die();
        $result = FuncHelper::curlj("http://bus64.ru/php/getStations.php?city=".$city."&info=12345&lang=ru&_=1621514992254",$post); 
        $array_ost=json_decode($result['content']);
        foreach ($array_ost as $key=>$st) {
            $station= Station::find()->where(['temp_id' => $st->id,'city_id'=>$city_id])->one();
            if (!$station) {
               $new_st=new Station;
               $new_st->name=FuncHelper::refresh_name_station($st->name);
               $new_st->city_id=$city_id;
               $new_st->y=FuncHelper::helpcoord($st->lat);
               $new_st->x=FuncHelper::helpcoord($st->lng);
               $new_st->temp_id=$st->id;
               $new_st->info=FuncHelper::refresh_info($st->descr);
               $new_st->save();
            }
        }
       // die();
        $result = FuncHelper::curlj("http://bus64.ru/php/getRoutes.php?city=".$city."&info=12345&lang=ru&_=1621514203992",$post); 
        $array_marshrut=json_decode($result['content']);
       // var_dump($array_marshrut);die();
        $array_m=[];
        // перебор маршрутов для записи типа
        if (!is_array($array_marshrut)) { echo "error1"; die(); }
        foreach($array_marshrut as $key => $value){
          //  if ($key>200) { continue; }
 //var_dump($value->num);
            $num=$value->num;
           ///// тип автобуса
            $ty=$value->type;
            if ($ty=='А') { $type=1;}
            elseif($ty=='М') {
                $type=4;
            }elseif($ty=='Т') {
                $type=2;
            }elseif($ty=='Тр') {
                $type=3;
            }
            $array_marshrut[$key]->type=$type;
            
            /*************************************************************/
            /*меняем город в зависимости от номера маршрута*/
            $city_id = FuncHelper::changeCityid_saratov($value->num,$type); //ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
            var_dump($city_id); 
            /*************************************************************/
            // тип направления
           /* if (intval($value->num)>100 AND strpos($value->name, 'М5МОЛЛ')===FALSE) { /// ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
                $type_d=2;
            } else {*/
            $type_d= FuncHelper::changeTypedirection_saratov($value->num,$type,$city_id); //ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
           // }
            $array_marshrut[$key]->type_d=$type_d;
            /////////
           // var_dump($value->num,$type_d); echo PHP_EOL;
            $result = FuncHelper::curlj("http://bus64.ru/php/getRouteStations.php?city=".$city."&type=0&rid=".$value->id."&info=12345&_=1621514203997",$post);
         // var_dump(json_decode($result['content'])); die();
            $array_ost_marsh=json_decode($result['content']);
            $array_marshrut[$key]->routest=$array_ost_marsh;
            
            $result = FuncHelper::curlj("http://bus64.ru/php/getRouteNodes.php?city=".$city."&type=0&rid=".$value->id."&info=12345&_=1621514203996",$post);
            $array_line_marsh=json_decode($result['content']);
            $array_marshrut[$key]->routeline=$array_line_marsh;
            $array_marshrut[$key]->city_id=$city_id;
            
            $array_m[$num.'-'.$type][]=$array_marshrut[$key];
        }
       /* var_dump($array_m['3А-2'][0]->id,$array_m['3А-2'][1]->id);
        die();*/
        if (!is_array($array_m)) { echo "error2"; die(); }
        foreach ($array_m as $m) {
            $route= Route::find()->where(['temp_route_id' => $m[0]->id,'city_id'=>$m[0]->city_id])->one();
            //var_dump($route); 
            echo $m[0]->id." - ";
            if (!$route) {
                $route=Route::find()->where(['number'=>$m[0]->num,'type_transport' => $m[0]->type,'type_direction'=>$m[0]->type_d,'city_id'=>$m[0]->city_id])->one();
           
                if ($route) { // ОБНОВЛЯЕМ
                    $extra_fields=$route->getExtraFields();
                    if ($extra_fields[5]->value!='') { 
                        $intv="\n\n".'Интервал движения:'."\n".str_replace("<br>", "\n\n", $extra_fields[5]->value); 
                    } else {
                        $intv='';
                    }
                    if ($extra_fields[4]->value!='') { 
                        $graf=$extra_fields[4]->value; 
                    } else {
                        $graf='';
                    }
                    $route->time_work=$graf.$intv;
                    
                } else {
                    $route=new Route;                  //  var_dump($route); die()
                    $route->alias='none';
                    $route->id=0;
                }
                $route->name=$m[0]->fromst." - ".$m[0]->tost;
                if ($m[0]->city_id=='384') {                 
                    $charset = mb_detect_encoding($m[0]->num);
                    $m[0]->num = iconv($charset, "UTF-8", $m[0]->num);
                    $m[0]->num = str_replace("Б", "", $m[0]->num);   
                } elseif ($m[0]->city_id=='383') {      
                     $charset = mb_detect_encoding($m[0]->num);
                    $m[0]->num = iconv($charset, "UTF-8", $m[0]->num);
                    $m[0]->num = str_replace("Э", "", $m[0]->num);   
                }
                 elseif ($m[0]->city_id=='386') {      
                     $charset = mb_detect_encoding($m[0]->num);
                    $m[0]->num = iconv($charset, "UTF-8", $m[0]->num);
                    $m[0]->num = str_replace("С", "", $m[0]->num);   
                }
                 elseif ($m[0]->city_id=='387') {      
                     $charset = mb_detect_encoding($m[0]->num);
                    $m[0]->num = iconv($charset, "UTF-8", $str);
                    $m[0]->num = str_replace("Р", "", $m[0]->num);   
                }
                $route->number=$m[0]->num;
                $route->city_id=$m[0]->city_id;
                $route->type_transport=$m[0]->type;
                $route->type_direction=$m[0]->type_d;
                $route->active=1;
                $route->type_day=3;
                $route->temp_route_id=$m[0]->id;
                $route->version=1;
                
                $route->save();//var_dump($route->errors); die();
                $route->refresh();
                 
              
                /*die();*/
                for($f=0;$f<=1;$f++) {
                    $iu=10;
                    if (is_array($m[$f]->routest)) {
                        foreach ($m[$f]->routest as $routest) {
                             $station=Station::find()->where(['temp_id' => $routest->id,'city_id'=>$m[0]->city_id])->one();
                             if ($station) {
                                 $route->setStationRout($station->id,$f,$iu);
                                 $iu=$iu+10;
                             } else { 
                                 if (($m[0]->city_id>382 AND $m[0]->city_id<388) OR $m[0]->city_id==33) { // добавляем остановки если город в процессе добавления изменился
                                     $new_st=new Station;
                                    $new_st->name=FuncHelper::refresh_name_station($routest->name);
                                    $new_st->city_id=$m[0]->city_id;
                                    $new_st->y=FuncHelper::helpcoord($routest->lat);
                                    $new_st->x=FuncHelper::helpcoord($routest->lng);
                                    $new_st->temp_id=$routest->id;
                                    $new_st->info=FuncHelper::refresh_info($routest->descr);
                                    $new_st->save();
                                    echo $routest->name."-".$routest->id.PHP_EOL; //var_dump($new_st->errors);
                                    $new_st->refresh();
                                    if (!isset($new_st->id)) { echo "ERROR id"; die(); }
                                    $route->setStationRout($new_st->id,$f,$iu);
                                    $iu=$iu+10;
                                 } else {
                                    echo PHP_EOL."! Станция не найдена=".$m[0]->city_id.'-'.$routest->id.PHP_EOL; 
                                 }
                                 
                             }
                        }
                    }
                }
                for($f=0;$f<=1;$f++) {
                    $rl_a=[]; 
                    if (is_array($m[$f]->routeline)) {
                        foreach ($m[$f]->routeline as $rl) {
                            $rl_a[]=[FuncHelper::helpcoord($rl->lat),FuncHelper::helpcoord($rl->lng)];
                        }
                       // var_dump($route->id);
                        $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$f])->one();
                      //  var_dump($rl_a);
                        if ($mapRoute) {
                            $mapRoute->line=json_encode($rl_a);
                        } else {
                            $mapRoute=new MapRout;
                            $mapRoute->route_id=$route->id;
                            $mapRoute->line=json_encode($rl_a);
                            $mapRoute->direction=$f;
                            $mapRoute->active=1;
                        }
                        $mapRoute->save();
                     //   var_dump($mapRoute->errors);
                    }
                } 
                echo $m[0]->num.PHP_EOL;
            }
            //$route=
        }
       // var_dump($array_m); die();
        /*
        
          */
        
       // var_dump($array_ost_marsh); die();
        echo "good";
        die();
    }
    
    public function actionIrkbus($city='irkutsk') {
        
        $cache = Yii::$app->cache;
        $cache->flush();
       // die();
        $post='';
        $city_id= $this->citis[$city];
        //var_dump($this->citis); die();
        $result = FuncHelper::curlj("http://irkbus.ru/php/getStations.php?city=irkutsk&info=12345&_=1628152425859",$post); 
        $array_ost=json_decode($result['content']);
        foreach ($array_ost as $key=>$st) {
            $station= Station::find()->where(['temp_id' => $st->id,'city_id'=>$city_id])->one();
            if (!$station) {
               $new_st=new Station;
               $new_st->name=FuncHelper::refresh_name_station($st->name);
               $new_st->city_id=$city_id;
               $new_st->y=FuncHelper::helpcoord($st->lat);
               $new_st->x=FuncHelper::helpcoord($st->lng);
               $new_st->temp_id=$st->id;
               $new_st->info=FuncHelper::refresh_info($st->descr);
            //   var_dump($new_st); die();
               $new_st->save();
            }
        }
     //   die();
        $result = FuncHelper::curlj("http://irkbus.ru/php/getRoutes.php?city=irkutsk&info=12345&_=1628152425856",$post); 
        $array_marshrut=json_decode($result['content']);
      //  var_dump($array_marshrut);die();
        $array_m=[];
        // перебор маршрутов для записи типа
        if (!is_array($array_marshrut)) { echo "error1"; die(); }
        foreach($array_marshrut as $key => $value){
          //  if ($key>200) { continue; }
 //var_dump($value->num);
            $num=$value->num;
           ///// тип автобуса
            $ty=$value->type;
            if ($ty=='А') { $type=1;}
            elseif($ty=='М') {
                $type=4;
            }elseif($ty=='Т') {
                $type=2;
            }elseif($ty=='Тр') {
                $type=3;
            }
            $array_marshrut[$key]->type=$type;
            
            /*************************************************************/
            /*меняем город в зависимости от номера маршрута*/
           // $city_id = FuncHelper::changeCityid_saratov($value->num,$type); //ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
           // var_dump($city_id); 
            /*************************************************************/
            // тип направления
           /* if (intval($value->num)>100 AND strpos($value->name, 'М5МОЛЛ')===FALSE) { /// ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
                $type_d=2;
            } else {*/
            $type_d= FuncHelper::changeTypedirection_irkutsk($value->num,$type); //ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
           // }
            $array_marshrut[$key]->type_d=$type_d;
            /////////
           // var_dump($value->num,$type_d); echo PHP_EOL;
            $result = FuncHelper::curl("http://irkbus.ru/php/getRouteStations.php?city=irkutsk&type=0&rid=".$value->id."&info=12345&_=1621514203997",$post);
         // var_dump(json_decode($result['content'])); die();
            $array_ost_marsh=json_decode($result['content']);
            $array_marshrut[$key]->routest=$array_ost_marsh;
            
            $result = FuncHelper::curl("http://irkbus.ru/php/getRouteNodes.php?city=irkutsk&type=0&rid=".$value->id."&info=12345&_=1621514203996",$post);
            $array_line_marsh=json_decode($result['content']);
            $array_marshrut[$key]->routeline=$array_line_marsh;
            $array_marshrut[$key]->city_id=$city_id;
            
            $array_m[$num.'-'.$type][]=$array_marshrut[$key];
        }
       /* var_dump($array_m['3А-2'][0]->id,$array_m['3А-2'][1]->id);
        die();*/
        if (!is_array($array_m)) { echo "error2"; die(); }
        foreach ($array_m as $m) {
            $route= Route::find()->where(['temp_route_id' => $m[0]->id,'city_id'=>$m[0]->city_id])->one();
            //var_dump($route); 
            echo $m[0]->id." - ";
            if (!$route) {
                $route=Route::find()->where(['number'=>$m[0]->num,'type_transport' => $m[0]->type,'type_direction'=>$m[0]->type_d,'city_id'=>$m[0]->city_id])->one();
                if ($route) { // ОБНОВЛЯЕМ
                    $extra_fields=$route->getExtraFields();
                    $marsh=$route->getMarshrut();
                     $in=''; $out='';
                    if (count($marsh)>1) {
                        foreach ($marsh as $mar) {
                            if ($mar[2]=='in' AND $mar[1]!='') {
                                $in.=$mar[0].": ".$mar[1]."\n";
                            }
                            if ($mar[2]=='out' AND $mar[1]!='') {
                                $out.=$mar[0].": ".$mar[1]."\n";
                            }
                        }
                    }
                  
                    if ($extra_fields[4]->value!='') { 
                        if ($in!='' OR $out!='') { 
                            $graf="\n".$extra_fields[4]->value;
                        } else {
                            $graf=$extra_fields[4]->value;
                        }
                    } else {
                        $graf='';
                    } 
                    if ($extra_fields[5]->value!='') { 
                        if ($graf=='') {
                            $intv="".'Интервал движения:'."\n".str_replace("<br>", "\n\n", $extra_fields[5]->value); 
                        } else {
                            $intv="\n\n".'Интервал движения:'."\n".str_replace("<br>", "\n\n", $extra_fields[5]->value); 
                        }
                        if (mb_strpos($intv,'Расстояние')) {
                            $intv='';
                        }
                    } else {
                        $intv='';
                    }
                    $route->time_work=$in.$out.$graf.$intv;
                    
                } else {
                    $route=new Route;                  //  var_dump($route); die()
                    $route->alias='none';
                    $route->id=0;
                }
                $route->name=$m[0]->fromst." - ".$m[0]->tost;
              
                $route->number=$m[0]->num;
                $route->city_id=$m[0]->city_id;
                $route->type_transport=$m[0]->type;
                $route->type_direction=$m[0]->type_d;
                $route->active=1;
                $route->type_day=3;
                $route->temp_route_id=$m[0]->id;
                $route->version=1;
                
                $route->save();//var_dump($route->errors); die();
                $route->refresh();
                 
              
                /*die();*/
                for($f=0;$f<=1;$f++) {
                    $iu=10;
                    if (is_array($m[$f]->routest)) {
                        foreach ($m[$f]->routest as $routest) {
                             $station=Station::find()->where(['temp_id' => $routest->id,'city_id'=>$m[0]->city_id])->one();
                             if ($station) {
                                 $route->setStationRout($station->id,$f,$iu);
                                 $iu=$iu+10;
                             } else { 
                                    echo PHP_EOL."! Станция не найдена=".$m[0]->city_id.'-'.$routest->id.PHP_EOL;                                  
                             }
                        }
                    }
                }
                for($f=0;$f<=1;$f++) {
                    $rl_a=[]; 
                    if (is_array($m[$f]->routeline)) {
                        foreach ($m[$f]->routeline as $rl) {
                            $rl_a[]=[FuncHelper::helpcoord($rl->lat),FuncHelper::helpcoord($rl->lng)];
                        }
                       // var_dump($route->id);
                        $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$f])->one();
                      //  var_dump($rl_a);
                        if ($mapRoute) {
                            $mapRoute->line=json_encode($rl_a);
                        } else {
                            $mapRoute=new MapRout;
                            $mapRoute->route_id=$route->id;
                            $mapRoute->line=json_encode($rl_a);
                            $mapRoute->direction=$f;
                            $mapRoute->active=1;
                        }
                        $mapRoute->save();
                     //   var_dump($mapRoute->errors);
                    }
                } 
                echo $m[0]->num.PHP_EOL;
            } else {
                $route->number=$m[0]->num;
                $route->city_id=$m[0]->city_id;
                $route->type_transport=$m[0]->type;
                $route->type_direction=$m[0]->type_d;
                $route->active=1;
                $route->type_day=3;
                $route->temp_route_id=$m[0]->id;
                $route->version=1;
                
                $route->save();//var_dump($route->errors); die();
                $route->refresh();  
            }
            //$route=
        }
       // var_dump($array_m); die();
        /*
        
          */
        
       // var_dump($array_ost_marsh); die();
        echo "good";
        die();
    }
    
     public function actionUlyanovsk() {
        
        $cache = Yii::$app->cache;
        $cache->flush();
       // die();
        $post='';
        $city_id=265;
        //var_dump($this->citis); die();
        $result = FuncHelper::curl("https://bus173.ru/php%20direct/getStations.php?city=ulyanovsk&info=12345&_=1628152425859",$post); 
        $array_ost=json_decode($result['content']);       
        foreach ($array_ost as $key=>$st) { 
           
            $station= Station::find()->where(['temp_id' => $st->id."-".$st->type,'city_id'=>$city_id])->one();
          //   if ($st->id==206) { var_dump($st->id,$station);  die(); }
            if (!$station) {
               $new_st=new Station;
               $new_st->name=FuncHelper::refresh_name_station($st->name);
               $new_st->city_id=$city_id;
               $new_st->y=FuncHelper::helpcoord($st->lat);
               $new_st->x=FuncHelper::helpcoord($st->lng);
               $new_st->temp_id="".$st->id."-".$st->type;
               $new_st->info=FuncHelper::refresh_info($st->descr);
               $new_st->alias='none';
            //   var_dump($new_st); die();
               $new_st->save();//var_dump($new_st->errors); die();
            }
        }
        //die();
        $result = FuncHelper::curl("https://bus173.ru/php%20direct/getRoutes.php?city=ulyanovsk&info=12345&_=1628152425856",$post); 
        $array_marshrut=json_decode($result['content']);
   //     var_dump($array_marshrut);die();
        $array_m=[];
        // перебор маршрутов для записи типа
        if (!is_array($array_marshrut)) { echo "error1"; die(); }
        foreach($array_marshrut as $key => $value){
           // if ($value->num!=107) { continue; }
 echo "NUM=".$value->num.PHP_EOL;
            $num=$value->num;
           ///// тип автобуса
            $ty=$value->type;
            $type_url=0;
            if ($ty=='А') { $type=1;}
            elseif($ty=='М') {
                $type=4;
            }elseif($ty=='Т') {
                $type=2;
            }elseif($ty=='Тр') {
                $type=3;
                $type_url=1; // хз почему этот тип другой в урле у трамваев
            }
            $array_marshrut[$key]->type=$type;
      //      if ($type!=3) { continue; } /// ВРЕМЕННО №№№№№№№№№№№№№№№№№""""""""!!!!!!!!!!!!!!!!!!!!!!
            /*************************************************************/
            /*меняем город в зависимости от номера маршрута*/
           // $city_id = FuncHelper::changeCityid_saratov($value->num,$type); //ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
           // var_dump($city_id); 
            /*************************************************************/
            // тип направления
           /* if (intval($value->num)>100 AND strpos($value->name, 'М5МОЛЛ')===FALSE) { /// ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
                $type_d=2;
            } else {*/
            $type_d= 1;//FuncHelper::changeTypedirection_irkutsk($value->num,$type); //ДЛЯ КАЖДОГО ГОРОДА СВОЕ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
           // }
            $array_marshrut[$key]->type_d=$type_d;
            /////////
           // var_dump($value->num,$type_d); echo PHP_EOL;
            $result = FuncHelper::curl("https://bus173.ru/php%20direct/getRouteStations.php?city=ulyanovsk&type=".$type_url."&rid=".$value->id."&info=12345&_=1621514203997",$post);
         // var_dump(json_decode($result['content'])); die();
            $array_ost_marsh=json_decode($result['content']);
            $array_marshrut[$key]->routest=$array_ost_marsh;
            
            $result = FuncHelper::curl("https://bus173.ru/php%20direct/getRouteNodes.php?city=ulyanovsk&type=".$type_url."&rid=".$value->id."&info=12345&_=1621514203996",$post);
            $array_line_marsh=json_decode($result['content']);
            $array_marshrut[$key]->routeline=$array_line_marsh;
            $array_marshrut[$key]->city_id=$city_id;
            
            $array_m[$num.'-'.$type][]=$array_marshrut[$key];
        }
      /*  var_dump($array_m);
        die();*/
        if (!is_array($array_m)) { echo "error2"; die(); }
        foreach ($array_m as $m) {
            $route= Route::find()->where(['temp_route_id' => $m[0]->id,'city_id'=>$m[0]->city_id])->one();
            //var_dump($route); 
            echo $m[0]->id." - ".PHP_EOL;
            if (!$route) {
                $route=Route::find()->where(['number'=>$m[0]->num,'type_transport' => $m[0]->type,'type_direction'=>$m[0]->type_d,'city_id'=>$m[0]->city_id])->one();
                if ($route) { // ОБНОВЛЯЕМ
                    $extra_fields=$route->getExtraFields();
                    $marsh=$route->getMarshrut();
                     $in=''; $out='';
                    if (count($marsh)>1) {
                        foreach ($marsh as $mar) {
                            if ($mar[2]=='in' AND $mar[1]!='') {
                                $in.=$mar[0].": ".$mar[1]."\n";
                            }
                            if ($mar[2]=='out' AND $mar[1]!='') {
                                $out.=$mar[0].": ".$mar[1]."\n";
                            }
                        }
                    }
                  
                    if ($extra_fields[4]->value!='') { 
                        if ($in!='' OR $out!='') { 
                            $graf="\n".$extra_fields[4]->value;
                        } else {
                            $graf=$extra_fields[4]->value;
                        }
                    } else {
                        $graf='';
                    } 
                    if ($extra_fields[5]->value!='') { 
                        if ($graf=='') {
                            $intv="".'Интервал движения:'."\n".str_replace("<br>", "\n\n", $extra_fields[5]->value); 
                        } else {
                            $intv="\n\n".'Интервал движения:'."\n".str_replace("<br>", "\n\n", $extra_fields[5]->value); 
                        }
                        if (mb_strpos($intv,'Расстояние')) {
                            $intv='';
                        }
                    } else {
                        $intv='';
                    }
                    $route->time_work=$in.$out.$graf.$intv;
                    
                } else {
                    $route=new Route;                  //  var_dump($route); die()
                    $route->alias='none';
                    $route->id=0;
                }
                $route->name=$m[0]->fromst." - ".$m[0]->tost;
              
                $route->number=$m[0]->num;
                $route->city_id=$m[0]->city_id;
                $route->type_transport=$m[0]->type;
                $route->type_direction=$m[0]->type_d;
                $route->active=1;
                $route->type_day=3;
           //     var_dump($m[0]->id); die();
                $route->temp_route_id="".$m[0]->id;
                $route->version=1;
                
                $route->save();//var_dump($route->errors); die();
                $route->refresh();
                 
              
                /*die();*/
                for($f=0;$f<=1;$f++) {
                    $iu=10;
                    if (is_array($m[$f]->routest)) {
                        foreach ($m[$f]->routest as $routest) {
                            $trtr=($m[0]->type==3)?1:0;
                             $station=Station::find()->where(['temp_id' => $routest->id."-".$trtr,'city_id'=>265])->one();
                             if ($station) {
                                 $route->setStationRout($station->id,$f,$iu);
                                 $iu=$iu+10;
                             } else { 
                                    echo PHP_EOL."! Станция не найдена=".$m[0]->city_id.'-'.$routest->id.PHP_EOL;                                  
                             }
                        }
                    }
                }
                for($f=0;$f<=1;$f++) {
                    $rl_a=[]; 
                    if (is_array($m[$f]->routeline)) {
                        foreach ($m[$f]->routeline as $rl) {
                            $rl_a[]=[FuncHelper::helpcoord($rl->lat),FuncHelper::helpcoord($rl->lng)];
                        }
                       // var_dump($route->id);
                        $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$f])->one();
                      //  var_dump($rl_a);
                        if ($mapRoute) {
                            $mapRoute->line=json_encode($rl_a);
                        } else {
                            $mapRoute=new MapRout;
                            $mapRoute->route_id=$route->id;
                            $mapRoute->line=json_encode($rl_a);
                            $mapRoute->direction=$f;
                            $mapRoute->active=1;
                        }
                        $mapRoute->save();
                     //   var_dump($mapRoute->errors);
                    }
                } 
                echo $m[0]->num.PHP_EOL;
            } else {
                $route->number=$m[0]->num;
                $route->city_id=$m[0]->city_id;
                $route->type_transport=$m[0]->type;
                $route->type_direction=$m[0]->type_d;
                $route->active=1;
                $route->type_day=3;
                $route->temp_route_id=$m[0]->id;
                $route->version=1;
                
                $route->save();//var_dump($route->errors); die();
                $route->refresh();  
            }
            //$route=
        }
       // var_dump($array_m); die();
        /*
        
          */
        
       // var_dump($array_ost_marsh); die();
        echo "good";
        die();
    }
    
    
    
    public function actionGobusTest() { ////// КОНСОЛЬ из папки yii: yii carl/gobus-test
       // $r= Route::findOne(['id'=>24239]);
         $r=Route::find()->where(['id'=>24239])->one();
        var_dump($r->name);
        die();
    }
    
    public function actionGobusPerm() { ////// КОНСОЛЬ из папки yii: yii carl/gobus-perm  // последнее обновление 11/10/23
        error_reporting(E_ERROR | E_PARSE);
      // die('perm');
        $keys_day=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $cache = Yii::$app->cache;
        $cache->flush();
       // die();
        $post='';
        $city_id=207;
        //var_dump($this->citis); die();
         $date1=['16.10.2023','17.10.2023','18.10.2023','19.10.2023','20.10.2023','21.10.2023','22.10.2023'];
        $result = FuncHelper::curl("http://www.map.gortransperm.ru/json/route-types-tree/09.10.2023/",$post); 
      //  var_dump($result); die();
        $array_type_marsh=json_decode($result['content']); //var_dump($array_type_marsh); die();
        $first_st=[];
        foreach ($array_type_marsh as $key=>$type_marsh) { if ($key==0) { continue; }  // $key=0-автобусы 1-трамваи 2 - чето еще
           $iie=0;
            foreach ($type_marsh->children as $key324=>$route_p) { $iie++; //if ($iie<16) { continue; }
                $result = FuncHelper::curl("http://www.map.gortransperm.ru/json/full-route-new/09.10.2023/".$route_p->routeId,$post); 
                $route_info=json_decode($result['content']);              //  var_dump($route_info); die();
                
            if  ($route_p->routeNumber>0) {
                $num=$route_p->routeNumber;
                $ty=$route_p->routeTypeId;
                if ($ty=='0') { $type=1;}
                elseif($ty=='2') {
                    $type=3;
                }elseif($ty=='3') {
                    $type=4;
                } else {
                    $type=1;
                }
                $route_info->type=$type;
                $route_info->type_d=1;
                
                
                $route= Route::find()->where(['temp_route_id' =>$route_info->routeId,'city_id'=>$city_id])->one();
         //      var_dump($route_info,$route); die();
                echo $route_info->routeId." - ".$num.'     ';
                
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $route_info->type,'type_direction'=>$route_info->type_d,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';

                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                    }
                    
                }
                $route->name=$route_p->title;//$first_st." - ".$last_st;
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$route_info->type;
                $route->type_direction=$route_info->type_d;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$route_info->routeId;
                $route->version=1;
                
                $route->save();//var_dump($route->errors); die();
                $route->refresh();
                $route->deleteStationRout(); /// Удаляем все направления движения и время маршрута
                //var_dump($route_info->fwdStoppoints); die();
                $arr_dist=[]; $dist_name=[];
                if (count($route_info->fwdStoppoints)>0) { $arr_dist[]=$route_info->fwdStoppoints; $dist_name[]='fwd'; }
                if (count($route_info->bkwdStoppoints)>0) { $arr_dist[]=$route_info->bkwdStoppoints; $dist_name[]='bkwd';  }
                if (count($route_info->twoStoppoints)>0) { $arr_dist[]=$route_info->twoStoppoints; $dist_name[]='two';  }
                if (count($route_info->threeStoppoints)>0) { $arr_dist[]=$route_info->threeStoppoints; $dist_name[]='three';  }
                if (count($route_info->fourStoppoints)>0) { $arr_dist[]=$route_info->fourStoppoints; $dist_name[]='four';  }
                if (count($route_info->fiveStoppoints)>0) { $arr_dist[]=$route_info->fiveStoppoints; $dist_name[]='five';  }

           //     $arr_dist=[$route_info->fwdStoppoints,$route_info->bkwdStoppoints,$route_info->twoStoppoints, $route_info->threeStoppoints, $route_info->fourStoppoints, $route_info->fiveStoppoints];
              //  var_dump($arr_dist); die();
                foreach ($arr_dist as $key_d=>$distpoints) {
                    if (count($distpoints)!=0) { 
                        $iu=0;
                        foreach ($distpoints as $key123=>$st) {

                            $station= Station::find()->where(['temp_id' => $st->stoppointId,'city_id'=>$city_id])->one();
                            if (!$station) {
                               $station=new Station;
                               $station->alias='none';
                               $station->name=FuncHelper::refresh_name_station($st->stoppointName);
                               $station->city_id=$city_id;
                               $point=str_replace(["POINT (",")"],"",$st->location);
                               $points=explode(" ", $point, $city_id);
                               $station->y=FuncHelper::helpcoord($points[1]);
                               $station->x=FuncHelper::helpcoord($points[0]);
                               $station->temp_id="".$st->stoppointId;
                               $station->info=(isset($st->note))?FuncHelper::refresh_info($st->note):'';
                               $station->save();
                               $station->refresh();
                           //    var_dump($station); die();
                               $id_station_rout=$route->setStationRout($station->id,$key_d,$iu);
                              // var_dump($id_station_rout);
                               $iu=$iu+10;
                            } else {
                                $station->name=FuncHelper::refresh_name_station($st->stoppointName);
                                $point=str_replace(["POINT (",")"],"",$st->location);
                                $points=explode(" ", $point, $city_id);
                                $station->y=FuncHelper::helpcoord($points[1]);
                                $station->x=FuncHelper::helpcoord($points[0]);
                                $station->temp_id=$st->stoppointId;
                               $station->info=(isset($st->note))?FuncHelper::refresh_info($st->note):'';
                                $station->save();
                                $station->refresh();
                                $id_station_rout=$route->getStationRout($station->id,$key_d);
                               // var_dump($id_station_rout);
                                if (!$id_station_rout) {
                                    $id_station_rout=$route->setStationRout($station->id,$key_d,$iu);
                                }

                            } 
                            $timework=TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one();
                            if (!$timework) {
                                 $timework=new TimeWork;
                            }
                           // var_dump($station); die();
                            $timework->station_rout_id=$id_station_rout;
                            foreach ($keys_day as $kkk => $d) {
                                $result = FuncHelper::curl("http://www.map.gortransperm.ru/json/time-table-h/".$date1[$kkk]."/".$route_p->routeId."/".$st->stoppointId,$post); 
                                $time=json_decode($result['content']); //var_dump("http://www.map.gortransperm.ru/json/time-table-h/".$date1[$kkk]."/".$route_p->routeId."/".$st->stoppointId,$time); die();
                                $itog_time=[];
                                if (isset($time->timeTable)) {
                                    foreach ($time->timeTable as $tt) {
                                        foreach ($tt->stopTimes as $t) {
                                            //var_dump($t->scheduledTime);
                                            $qwe2=explode(":",$t->scheduledTime);
                                            $itog_time[$qwe2[0]][]=$qwe2[1];
                                        }
                                    }
                                    $itog_time0=[];
                                    foreach ($itog_time as $key=>$it) {
                                        $itog_time0[]=[$key,$it];
                                    }
                                    $timework->$d=json_encode($itog_time0);
                                } else {
                                  //  var_dump($d,$time);
                                    echo $kkk."/".$d." - timeTable - error".PHP_EOL;
                                }
                            }
                          //  var_dump($id_station_rout,$timework); die();
                            $timework->save();//var_dump($route->errors); die();
                            $timework->refresh();
                            if ($key123==0) { $first_st[]=$st->stoppointName;}   //var_dump($first_st);
                        }
                        $arr_line=[];
                        foreach ($dist_name as $d_n) {
                            $d_nn=$d_n."TrackGeom";
                            $arr_line[]=$route_info->$d_nn;
                        } 
                        
                        $itg=str_replace(["MULTILINESTRING",'(',')'], '', $arr_line[$key_d]);
                        $itg=explode(",", $itg);
                        $itog=[];
                        foreach ($itg as $itre) {
                            $itre= trim($itre);
                            $itre=explode(" ", $itre);
                            $itog[]=[FuncHelper::helpcoord($itre[1]),FuncHelper::helpcoord($itre[0])];
                        }
                         $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$key_d])->one();
                         if ($mapRoute) {
                             $mapRoute->line=json_encode($itog);
                         } else {
                             $mapRoute=new MapRout;
                             $mapRoute->route_id=$route->id;
                             $mapRoute->line=json_encode($itog);
                             $mapRoute->direction=$key_d;
                             $mapRoute->active=1;
                         }
                         $mapRoute->save();
                    }
                }       
               // var_dump($first_st);
                $route->name=$first_st[0]." - ".$first_st[1];
                $first_st=[];
                $route->save();
                $route->refresh();
                $this->update_busget($route);
                echo "END - ".$route->number." - ".$route->name; echo PHP_EOL;
                
            
            }
            }
        }
        die('!!!good');
    }
    
    public function actionGobusYarosl() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-yarosl ///////УСТАРЕЛО
       
  
       
       // $keys_day=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $cache = Yii::$app->cache;
        $cache->flush();
       // die();
        $post='';
        $city_id=284;
       //  $date1=['02.08.2021','03.08.2021','04.08.2021','05.08.2021','06.08.2021','07.08.2021','08.08.2021'];
      //  $result = FuncHelper::curl("https://yartransport.ru/map/",$post); 
        
      //  $stops = FuncHelper::curl("https://yartransport.ru/stop.json".$type_transport."/".$bus.".json",$post); 
         
      
        $array_bus=json_decode('[["1.","2.","2\u041a.","4.","4\u0410.","5.","6.","7.","8.","9.","10.","11.","12.","13.","14.","15.","17.","18.","18\u041a.","19.","19\u041a.","21.","21\u0411.","21\u0422.","22.","22\u0421.","23.","24.","25.","26.","27.","28.","29.","30.","32.","33.","34.","39.","40\u041a.","41.","41\u0410.","41\u0411.","42.","43.","44\u041a.","49.","53.","55.","56.","57.","58.","59.","62.","64.","65.","66.","68.","70.","76.","77.","79.","85.","90\u0421.","92.","93\u0413.","99\u0421."],["1","3","4","5","7","8","9"],["1","5","6","7"],["40","71","84","94","97"]]'); //var_dump($array_type_marsh); die();
     //  $array_bus=json_decode('[["85.","90\u0421.","92.","93\u0413.","99\u0421."],["1","3","4","5","7","8","9"],["1","5","6","7"],["40","71","84","94","97"]]'); //var_dump($array_type_marsh); die();
        $first_st=[];
        foreach ($array_bus as $type_marsh=>$type) {
            foreach ($type as $key223=>$bus) {
                $temp_id_marsh=$type_marsh.$bus;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $type_transport=$type_marsh+1;
                $num= str_replace('.', '', $bus);
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name='Маршрут '.$type_marsh.$bus;
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;
                
                $route->save();if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
                
                $stops = FuncHelper::curl("https://yartransport.ru/routex/".$type_transport."/".$bus.".json",$post); 
                $stops=json_decode($stops['content']);
                $stops=(array)$stops;
                //var_dump($stops);
                $iu=0;
                
                $marsh=$route->getMarshrut(); // ищем время от начальных остановок
              
                if (count($marsh)>1) {
                    $in_out=[];
                    $flag=true;
                    foreach ($marsh as $key=>$mar) {
                        if ($mar[2]=='in') {
                            $mr=FuncHelper::time_old_to_new($mar[1]);
                            $mar[1]=$mr[0];
                            $in_out[0][]=[$mar[0],$mar[1],$mr[2]];
                            if ($flag) { 
                                $route->type_day=$mr[1];
                                $flag=false;
                            }
                        }
                        if ($mar[2]=='out') {
                             $mr=FuncHelper::time_old_to_new($mar[1]);
                            $mar[1]=$mr[0];
                            $in_out[1][]=[$mar[0],$mar[1],$mr[2]];
                            if ($flag) { 
                                $route->type_day=$mr[1];
                                $flag=false;
                            }
                        }
                    }
                } else {
                    $in_out=false;
                }
                               
                for ($i=0;$i<2;$i++) { // $i туда или назад
                    foreach ($stops[($i+1)] as $key123 => $s) {
                        $stop = FuncHelper::curl("https://yartransport.ru/stop/".$s.".json",$post); 
                        $st=json_decode($stop['content']);
                        if (isset($st)) {
                            $station= Station::find()->where(['temp_id' => $st->id,'city_id'=>$city_id])->one();
                            if (!$station) {
                                 $station=new Station;
                                 $station->name=FuncHelper::refresh_name_station($st->name);
                                if ($key123==0) { $first_st[]=$station->name;}
                                $station->city_id=$city_id;
                                //$point=str_replace(["POINT (",")"],"",$st->location);
                                // $points=explode(" ", $point, $city_id);
                                $station->y=FuncHelper::helpcoord($st->coordinates[0]);
                                $station->x=FuncHelper::helpcoord($st->coordinates[1]);
                                $station->temp_id=$st->id;
                                $station->info='';
                                $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                              // var_dump($id_station_rout); die();
                                if ($in_out AND $in_out[$i][$key123][1]) {
                                    $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                    if (!$timework) {
                                        $timework=new TimeWork;
                                        $timework->station_rout_id=$id_station_rout;
                                    } 
                                    $timework->monday=$in_out[$i][$key123][1][0];
                                    $timework->tuesday=$in_out[$i][$key123][1][1];
                                    $timework->wednesday=$in_out[$i][$key123][1][2];
                                    $timework->thursday=$in_out[$i][$key123][1][3];
                                    $timework->friday=$in_out[$i][$key123][1][4];
                                    $timework->saturday=$in_out[$i][$key123][1][5];
                                    $timework->sunday=$in_out[$i][$key123][1][6];
                                    $timework->save();if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                                } 
                                $iu=$iu+10;
                            } else {
                                $station->name=FuncHelper::refresh_name_station($st->name);
                                 if ($key123==0) { $first_st[]=$station->name;}
                                $station->y=FuncHelper::helpcoord($st->coordinates[0]);
                                $station->x=FuncHelper::helpcoord($st->coordinates[1]);
                                $station->temp_id=$st->id;
                                $station->info='';
                                $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                                $station->refresh();
                                $id_station_rout=$route->getStationRout($station->id,$i);
                               // var_dump($id_station_rout); die();
                                if (!$id_station_rout) {
                                    $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                                }
                                
                                if ($in_out AND $in_out[$i][$key123][1]) {
                                    $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                    if (!$timework) {
                                        $timework=new TimeWork;
                                        $timework->station_rout_id=$id_station_rout;
                                    } 
                                    $timework->monday=$in_out[$i][$key123][1][0];
                                    $timework->tuesday=$in_out[$i][$key123][1][1];
                                    $timework->wednesday=$in_out[$i][$key123][1][2];
                                    $timework->thursday=$in_out[$i][$key123][1][3];
                                    $timework->friday=$in_out[$i][$key123][1][4];
                                    $timework->saturday=$in_out[$i][$key123][1][5];
                                    $timework->sunday=$in_out[$i][$key123][1][6];
                                    $timework->save();if (count($timework->errors)>0) { echo"timeworksave11-";print_r($timework->errors); }
                                } 
                            } 
                        }
                    }
                }
                
           //     var_dump($stops); die();
                //$route->name=$route_p->title;//$first_st." - ".$last_st;
              
               // var_dump($route);die();
                
                $ro = FuncHelper::curl("https://yartransport.ru/route/".$type_transport."/".$bus.".json",$post); 
                $ro=json_decode($ro['content'],true);
                $itog=[];
                foreach ($ro as $r) {
                    $itog[]=[FuncHelper::helpcoord($r[0]),FuncHelper::helpcoord($r[1])];
                }
                for ($key_d=0;$key_d<2;$key_d++) { // $key_d туда и назад. Одинаковый маршрут, так как нет разделения на маршрут туда и обратно, будет только туда
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$key_d])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$key_d;
                        $mapRoute->active=1;
                    }
                    $mapRoute->save();
                }
               // var_dump($first_st);
               $route->name=$first_st[0]." - ".$first_st[1];
                $first_st=[];
                $route->save();if (count($route->errors)>0) { echo"routesave55-"; print_r($route->errors); } //var_dump($route->errors); die();
                $route->refresh();
                echo "END - ".$route->number." - ".$route->name; echo PHP_EOL;
               // if ($key223>2) { die(); }
            }
        }
        
        die('good');
       
    }
        
    
     public function actionGobusOmsk() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-omsk

         
                 $post='';
        $url='http://bus-55.ru/api/rpc.php';
        
     
        $city_id[]=199;$ok_id[]='';
               
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=1000; // номера городских
        $type_direction_num[1]=50000; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            if ($key<18) { 
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
         
        $post='';
        $url='http://bus-55.ru/api/rpc.php';
        $city_id=199; //$ok_id=66401;
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post); 
        $sid= json_decode($sid);
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh); 
        //var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $type_transport=$tt+1;
            foreach ($marsh->routes as $route_p) { //if ($route_p->mr_num!='3') { continue; }
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."----------------------".PHP_EOL;
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                foreach ($route_info->result->races as $dest => $mar) { 
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                    
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=25;$day<32;$day++) {
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2022-07-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      //  var_dump($rasp_info); die();
                        
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                foreach ($ri->hours as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; $hour->hour=$hour->hour-24; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                if ($flag_color) {
                                    $ps_i='';
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                    $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                                }
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                    //var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                       // var_dump($st); die();
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) {
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
            }
        }
       
        die();
    }
    

   public function actionGobusKazan() { ////// СТАРОЕ !!! КОНСОЛЬ из папки yii:  yii carl/gobus-kazan
    
        $post='';
        $url='http://navi.kazantransport.ru/api/rpc.php';
          $city_id=111; //$ok_id=66401;
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post); 
        $sid= json_decode($sid);
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh); 
        //var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $type_transport=$tt+1;
            foreach ($marsh->routes as $route_p) { //if ($route_p->mr_num!='3') { continue; }
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."----------------------".PHP_EOL;
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                foreach ($route_info->result->races as $dest => $mar) { 
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                    
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=25;$day<32;$day++) {
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2022-07-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      //  var_dump($rasp_info); die();
                        
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                foreach ($ri->hours as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; $hour->hour=$hour->hour-24; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                if ($flag_color) {
                                    $ps_i='';
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                    $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                                }
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                    //var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                       // var_dump($st); die();
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) {
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
            }
        }
       
        die();
    }
    
    
    public function actionGobusEkater() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-ekater
    
        $post='';
        $url='http://xn--80axnakf7a.xn--80acgfbsl1azdqr.xn--p1ai/api/rpc.php';
          $city_id=93; //$ok_id=66401;
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post); 
        $sid= json_decode($sid);
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh); 
        //var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $type_transport=$tt+1;
            foreach ($marsh->routes as $route_p) { //if ($route_p->mr_num!='3') { continue; }
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."----------------------".PHP_EOL;
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                foreach ($route_info->result->races as $dest => $mar) { 
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                    
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=25;$day<32;$day++) {
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2022-07-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      //  var_dump($rasp_info); die();
                        
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                foreach ($ri->hours as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; $hour->hour=$hour->hour-24; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                if ($flag_color) {
                                    $ps_i='';
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                    $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                                }
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                    //var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                       // var_dump($st); die();
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) {
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
            }
        }
       
        die();
    }
    
    
     public function actionGobusPskov() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-pskov
    
        $post='';
        $url='http://navitrans.pskovbus.ru:8080/api/rpc.php';
        $city_id=213; $ok_id='';
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post); 
        $sid= json_decode($sid);
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh); 
        //var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $type_transport=$tt+1;
            foreach ($marsh->routes as $route_p) { //if ($route_p->mr_num!='3') { continue; }
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."----------------------".PHP_EOL;
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                foreach ($route_info->result->races as $dest => $mar) { 
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                    
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=25;$day<32;$day++) {
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2022-07-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      //  var_dump($rasp_info); die();
                        
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                foreach ($ri->hours as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                if ($flag_color) {
                                    $ps_i='';
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                    $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                                }
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                    //var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                       // var_dump($st); die();
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) {
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
            }
        }
       
        die();
    }
    
    public function actionGobusSmolen() { ///СТАРЫЙ ////// КОНСОЛЬ из папки yii:  yii carl/gobus-smolen
    
        $post='';
        $url='http://bus67.ru/api/rpc.php';
          $city_id=241; $ok_id=66401;// смоленск
      //  $city_id=486; $ok_id=66203501;//Велиж
     //  $city_id=219; $ok_id=66236501; // росславль
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post); 
        $sid= json_decode($sid);
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh); 
        //var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $type_transport=$tt+1;
            foreach ($marsh->routes as $route_p) { //if ($route_p->mr_num!='3') { continue; }
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."----------------------".PHP_EOL;
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                foreach ($route_info->result->races as $dest => $mar) { 
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                    
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=25;$day<32;$day++) {
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2022-07-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      //  var_dump($rasp_info); die();
                        
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                foreach ($ri->hours as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; $hour->hour=$hour->hour-24; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                if ($flag_color) {
                                    $ps_i='';
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                    $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                                }
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                    //var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                       // var_dump($st); die();
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) {
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
            }
        }
       
        die();
    }
    
    
    public function actionGobusKrasnoyarsk() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-Krasnoyarsk
    
        $post='';
        $url='https://mu-kgt.ru/informing/api/rpc.php';
        $city_id=138; $ok_id='';
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post); 
        $sid= json_decode($sid);
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh); 
        //var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $type_transport=$tt+1;
            foreach ($marsh->routes as $route_p) {// if ($route_p->mr_num!='2') { continue; }
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."----------------------".PHP_EOL;
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>1,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=1;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                foreach ($route_info->result->races as $dest => $mar) { 
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                    
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=1;$day<8;$day++) {
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2020-02-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                     //   var_dump($rasp_info); die();
                        
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                foreach ($ri->hoursl as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; $hour->hour=$hour->hour-24; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                foreach ($ri->hoursr as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) {
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[]=$m->minute."(".$col.")";
                                        } else { $min[]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { echo 'ERROR24='.$hour->hour.PHP_EOL; $hour->hour=$hour->hour-24; }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                if ($flag_color) {
                                    $ps_i='';
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                    $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                                }
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                    //var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                       // var_dump($st); die();
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) {
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if ($in_out[$st->st_id][$i]!=NULL) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout;
                                }
                                $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
            }
        }
       
        die();
    }
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
  
    
    /* НЕ ДОДЕЛАНА*/
  private function transnavig_yarosl($post,$url,$cid,$ok_id,$type_direction_num=false,$city_array=false,$array_temp_mr_id=[]) { //////////// поменять даты и прокси в функции!!!!!!!!!!!!!!!
       /* function mb_substr_replace($original, $replacement, $position, $length)
                    {
                        $startString = mb_substr($original, 0, $position, "UTF-8");
                        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");
                        $out = $startString . $replacement . $endString;
                        return $out;
                    }*/
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        $proxy_num=rand(0,3); // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
       
        $sid = FuncHelper::curlj($url,$post,$proxy_num,$cid);
              //  var_dump($sid); die();
        $sid= json_decode($sid); //var_dump($sid); die(); 
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        
         //t.data.method, t.data.id, t.data.params.sid
        $sha1n = sha1("startSession"."-"."1"."-" + $sid);
        $magici = substr($sha1n,0, 8)."-".substr($sha1n,8, 4)."-".substr($sha1n,12, 4)."-".substr($sha1n,24, 4)."-".substr($sha1n,28, 12);//m GET
        $magicStr=substr($sha1n,16, 8); //magic
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'","magic":"'.$magicStr.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url."?m=".$magici,$post_all_marsh,$proxy_num,$cid); 
        var_dump($all_marsh); die();
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            $proxy_num=rand(0,3);
            $type_transport=$tt+1;
         //   if ($type_transport!=3) { continue; } //////////// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            foreach ($marsh->routes as $route_p) { 
               // print_r($route_p->mr_num);
                //if ($route_p->mr_num!='1') { continue; } // проходим один из маршрутов
                $city_id=$cid;
                $charset = mb_detect_encoding($route_p->mr_num);
                $route_p->mr_num = iconv($charset, "UTF-8", $route_p->mr_num);
                
                if ($city_id==29) {
         
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."---------city_id=".$city_id."-------------".PHP_EOL;
                if (isset($array_temp_mr_id[$route_p->mr_id])) { // пропускаем, если этот маршрут уже добавлен в другом городе этого сайта
                    echo "----".$route_p->mr_num."--".$route_p->mr_id."--CONTIN".PHP_EOL;
                      continue;
                }
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post,$proxy_num,$cid); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route,$proxy_num,$cid); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
                $dubl=false;
                if ($city_array AND !$route) {
                    $caer=[];
                    foreach ($city_array as $carr) {
                        $caer[]="city_id=$carr";
                    }
                    $city_where=implode(" OR ", $caer);
                    $route= Route::find()->where("temp_route_id=$temp_id_marsh AND (".$city_where.")")->one();
                    $dubl=false;
                    if ($route) { echo "dubl | num=".$num." | temp_route_id=$temp_id_marsh".PHP_EOL; $dubl=true; }
                }
                if ($dubl) { continue; }
                if ($type_direction_num) {
                    if ($num<$type_direction_num[0]) {
                        $type_direction=1;
                    } elseif($num<$type_direction_num[1]) {
                        $type_direction=2;
                    } else {
                        $type_direction=3;
                    }
                } else {
                    $type_direction=1;
                }
               // var_dump($route); die();
                if (!$route) {
                    $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>$type_direction,'city_id'=>$city_id])->one();
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                $route->name=FuncHelper::refresh_title($route_p->mr_title);
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=$type_direction;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
                $route->deleteStationRout(); /// Удаляем все направления движения и время маршрута
                
                $array_temp_mr_id[$route_p->mr_id]=$route->name;
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                //var_dump($route_info->result->races); die();///////////////////////////////////////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                
                foreach ($route_info->result->races as $dest => $mar) { 
                    /*foreach ($route_info->result->races as $dest2 => $mar2) { 
                        if ($mar->stopList==$mar2->stopList) { $dest=$dest2; break; }
                    }*/
                   // var_dump($dest);/////////////////////////////////////////////////////////////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                   // if ($i>1) { break; } ////////////////////////// ЕСЛИ МНОГО НАПРАЛЕНИЙ В МАРШРУТЕ
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=16;$day<23;$day++) { // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post,$proxy_num,$cid); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2023-10-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp,$proxy_num,$cid); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      // var_dump($rasp_info); die();
                       // if (isset($rasp_info->result->rv_season)) { echo $rasp_info->result->rv_season.PHP_EOL; }
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                $hours_itog=[];
                                if (isset($ri->hours)) { $hours_itog=$ri->hours; } 
                                elseif (isset($ri->hoursl)) { $hours_itog=$ri->hoursl; if (isset($ri->hoursr))  {$hours_itog=array_merge($hours_itog,$ri->hoursr); } }//$hours_itog+$ri->hoursr;} }
                                elseif (isset($ri->hoursr)) { $hours_itog=$ri->hoursr;  }
                                foreach ($hours_itog as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                       
                                        /////////// ЕСЛИ заголовок равен маршруту в примечании (легенде), то не выводим такую легенду.
                                        
                                   //     if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l) AND $namsw!=$nosn AND $namsw2!=$nosn) { 
                                           if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) { 
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[$m->minute]=$m->minute."(".$col.")";
                                        } else { $min[$m->minute]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { 
                                       // echo 'ERROR24='.$hour->hour.PHP_EOL; 
                                        $hour->hour=$hour->hour-24; 
                                    }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $min=array_values($min);
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                $ps_i='';
                              //  var_dump($ri->ivals,$city_id); die();
                                if (isset($ri->ivals) && $city_id==138 && isset($ri->ivals[0])) { //Красноярск интервальные маршруты
                                    if ($ri->ivals[0]->ival1!=$ri->ivals[0]->ival2) {
                                        $ps_i.="c 6:00 до 9:00 интервал ".$ri->ivals[0]->ival1."-".$ri->ivals[0]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 6:00 до 9:00 интервал ".$ri->ivals[0]->ival1." минут<br>";
                                    }
                                    if ($ri->ivals[1]->ival1!=$ri->ivals[1]->ival2) {
                                        $ps_i.="c 9:00 до 16:30 интервал ".$ri->ivals[1]->ival1."-".$ri->ivals[1]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 9:00 до 16:30 интервал ".$ri->ivals[1]->ival1." минут<br>";
                                    }
                                    if ($ri->ivals[2]->ival1!=$ri->ivals[2]->ival2) {
                                        $ps_i.="c 16:30 до 19:00 интервал ".$ri->ivals[2]->ival1."-".$ri->ivals[2]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 16:30 до 19:00 интервал ".$ri->ivals[2]->ival1." минут<br>";
                                    }
                                    if ($ri->ivals[3]->ival1!=$ri->ivals[3]->ival2) {
                                        $ps_i.="c 19:00 до 21:00 интервал ".$ri->ivals[3]->ival1."-".$ri->ivals[3]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 19:00 до 21:00 интервал ".$ri->ivals[3]->ival1." минут<br>";
                                    }
                                }
                                if ($flag_color) {
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            }
                                        }
                                    }
                                   
                                    //var_dump($in_out); die();
                                } 
                                $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                //    var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                        
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) { //var_dump($station); die();
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if (isset($in_out[$st->st_id][$i])) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                              //  var_dump($timework); die();
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout; 
                                    $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                    $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                    $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                    $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                    $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                    $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                    $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                } else {
                                    echo "error 1258";
                                }
                               
                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if (isset($in_out[$st->st_id][$i])) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                               
                               if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout; 
                                    $timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                    $timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                    $timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                    $timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                    $timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                    $timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                    $timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);
                                } else {
                                    echo "error 1982";
                                }

                                $timework->save();
                               // var_dump($timework); die();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
             //   var_dump($route_p); die();
                /*busget*/
                $this->update_busget($route);
            }
        }
        return $array_temp_mr_id;
    }
  }
    
    private function transnavig($post,$url,$cid,$ok_id,$type_direction_num=false,$city_array=false,$array_temp_mr_id=[]) { //////////// поменять даты и прокси в функции!!!!!!!!!!!!!!!
       /* function mb_substr_replace($original, $replacement, $position, $length)
                    {
                        $startString = mb_substr($original, 0, $position, "UTF-8");
                        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");
                        $out = $startString . $replacement . $endString;
                        return $out;
                    }*/
        $color_l=[];
        foreach ($this->color as $rr=>$cc) {
            $color_l[]=$rr;
        }
     /*     jsonrpc: "2.0",
            method: "getRoute",
            params: {
                sid: c.sid,
                mr_id: a
            },
            id: b*/
        $proxy_num=rand(0,3); // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj($url,$post,$proxy_num,$cid);
              //  var_dump($sid); die();
        $sid= json_decode($sid); //var_dump($sid); die(); 
        $sid=$sid->result->sid;//var_dump($sid); die();
        
        $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj($url,$post_all_marsh,$proxy_num,$cid); 
        
        $all_marsh= json_decode($all_marsh);
        foreach ($all_marsh->result as $tt => $marsh) { // $marsh - объекты по типам маршрутов - автобус - троллейбус - трамвай | $type_transport совпадает с нашей нумерацией
            
            $type_transport=$tt+1;
         //   if ($type_transport!=3) { continue; } //////////// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            foreach ($marsh->routes as $route_p) {
                $flag_busget=true;
                $proxy_num=rand(0,3);
               // print_r($route_p->mr_num);
              //  if ($route_p->mr_num!='102') { continue; } // проходим один из маршрутов
               // if ($route_p->mr_num < 110) { continue; } // проходим выбраный интервал маршрутов
                $city_id=$cid;
                $charset = mb_detect_encoding($route_p->mr_num);
                $route_p->mr_num = iconv($charset, "UTF-8", $route_p->mr_num);
                
                if ($city_id==29) { if (is_numeric($route_p->mr_num) AND $route_p->mr_num>0 AND $route_p->mr_num<200) {   } else { continue; } } //АРХАНГЕЛЬСК
                if ($city_id==269) { //ФЕОДОСИЯ
                    $pos = strpos($route_p->mr_num, 'ФД');
                    if ($pos === false) { continue; } else {
                        $route_p->mr_num=str_replace('ФД','',$route_p->mr_num);
                    }
                } 
               /* if ($city_id==182) { // Нижний новгород
                    var_dump(mb_substr($route_p->mr_num, 0, 1 ),mb_substr_replace($route_p->mr_num,"",0,1)); //die();
                    if (mb_substr($route_p->mr_num, 0, 1 ) === "Г") {
                        $city_id=80; // городец
                        $route_p->mr_num = mb_substr_replace($route_p->mr_num, "",0,1);
                    } elseif (mb_substr($route_p->mr_num, 0, 2 ) === "БХ") { //Балахна
                        $city_id=34; 
                        $route_p->mr_num = mb_substr_replace($route_p->mr_num, "",0,2);
                    }
                    elseif (mb_substr($route_p->mr_num, 0, 1 ) === "Д") { //Дзержинск
                         $city_id=83; 
                         $route_p->mr_num = mb_substr_replace($route_p->mr_num, "",0,1);
                    }
                    elseif (mb_substr($route_p->mr_num, 0, 2 ) === "БГ") { //Богородск
                         $city_id=289; 
                         $route_p->mr_num = mb_substr_replace($route_p->mr_num, "",0,2);
                    }
                    elseif (mb_substr($route_p->mr_num, 0,1 ) === "Б") { //Бор
                         $city_id=44; 
                         $route_p->mr_num = mb_substr_replace($route_p->mr_num, "",0,1);
                    }
                    elseif (mb_substr($route_p->mr_num, 0,1 ) === "К") { //Кстово
                         $city_id=141; 
                         $route_p->mr_num = mb_substr_replace($route_p->mr_num, "",0,1);
                    }
                    $route_p->mr_num= trim($route_p->mr_num);
                }*/
                echo "---------------------".$route_p->mr_num."---type=".$type_transport."---------city_id=".$city_id."-------------".PHP_EOL;
                if (isset($array_temp_mr_id[$route_p->mr_id])) { // пропускаем, если этот маршрут уже добавлен в другом городе этого сайта
                    echo "----".$route_p->mr_num."--".$route_p->mr_id."--CONTIN".PHP_EOL;
                      continue;
                }
                $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                $sid = FuncHelper::curlj($url,$post,$proxy_num,$cid); 
                if (!$sid) { echo "---ERROR1--".PHP_EOL; continue;}
                $sid= json_decode($sid);
                $sid=$sid->result->sid;//var_dump($sid); die();
                $post_route='{"jsonrpc": "2.0","method": "getRoute","params": {"sid": "'.$sid.'", "mr_id": "'.$route_p->mr_id.'" }, "id": 0}';
                $route_info = FuncHelper::curlj($url,$post_route,$proxy_num,$cid); 
                if (!$route_info) { echo "---ERROR2--".PHP_EOL;continue;}
                $route_info= json_decode($route_info);  // информация о маршруте - races:"stopList" - 
              //  var_dump($route_info); die();
                
                $temp_id_marsh=$route_p->mr_id;
                $route= Route::find()->where(['temp_route_id' =>$temp_id_marsh,'city_id'=>$city_id])->one();
                $num=$route_p->mr_num;
                $dubl=false;
                if ($city_array AND !$route) {
                    $caer=[];
                    foreach ($city_array as $carr) {
                        $caer[]="city_id=$carr";
                    }
                    $city_where=implode(" OR ", $caer);
                    $route= Route::find()->where("temp_route_id=$temp_id_marsh AND (".$city_where.")")->one();
                    $dubl=false;
                    if ($route) { echo "dubl | num=".$num." | temp_route_id=$temp_id_marsh".PHP_EOL; $dubl=true; }
                }
                if ($dubl) { continue; }
                $num_bez_bukv = preg_replace('/[^0-9]+/', '', $num);
                if ($type_direction_num) {
                    if ($num_bez_bukv<$type_direction_num[0]) {
                        $type_direction=1;
                    } elseif($num_bez_bukv<$type_direction_num[1]) {
                        $type_direction=2;
                    } else {
                        $type_direction=3;
                    }
                } else {
                    $type_direction=1;
                }
                if ($type_transport!=1) { // если не автобус то только город (трамвай-троллебйс только в городе же?)
                    $type_direction=1;
                }
               // var_dump($route); die();
                if (!$route) {
                    
                    if ($num_bez_bukv<300) {
                        // если номер автобуса меньше 300, то не учитываем направление
                        $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'city_id'=>$city_id])->one();
                    } else {
                        $route=Route::find()->where(['number'=>$num,'type_transport' => $type_transport,'type_direction'=>$type_direction,'city_id'=>$city_id])->one();
                    }
                    if ($route) { // ОБНОВЛЯЕМ
                        $route->time_work='';                        
                    } else {
                        $route=new Route;                  //  var_dump($route); die()
                        $route->alias='none';
                        $route->id=0;
                        $route->name= FuncHelper::refresh_title($route_p->mr_title);
                    }
                }
                $route->name=FuncHelper::refresh_title($route_p->mr_title);
                $route->number=$num;
                $route->city_id=$city_id;
                $route->type_transport=$type_transport;
                $route->type_direction=$type_direction;
                $route->active=1;
                $route->type_day=2;
                $route->temp_route_id=$temp_id_marsh;
                $route->version=1;//echo"routesave1111-"; print_r($route);
                $route->save(false); if (count($route->errors)>0) { echo"routesave1111-"; print_r($route->errors); }
                $route->refresh();
                $route->deleteStationRout(); /// Удаляем все направления движения и время маршрута
                
                $array_temp_mr_id[$route_p->mr_id]=$route->name;
               // echo"3333333333333333311-"; print_r($route); die();
                $first_st=[];
                //var_dump($route_info->result->races); die();///////////////////////////////////////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                
                foreach ($route_info->result->races as $dest => $mar) { 
                    /*foreach ($route_info->result->races as $dest2 => $mar2) { 
                        if ($mar->stopList==$mar2->stopList) { $dest=$dest2; break; }
                    }*/
                   // var_dump($dest);/////////////////////////////////////////////////////////////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    $i=$dest; // 0-туда 1-назад 2-другой маршрут 3-другой маршрут .....
                   // if ($i>1) { break; } ////////////////////////// ЕСЛИ МНОГО НАПРАЛЕНИЙ В МАРШРУТЕ
                    ////////////// линия на карте
                    $itog=[];
                    foreach ($mar->coordList as $coord) {
                            $itog[]=[FuncHelper::helpcoord($coord->rd_lat),FuncHelper::helpcoord($coord->rd_long)];
                    }    
                   // var_dump($itog);
                    $mapRoute= MapRout::find()->where(['route_id' => $route->id,'direction'=>$i])->one();
                    if ($mapRoute) {
                        $mapRoute->line=json_encode($itog);
                    } else {
                        $mapRoute=new MapRout;
                        $mapRoute->route_id=$route->id;
                        $mapRoute->line=json_encode($itog);
                        $mapRoute->direction=$i;
                        $mapRoute->active=1; 
                    }
                    $mapRoute->save(); if (count($mapRoute->errors)>0) { echo"routesave_mapsave-"; print_r($mapRoute->errors); }
                   // die();
                    ///////////////////////////////////////////
                    
                    $di=0;
                    $days_week=['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $in_out=[];
                    for ($day=23;$day<30;$day++) { // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
                        $sid = FuncHelper::curlj($url,$post,$proxy_num,$cid); 
                        if (!$sid) { echo "---ERROR3--".PHP_EOL; continue;}
                        $sid= json_decode($sid);
                        $sid=$sid->result->sid;//var_dump($sid); die();
                
                        $post_rasp='{"jsonrpc": "2.0","method": "getRaspisanie","params": {"sid": "'.$sid.'",'
                            . '"mr_id": "'.$route_p->mr_id.'","data": "2023-10-'.$day.'","rl_racetype": "'.$mar->rl_racetype.'","rc_kkp": "","st_id": 0 }, "id": 0}';
                        $rasp_info = FuncHelper::curlj($url,$post_rasp,$proxy_num,$cid); 
                        if (!$rasp_info) { echo "---ERROR4--".PHP_EOL; continue;}
                        $rasp_info= json_decode($rasp_info);
                      // var_dump($rasp_info); die();
                       // if (isset($rasp_info->result->rv_season)) { echo $rasp_info->result->rv_season.PHP_EOL; }
                        if (isset($rasp_info->result->stopList)) {
                           
                            foreach ($rasp_info->result->stopList as $keysl=>$ri) {
                                
                                $flag_color=0;
                                $f_color=[];
                                $hours_itog=[];
                                if (isset($ri->hours)) { $hours_itog=$ri->hours; } 
                                elseif (isset($ri->hoursl)) { $hours_itog=$ri->hoursl; if (isset($ri->hoursr))  {$hours_itog=array_merge($hours_itog,$ri->hoursr); } }//$hours_itog+$ri->hoursr;} }
                                elseif (isset($ri->hoursr)) { $hours_itog=$ri->hoursr;  }
                                foreach ($hours_itog as $hour) {
                                    $min=[];
                                    foreach ($hour->minutes as $m) {
                                        /* ////////////////////////// ЕСЛИ МНОГО НАПРАЛЕНИЙ В МАРШРУТЕ
                                        $namsw=$rasp_info->result->races[$dest]->rl_firststation." - ".$rasp_info->result->races[$dest]->rl_laststation;
                                        $namsw=str_replace(' ', '', $namsw);
                                        $namsw2=$rasp_info->result->races[$dest]->rl_laststation." - ".$rasp_info->result->races[$dest]->rl_firststation;
                                        $namsw2=str_replace(' ', '', $namsw2);
                                        foreach ($rasp_info->result->races as $eee) {
                                            //echo PHP_EOL."-------------".$m->rl_racetype."-------------";
                                            if ($eee->rl_racetype==$m->rl_racetype) {
                                               /* echo PHP_EOL."+++++++++++++".$eee->rl_laststation." - ".$eee->rl_firststation;
                                                echo PHP_EOL."!!!!!!!!!!!".$namsw;
                                                $nosn=str_replace(' ', '', $eee->rl_laststation." - ".$eee->rl_firststation);
                                            }
                                        }
                                        */
                                        /////////// ЕСЛИ заголовок равен маршруту в примечании (легенде), то не выводим такую легенду.
                                        
                                   //     if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l) AND $namsw!=$nosn AND $namsw2!=$nosn) { 
                                           if ($m->rl_racetype!=$mar->rl_racetype AND in_array($m->rl_racetype,$color_l)) { 
                                            /* var_dump($this->color); var_dump($m->rl_racetype);*/ 
                                            $col=$this->color[$m->rl_racetype][0]; 
                                            $f_color[$m->rl_racetype]=1;$flag_color=1; 
                                            $min[$m->minute]=$m->minute."(".$col.")";
                                        } else { $min[$m->minute]=$m->minute; }
                                        
                                    }
                                    if ($hour->hour>24) { 
                                       // echo 'ERROR24='.$hour->hour.PHP_EOL; 
                                        $hour->hour=$hour->hour-24; 
                                    }
                                    if ($hour->hour=='24') { $hour_norm='00'; } else { $hour_norm=$hour->hour; }
                                    $min=array_values($min);
                                    $in_out[$ri->st_id][$i][$days_week[$di]][]=[$hour_norm.'',$min];
                                }
                                $ps_i='';
                              //  var_dump($ri->ivals,$city_id); die();
                                if (isset($ri->ivals) && $city_id==138 && isset($ri->ivals[0])) { //Красноярск интервальные маршруты
                                    if ($ri->ivals[0]->ival1!=$ri->ivals[0]->ival2) {
                                        $ps_i.="c 6:00 до 9:00 интервал ".$ri->ivals[0]->ival1."-".$ri->ivals[0]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 6:00 до 9:00 интервал ".$ri->ivals[0]->ival1." минут<br>";
                                    }
                                    if ($ri->ivals[1]->ival1!=$ri->ivals[1]->ival2) {
                                        $ps_i.="c 9:00 до 16:30 интервал ".$ri->ivals[1]->ival1."-".$ri->ivals[1]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 9:00 до 16:30 интервал ".$ri->ivals[1]->ival1." минут<br>";
                                    }
                                    if ($ri->ivals[2]->ival1!=$ri->ivals[2]->ival2) {
                                        $ps_i.="c 16:30 до 19:00 интервал ".$ri->ivals[2]->ival1."-".$ri->ivals[2]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 16:30 до 19:00 интервал ".$ri->ivals[2]->ival1." минут<br>";
                                    }
                                    if ($ri->ivals[3]->ival1!=$ri->ivals[3]->ival2) {
                                        $ps_i.="c 19:00 до 21:00 интервал ".$ri->ivals[3]->ival1."-".$ri->ivals[3]->ival2." минут<br>";
                                    } else {
                                        $ps_i.="c 19:00 до 21:00 интервал ".$ri->ivals[3]->ival1." минут<br>";
                                    }
                                }
                                if ($flag_color) {
                                    $rri=0;
                                    foreach ($f_color as $flk=>$rcs) {
                                        foreach ($rasp_info->result->races as $re) {
                                            if ($re->rl_racetype==$flk) {
                                                
                                                $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color[$re->rl_racetype][0].';" class="div-text">'.$this->color[$re->rl_racetype][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.FuncHelper::refresh_name_station($re->rl_firststation).' - '.FuncHelper::refresh_name_station($re->rl_laststation).'</div>'; 
                                                
                                            /*    $ps_i.='<div>'
                                                    . '<strong>'
                                                    . '<div style="color: '.$this->color2[$rri][0].';" class="div-text">'.$this->color2[$rri][1].' минуты</div>:'
                                                    . '</strong> автобус следует рейсом '.$re->rl_firststation.' - '.$re->rl_laststation.'</div>';
                                                $rri++;*/
                                            }
                                        }
                                    }
                                   
                                    //var_dump($in_out); die();
                                } 
                                $in_out[$ri->st_id][$i][$days_week[$di]]['ps']=[$ps_i];
                            }
                        } else {
                           // var_dump($rasp_info);
                        }
                        $di++;
                    }
                //    var_dump($in_out); die();
                    $key123=0;
                    $iu=10;
                    foreach ($mar->stopList as $st) {
                        
                        $station= Station::find()->where(['temp_id' => $st->st_id,'city_id'=>$city_id])->one();
                        if (!$station) { //var_dump($station); die();
                             $station=new Station;
                             $station->name=FuncHelper::refresh_name_station($st->st_title);
                            if ($key123==0) { $first_st[]=$station->name;}
                            $station->city_id=$city_id;
                            //$point=str_replace(["POINT (",")"],"",$st->location);
                            // $points=explode(" ", $point, $city_id);
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->alias='none';
                            $station->save(); if (count($station->errors)>0) { echo"stationsave11-"; print_r($station->errors); }
                            $id_station_rout=$route->setStationRout($station->id,$i,$iu,'');
                             
                            if (isset($in_out[$st->st_id][$i])) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                              //  var_dump($timework); die();
                                if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout; 
                                    @$timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                    @$timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                    @$timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                    @$timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                    @$timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                    @$timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                    @$timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);

                                } else {
                                    echo "error 1258";
                                    /*$timework->monday=FuncHelper::time_vmeste(json_decode($timework->monday, true),$in_out[$st->st_id][$i]['monday']);
                                    $timework->tuesday=FuncHelper::time_vmeste(json_decode($timework->tuesday, true),$in_out[$st->st_id][$i]['tuesday']);
                                    $timework->wednesday=FuncHelper::time_vmeste(json_decode($timework->wednesday, true),$in_out[$st->st_id][$i]['wednesday']);
                                    $timework->thursday=FuncHelper::time_vmeste(json_decode($timework->thursday, true),$in_out[$st->st_id][$i]['thursday']);
                                    $timework->friday=FuncHelper::time_vmeste(json_decode($timework->friday, true),$in_out[$st->st_id][$i]['friday']);
                                    $timework->saturday=FuncHelper::time_vmeste(json_decode($timework->saturday, true),$in_out[$st->st_id][$i]['saturday']);
                                    $timework->sunday=FuncHelper::time_vmeste(json_decode($timework->sunday, true),$in_out[$st->st_id][$i]['sunday']);*/
                                }
                               
                                $timework->save();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        } else {
                            $station->name=FuncHelper::refresh_name_station($st->st_title);
                             if ($key123==0) { $first_st[]=$station->name;}
                            $station->y=FuncHelper::helpcoord($st->st_lat);
                            $station->x=FuncHelper::helpcoord($st->st_long);
                            $station->temp_id=$st->st_id;
                            $station->info='';
                            $station->save();if (count($station->errors)>0) { echo"stationsave22-"; print_r($station->errors); }
                            $station->refresh();
                            $id_station_rout=$route->getStationRout($station->id,$i);
                           // var_dump($id_station_rout); die();
                            if (!$id_station_rout) {
                                $id_station_rout=$route->setStationRout($station->id,$i,$iu);
                            }
                            if (isset($in_out[$st->st_id][$i])) {
                                $timework= TimeWork::find()->where(['station_rout_id' => $id_station_rout])->one(); 
                               
                               if (!$timework) {
                                    $timework=new TimeWork;
                                    $timework->station_rout_id=$id_station_rout; 
                                    @$timework->monday= json_encode($in_out[$st->st_id][$i]['monday']);
                                    @$timework->tuesday= json_encode($in_out[$st->st_id][$i]['tuesday']);
                                    @$timework->wednesday= json_encode($in_out[$st->st_id][$i]['wednesday']);
                                    @$timework->thursday= json_encode($in_out[$st->st_id][$i]['thursday']);
                                    @$timework->friday= json_encode($in_out[$st->st_id][$i]['friday']);
                                    @$timework->saturday= json_encode($in_out[$st->st_id][$i]['saturday']);
                                    @$timework->sunday= json_encode($in_out[$st->st_id][$i]['sunday']);
  //var_dump($timework); die();
                                } else {
                                    echo "error 1982";
                                    $flag_busget=false;
                                    //var_dump($timework['monday']);
                                  /*  $timework->monday=FuncHelper::time_vmeste(json_decode($timework['monday'], true),$in_out[$st->st_id][$i]['monday']);
                                    $timework->tuesday=FuncHelper::time_vmeste(json_decode($timework['tuesday'], true),$in_out[$st->st_id][$i]['tuesday']);
                                    $timework->wednesday=FuncHelper::time_vmeste(json_decode($timework['wednesday'], true),$in_out[$st->st_id][$i]['wednesday']);
                                    $timework->thursday=FuncHelper::time_vmeste(json_decode($timework['thursday'], true),$in_out[$st->st_id][$i]['thursday']);
                                    $timework->friday=FuncHelper::time_vmeste(json_decode($timework['friday'], true),$in_out[$st->st_id][$i]['friday']);
                                    $timework->saturday=FuncHelper::time_vmeste(json_decode($timework['saturday'], true),$in_out[$st->st_id][$i]['saturday']);
                                    $timework->sunday=FuncHelper::time_vmeste(json_decode($timework['sunday'], true),$in_out[$st->st_id][$i]['sunday']);*/
                                }

                                $timework->save();
                               // var_dump($timework); die();
                                if (count($timework->errors)>0) { echo"timeworksave00-"; print_r($timework->errors); }
                            }
                        }
                        $iu=$iu+10;                    
                    }
                    
                   // var_dump($rasp_info); die();
                }
               // var_dump($route); die();
                if ($flag_busget) {
                    /*busget*/
                    $this->update_busget($route);
                }
            }
        }
        return $array_temp_mr_id;
    }

    public function actionGobusKemer() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-kemer /// Кемерово и область
        error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://bus42.info/navi/api/rpc.php';
        /*
         * 
        
        37=>95,455=>109,293=>242,1165=>1134,305=>428,388=>530,
        375=>574,8=>584, 809=>617,13=>671,212=>787,872=>1135,
        1166=>1136,1029=>944,1036=>953,1045=>970,1167=>1137,281=>1103,
        1164=>1132,410=>42,1169=>1138

         *          */
        /* $city_id[0]=3;  // Кемерово+
        $ok_id[0]=32401;
        */
       /* $city_id[0]=37;  // Белово+
        $ok_id[0]=32407;*/
        /*
        $city_id[1]=455;  // Берёзовский+
        $ok_id[1]=32410;*/
        /*
        $city_id[2]=293;  // Гурьевск+
        $ok_id[2]=32413;
       
        $city_id[3]=1165;  // Ижморский+
        $ok_id[3]=32204;
          *//*
        $city_id[4]=305;  // Киселёвск+
        $ok_id[4]=32416;*/
        /*
        $city_id[18]=388;  // ленинск-кузнецк+
        $ok_id[18]=32419;*/
        /*
        $city_id[5]=375;  // Мариинск+
        $ok_id[5]=32422;
        
        $city_id[6]=8;  // Междуреченск+
        $ok_id[6]=32425;
        
        $city_id[7]=809;  // Мыски+
        $ok_id[7]=32428;*/
        
      /*  $city_id[8]=13;  // Новокузнецк+
        $ok_id[8]=32431;
        
        $city_id[9]=212;  // Прокопьевск+
        $ok_id[9]=32437;
        */
        $city_id[10]=872;  // Осинники+
        $ok_id[10]=32434;
        
       /* $city_id[11]=1166;  // Промышленная+
        $ok_id[11]=32225;*/
        /*
        $city_id[12]=1029;  // Тайга+
        $ok_id[12]=32440;
        
        
        $city_id[13]=1036;  // Таштагол+
        $ok_id[13]=32443;
        
        $city_id[14]=1045;  // Топки+
        $ok_id[14]=32446;
        
        $city_id[15]=1167;  // Тяжинский+
        $ok_id[15]=32234;
        
        $city_id[16]=281;  // Юрга+
        $ok_id[16]=32449;
        
        
        $city_id[17]=1164;  // Яшкино+
        $ok_id[17]=32246;
        
      */
/*
        $city_id[19]=410;  // Анжеро-Судженск+
        $ok_id[19]=32404;
        */
       /*
        $city_id[20]=1169;  // Крапивинский
        $ok_id[20]=32210;
        
        
        */
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=300; // пригородных
        $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
          //  if ($key<3) { 
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
           // }
        }
       
        die();
    }
    
    
    public function actionGobusVolgograd() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-Volgograd /// Волгоград и область

        $post='';
        $url='http://transport.volganet.ru/api/rpc.php';
        
        
        $city_id=[];  
        $ok_id=[];
        
        
        
        $city_id[]=64;  // Волжский
        $ok_id[]=18410;
        
        $city_id[]=115;  //Камышин 18415
        $ok_id[]=18415; //
        
        $city_id[]=797;  //		Михайловка
        $ok_id[]=18420; //
        
        $city_id[]=1071;  //			Урюпинск
        $ok_id[]=18254800; //
        
        $city_id[]=1071;  //			Урюпинск
        $ok_id[]=18254; //
        
          $city_id[]=1085;  //			1085	Фролово
        $ok_id[]=18428; //
        
        $city_id[]=1085;  //			1085	Фролово
        $ok_id[]=18256; //
        
        $city_id[]=1085;  //			1085	Фролово
        $ok_id[]=18256800; //
        
        $city_id[]=624;  //	Калач-на-Дону
        $ok_id[]=18216501; //
        
        $city_id[]=697;  //	Котово
        $ok_id[]=18226501; //
        
        $city_id[]=694;  //	Котельниково
        $ok_id[]=18224501; //
        
        $city_id[]=1020;  //	Суровикино
        $ok_id[]=18253501; //
        
        $city_id[]=708;  //	Краснослободск
        $ok_id[]=18251507; //
        
        $city_id[]=837;  //	Новоаннинский
        $ok_id[]=18238501; //
        
        $city_id[]=581;  //	Жирновск
        $ok_id[]=18212501; //
        
        $city_id[]=565;  //	Дубовка
        $ok_id[]=18208501; //
        
        $city_id[]=830;  //	Николаевск
        $ok_id[]=18236501; //
        
        $city_id[]=891;  //	Петров Вал
        $ok_id[]=18218503; //
        
        $city_id[]=968;  //	968	Серафимович
        $ok_id[]=18250501; //
        
        
        $city_id[]=61;  // 	Волгоград
        $ok_id[]='';
        	
        	
        
        $type_direction_num=false;
        $type_direction_num[0]=100; // номера городских
       $type_direction_num[1]=1000; // пригородных
        $type_direction_num[2]=2000; // номера межгород*/
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<180) { 
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id);
            }
        }
       
        die();
    }
    
    public function actionGobusBelgorod() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-kemer /// Кемерово и область

        $post='';
        $url='https://maps.etk31.ru/api/rpc.php';
        
     
        $city_id[]=5;$ok_id[]='';
               
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=500; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<18) { 
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function actionGobusKirov() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-kirov 
        $post='';
        $url='https://businfo.cdsvyatka.com/api/rpc.php';
        
     
        $city_id[]=9;$ok_id[]='';
               
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=500; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
        public function actionGobusKrym() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-krym /// крым
//error_reporting(E_ALL ^ E_NOTICE);
       //     error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='http://businfo82.ru/api/rpc.php';
        
        
        $city_id=[];  
        $ok_id=[];
        
        
       /*
        $city_id[]=20;  // Алушта
        $ok_id[]=35403;
        
        $city_id[]=440;  //Бахчисарай
        $ok_id[]=35204501; //
        
        $city_id[]=440;  //		Бахчисарай район
        $ok_id[]=35204; //
        
        $city_id[]=1171;  //			Белогорск
        $ok_id[]=35207501; //
        
        $city_id[]=1171;  //			Белогорск район
        $ok_id[]=35207; //
        
          $city_id[]=120;  //			Керчь
        $ok_id[]=35412; //
        
        $city_id[]=407;  //			Алупка
        $ok_id[]=35; //
        
        $city_id[]=225;  //			Саки
        $ok_id[]=35414; //
        */
    /*   
        */
        $city_id[]=239;  //	Симфиропаль
        $ok_id[]=35401; //
        
        $city_id[]=239;  //	Симфиропаль район
        $ok_id[]=35247; //
       
        $city_id[]=283;  //	Ялта
        $ok_id[]=35419;
        
        /* 
        $city_id[]=234;  //	Севастополь
        $ok_id[]=67; //
        $city_id[]=269;  //	Феодосия 1021-busget 
        $ok_id[]='';
        */
        $type_direction_num=false;
       /* $type_direction_num[0]=100; // номера городских
       $type_direction_num[1]=1000; // пригородных
        $type_direction_num[2]=2000; // номера межгород*/
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
            $this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id);
        }
       
        die();
    }
    
    
        public function actionGobusArh() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-arh  Архангельск
        $post='';
        $url='https://bus.arhtc.ru/api/rpc.php';
        
     
        $city_id[]=29;$ok_id[]='';
             
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=200; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();https://mu-kgt.ru/informing/api/rpc.php
    }
    
    public function actionGobusKrasnoyar() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-krasnoyar  Красноярск
        $post='';
        $url='https://mu-kgt.ru/informing/api/rpc.php';
        
     
        $city_id[]=138;$ok_id[]='';
             
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=200; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function actionGobusKrasnoyarprigor() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-krasnoyarprigor  Красноярск пригородные
         error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/regions/api/rpc.php';
        
     
        $city_id[]=138;$ok_id[]='4400';
             
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=0; // номера городских
        $type_direction_num[1]=200; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
           // $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function actionGobusAchinsk() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-achinsk  Ачинск
            error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/regions/api/rpc.php';
        
     
        $city_id[]=32;$ok_id[]='4403';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=200; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) { 
         //   $this->editversion($cid); !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
        public function actionGobusZhelezn() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-zhelezn  Железногорск красноярск кр
            error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/zheleznogorsk/api/rpc.php';
        
     
        $city_id[]=295;$ok_id[]='';
              
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=600; // номера городских
        $type_direction_num[1]=1000; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function actionGobusZelenogor() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-zelenogor  Зеленогорск красноярск кр
           error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/regions/api/rpc.php';
        
     
        $city_id[]=596;$ok_id[]='4537';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=600; // номера городских
        $type_direction_num[1]=1000; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function editversion($city_id) {
        echo $city_id.PHP_EOL;
        $routes=Route::find()->where("city_id=".$city_id)->all();
        foreach ($routes as $r) {
            if ($r->version==1) {
                $r->version=4;
            } elseif ($r->version==3) {
                $r->version=2;
            }
            $r->save();
        }
        $city= City::findOne(['id'=>$city_id]);
      //  var_dump($city); die();
        $data=date("Y-m-d H:i:s");
        echo $city->name." - ".$data.PHP_EOL;
        $city->lastmod = $data;
        $city->save();
        return true;
    }
    
    public function actionGobusKansk() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-kansk  Канск красноярск кр
        error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/regions/api/rpc.php';
        
     
        $city_id[]=117;$ok_id[]='4420';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=600; // номера городских
        $type_direction_num[1]=1000; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function actionGobusSharyp() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-sharyp  шарыпово красноярск кр
        error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/regions/api/rpc.php';
        
     
        $city_id[]=1124;$ok_id[]='4440';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=600; // номера городских
        $type_direction_num[1]=1000; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
    public function actionGobusUyar() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-uyar  Уяр красноярск кр
         error_reporting(E_ERROR | E_PARSE);
        $post='';
        $url='https://mu-kgt.ru/regions/api/rpc.php';
        
     
        $city_id[]=1082;$ok_id[]='4257';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=600; // номера городских
        $type_direction_num[1]=1000; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
    }
    
       public function actionGobusSmolen2() { // КОНСОЛЬ из папки yii:  yii carl/gobus-smolen2
    
   //     $post='';
     //   $url='http://bus67.ru/api/rpc.php';
        //  $city_id=241; $ok_id=66401;// смоленск
      //  $city_id=486; $ok_id=66203501;//Велиж
     //  $city_id=219; $ok_id=66236501; // росславль
       
           $post='';
        $url='http://bus67.ru/api/rpc.php';
        
     
    /*    $city_id[]=241;$ok_id[]='66401';
        $array_temp_mr_id=[];*/
        
        $city_id[]=219;$ok_id[]='66236501';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=100; // номера городских
        $type_direction_num[1]=500; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
       }
       
       public function actionGobusKazan2() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-kazan2
    
           error_reporting(0);
        $post='';
        $url='https://navi.kazantransport.ru/api/rpc.php';
        
        $city_id[]=111;$ok_id[]='';
        $array_temp_mr_id=[];
        
        
        $type_direction_num[0]=150; // номера городских
        $type_direction_num[1]=500; // пригородных
       // $type_direction_num[2]=500; // номера межгород
        foreach ($city_id as $key => $cid) {
            $this->editversion($cid);
            if ($key<2) {
                echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }
        }
       
        die();
        
       }
       
        public function actionGobusNiznnovg() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-niznnovg Нижний новгород
    
          
            $post='';
            $url='http://asip.cds-nnov.ru/api/rpc.php';

          //  $city_id[]=182;$ok_id[]='22401'; // ниж новгород
          //  $city_id[]=26;$ok_id[]='22403'; // арзамас
            
        //    $city_id[]=34;$ok_id[]='22205501'; // балахна*/
            $city_id[]=289;$ok_id[]='22207501'; // богородск
            $city_id[]=44;$ok_id[]='22412'; // бор
            
            $city_id[]=80;$ok_id[]='22228501'; // городец
            $city_id[]=83;$ok_id[]='22421'; // дзержинск
            
            $city_id[]=141;$ok_id[]='22237501'; // кстово
            
            
            $array_temp_mr_id=[];


            $type_direction_num[0]=100; // номера городских
            $type_direction_num[1]=1000; // пригородных
           // $type_direction_num[2]=500; // номера межгород
            foreach ($city_id as $key => $cid) {
                $this->editversion($cid);
                
                    echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                    $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }

            die();
        
        }
       
        public function actionGobusUfa() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-ufa Башкортостан Уфа
    
          
            $post='';
            $url='https://transportrb.ru/api/rpc.php';

            $city_id[]=267;$ok_id[]='80401'; // уфа
           // $array_temp_mr_id=[];

            $city_id[]=245;$ok_id[]='80445'; // стерлитамак 67
            $city_id[]=226;$ok_id[]='80439'; // салават 113
            $city_id[]=177;$ok_id[]='80427'; // нефтекамск 134*/
            
         /*   $city_id[]=862;$ok_id[]='80435'; // октябрьский 148
            $city_id[]=451;$ok_id[]='80410'; // белорецк 230 
            $city_id[]=109;$ok_id[]='80420'; // ишимбай 238
            $city_id[]=1052;$ok_id[]='80450'; // туймазы 235
            $city_id[]=971;$ok_id[]='80443'; // сибай 256
            $city_id[]=722;$ok_id[]='80423'; // кумертау 254
            $city_id[]=787;$ok_id[]='80425'; // мелеуз 265
            $city_id[]=444;$ok_id[]='80405'; // белебей 273
            
            $city_id[]=372;$ok_id[]='80415'; // бирск 376
            $city_id[]=567;$ok_id[]='80418'; // дюртюли 491
            $city_id[]=1157;$ok_id[]='80460'; // янаул 543
            */
            $array_temp_mr_id=[];

            $type_direction_num[0]=500; // номера городских
            $type_direction_num[1]=500; // пригородных
            $type_direction_num[2]=5500; // номера межгород
            foreach ($city_id as $key => $cid) {
                $this->editversion($cid);
                    echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                    $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }

            die();
        
       }
       
       public function actionGobusYarosl2() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-yarosl2
            error_reporting(E_ERROR | E_PARSE);
                       $post='';
            $url='http://bus.yarobltrans.ru/api/rpc.php';

            $city_id[]=284;$ok_id[]='78401'; // ярославль
           // $array_temp_mr_id=[];

          /*  $city_id[]=245;$ok_id[]='80445'; // стерлитамак 67
            $city_id[]=226;$ok_id[]='80439'; // салават 113
            $city_id[]=177;$ok_id[]='80427'; // нефтекамск 134
            */
           
            $array_temp_mr_id=[];

            $type_direction_num[0]=100; // номера городских
            $type_direction_num[1]=500; // пригородных
            $type_direction_num[2]=1500; // номера межгород
            foreach ($city_id as $key => $cid) {
                $this->editversion($cid);
                    echo "++++++++++++++++++++++".$cid."+++++++++".$ok_id[$key]."++++++++++".PHP_EOL;
                    $array_temp_mr_id=$this->transnavig($post,$url,$cid,$ok_id[$key],$type_direction_num,$city_id,$array_temp_mr_id);
            }

            die();
       }
       
        public function actionGobusNovoalt() { ////// КОНСОЛЬ из папки yii:  yii carl/gobus-novoalt
            $content = file_get_contents('pav.html');            
        }
       

}