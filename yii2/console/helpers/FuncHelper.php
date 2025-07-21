<?php

namespace console\helpers;

use yii;

class FuncHelper {

    private static $proxyList = null;

    /**
     * Загружает и возвращает список прокси из внешнего .txt файла.
     * Формат файла: user:pass:host:port (каждая прокси на новой строке).
     * @return array Внутренний формат: [ ['host:port', 'user:pass'], ... ]
     */
    private static function getProxyList()
    {
        // Если список уже был загружен ранее, просто возвращаем его
        if (self::$proxyList !== null) {
            return self::$proxyList;
        }

        // Используем псевдоним, который вы задали в bootstrap.php
        $proxyFile = Yii::getAlias('@sharedConfig/proxies.txt');

        if (!file_exists($proxyFile)) {
            Yii::error('Proxy configuration file not found: ' . $proxyFile, __METHOD__);
            self::$proxyList = []; // Возвращаем пустой массив, чтобы не было ошибок
            return self::$proxyList;
        }

        // Читаем все строки из файла в массив
        $lines = file($proxyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $parsedProxies = [];
        foreach ($lines as $line) {
            $line = trim($line);
            // Пропускаем комментарии
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Разбиваем строку user:pass:host:port
            // Этот метод надежно работает, даже если в пароле будут спецсимволы,
            // но не двоеточия.
            $parts = explode(':', $line);

            // Проверяем, что у нас правильное количество частей (минимум 4)
            if (count($parts) < 4) {
                Yii::warning("Invalid proxy format in proxies.txt: {$line}", __METHOD__);
                continue;
            }

            // Собираем части обратно в нужный нам формат
            // [ 'host:port', 'user:pass' ]

            $port = array_pop($parts); // Последний элемент - порт
            $host = array_pop($parts); // Предпоследний - хост
            $userPass = implode(':', $parts); // Все, что осталось - это user:pass

            $hostPort = $host . ':' . $port;

            $parsedProxies[] = [$hostPort, $userPass];
        }

        // Сохраняем результат в кэш и возвращаем его
        self::$proxyList = $parsedProxies;
        return self::$proxyList;
    }
    static function curllocal($url, $postdata = '', $cookie = '', $proxy = '', $ref = '') { // для парсинга CarlController

        $flag = true;
        $header = false;
        $i = 1;

        //$url='https://2ip.ru';
        $uagent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16";

        $ch = curl_init($url);
        // var_dump($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // возвращает заголовки
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        // curl_setopt($ch, CURLOPT_COOKIE, 'mos_id=Cg8qAV44Xhl+PRmSHuVLAgA=');
        if ($ref != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));
        }
        if (!empty($postdata)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        if (!empty($cookie)) {
            //curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/2.txt');
            //curl_setopt($ch, CURLOPT_COOKIEFILE,$_SERVER['DOCUMENT_ROOT'].'/2.txt');
        }
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        if ($header['content'] === FALSE) {
            //   var_dump($err); die();
            // ошибка, нет остановок
            /*  global $link;
              $qer="INSERT INTO `error`(`id`, `type_error`,`text`) "
              . "VALUES (NULL,'Ошибка CURL','proxy= ".$proxy." | url= ".$url."')";
              $ress=mysqli_query($link,$qer); */
            echo 'proxy error=' . $proxy . PHP_EOL; //die();
            $i++;
        } else {
            $flag = false;
        }



        //   var_dump($url,$content); //die();
        return $header;
    }

    static function countproxy() {
        return (count(self::getProxyList()) - 1);
    }



    static function curlj_new($url, $data = '', $proxy_number = -1, $city = 0) {
        $proxya = self::getProxyList();

        $flag = true;
        //header=false;
        $header['content'] = false;
        $i = 1;
        // echo $proxy_number.": ";


        while (($i <= 5) AND $flag) {
            //    echo $i."-";
            $i++;
            $proxy_a = $proxya; //[['91.188.241.161:9630','NG1wdE:PZ8Nx6'],['91.188.242.118:9654','NG1wdE:PZ8Nx6'],['91.188.240.170:9366','NG1wdE:PZ8Nx6'],['213.166.73.217:9206','2j2u2X:useNtY'],['213.166.75.139:9879','2j2u2X:useNtY'],['213.166.74.232:9952','2j2u2X:useNtY'],['193.124.178.131:9834','2j2u2X:useNtY'],['194.67.198.36:9956','2j2u2X:useNtY'],['193.124.177.200:9173','2j2u2X:useNtY']];
            $p = $proxy_number;
            if ($proxy_number == -1) {
                $p = array_rand($proxy_a);
            }
            $proxy = $proxy_a[$p][0];
            $proxyauth = $proxy_a[$p][1]; //'NG1wdE:PZ8Nx6';
            $uagent = " Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.5845.931 YaBrowser/23.9.3.931 Yowser/2.5 Safari/537.36";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyauth);
            curl_setopt($curl, CURLOPT_POST, true);
            @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
            curl_setopt($curl, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT, $uagent);  // useragent
            curl_setopt($curl, CURLOPT_TIMEOUT, 100);        // таймаут ответа
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
            curl_setopt($curl, CURLOPT_HEADER, 0);           // возвращает заголовки

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);


                $headers = array(
                    "Content-Type: application/json",
                    'cache-control: max-age=0',
                    //    'upgrade-insecure-requests: 1',
                    'sec-fetch-user: ?1',
                    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'x-compress: null',
                    'sec-fetch-site: none',
                    'sec-fetch-mode: navigate',
                    'accept-encoding: deflate, br',
                    'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'
                );

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            /* if ($data=='') {
              $data = <<<DATA
              {
              "jsonrpc": "2.0",
              "method": "startSession",
              "params": {},
              "id": 1
              }
              DATA;
              } */
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            /*  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); */
            /*
              //for debug only!
              curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
             */
            $content = curl_exec($curl);

            $err = curl_errno($curl);
            $errmsg = curl_error($curl);
            $header = curl_getinfo($curl);
            curl_close($curl);
           // var_dump($errmsg); die();
            $header['errno'] = $err;
            $header['errmsg'] = $errmsg;
            $header['content'] = $content;
            $content_proverka = json_decode($content);
            /*    if (isset($content_proverka->error)) {
              var_dump($content_proverka->error);
              echo $proxy.PHP_EOL;
              $header['content']=false;
              } */
             //   echo print_r($header);
            //   var_dump($proxy,$content,$errmsg,$err); die(); 
            if ($header['content'] === FALSE || $header['http_code'] != '200') {
                //echo $i.PHP_EOL;  
                //echo "---ERROR_PROXY--".$proxy.' - '.$header['errmsg'].PHP_EOL; continue;
                // ошибка, нет остановок

                $qer = "INSERT INTO `error`(`id`, `type_error`,`text`) "
                        . "VALUES (NULL,'Ошибка CURL','proxy= " . $proxy . " | url= " . $url . "')";
                \Yii::$app->db->createCommand($qer)->execute();
                echo '<div style="display:none;">p e=' . $proxy . "--code=" . $header['http_code'] . '</div>' . PHP_EOL;
            } else {
                $flag = false;
            }
        }
          //  if ($_SERVER['REMOTE_ADDR']=='5.187.71.226') {
       //   var_dump($header);// die();
          //} 
        // var_dump($content); //die();
        if ($header['content'] === FALSE || $header['http_code'] != '200') {
            // echo "B; ";
            return false;
        }
        // echo "G; ";
        return $header['content'];
    }

    static function helpcoord($coord) {
        $x = (string) $coord;
        $x = str_replace(['.', ',', ':'], '', $x);
        if ($x[0] == '1') {
            $x = (float) (substr($x, 0, 3) . "." . substr($x, 3, 8));
        } else {
            $x = (float) (substr($x, 0, 2) . "." . substr($x, 2, 7));
        }
        $rand1 = mt_rand(1, 100) * 0.0000003;
        $rand11 = mt_rand(0, 1);
        $rand1 = ($rand11 == 1) ? $rand1 * (-1) : $rand1;
        $x = round($x + $rand1, 5);
        return $x;
    }

    static function refresh_name_station($title) {
        $row = 0;
        //  echo $title;
        if (($handle = fopen(__DIR__ . "/word.csv", "r")) !== FALSE) {
            $replace0 = [];
            $replace1 = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                //   var_dump($data); die();
                $replace0[$row] = $data[0];
                $replace1[$row] = $data[1];
                $row++;
            }
            fclose($handle);

            $title = str_replace($replace0, $replace1, $title);
        } else {
            
        }
        $title = preg_replace('/[\s]{2,}/', ' ', $title);
        // echo "---".$title;
        return $title;
    }

    static function refresh_title($title) {
        $row = 0;
        //  echo $title;
        if (($handle = fopen("console/helpers/word_title.csv", "r")) !== FALSE) {
            $replace0 = [];
            $replace1 = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                //   var_dump($data); die();
                $replace0[$row] = $data[0];
                $replace1[$row] = $data[1];
                $row++;
            }
            fclose($handle);

            $title = str_replace($replace0, $replace1, $title);
        } else {
            
        }
        $title = preg_replace('/[\s]{2,}/', ' ', $title);
        // echo "---".$title;
        return trim($title);
    }

    static function refresh_info($title) {
        $row = 0;
        //  echo $title;
        if (($handle = fopen("console/helpers/word_info.csv", "r")) !== FALSE) {
            $replace0 = [];
            $replace1 = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                //   var_dump($data); die();
                $replace0[$row] = $data[0];
                $replace1[$row] = $data[1];
                $row++;
            }
            fclose($handle);

            $title = str_replace($replace0, $replace1, $title);
        } else {
            
        }
        $title = preg_replace('/[\s]{2,}/', ' ', $title);
        // echo "---".$title;
        return trim($title);
    }

    static function changeCityid_saratov($num, $type) {
        $one = mb_substr($num, 0, 1, 'UTF-8');

        if ($one == 'Э') {
            return 383;
        } elseif ($one == 'Б') {
            if ($num == 'Б110') {
                return 33;
            }
            return 384;
        } elseif ($one == 'В') {
            return 385;
        } elseif ($one == 'С') {
            return 386;
        } elseif ($one == 'Р') {
            return 387;
        } elseif (($num == '8' OR $num == '14') AND $type == '2') {
            return 383;
        }
        return 232;
    }

    static function changeTypedirection_saratov($num, $type) {
        if ($city_id != 232) {
            return 1;
        }
        $array_prigor = array(
            '102',
            '153',
            '223К',
            '223',
            '225',
            '226К',
            '226А',
            '229К',
            '231',
            '232',
            '233',
            '235',
            '236',
            '238',
            '239А',
            '239',
            '241',
            '242У',
            '242',
            '243Т',
            '243',
            '245',
            '246Н',
            '247А',
            '247',
            '248',
            '251',
            '274Б',
            '282Б',
            '283',
            '284',
            '284А',
            '284К',
            '284Б',
            '285',
            '291-А',
            '330',
            '348',
            '349',
            '350',
            '358',
            '365',
            '389',
            '391',
            '419-А',
            '445',
            '454',
            '463',
            '464',
            '491'
        );
        $array_meg = array(
            '366',
            '475',
            '501',
            '502П-Э',
            '503',
            '507',
            '525',
            '534',
            '567',
            '601',
            '602',
            '602П-Э',
            '602Э',
            '603',
            '605',
            '612',
            '614Э',
            '614',
            '623',
            '626',
            '630',
            '631',
            '632',
            '634А',
            '636',
            '637',
            '642Э',
            '646',
            '649',
            '650',
            '651',
            '663',
            '666',
            '668',
            '684',
            '712',
            '720',
            '766',
            '776',
            '826',
            '1259'
        );
        if (in_array($num, $array_prigor)) {
            return 2;
        } elseif (in_array($num, $array_meg)) {
            return 3;
        }
        return 1;
    }

    static function changeTypedirection_irkutsk($num, $type) {
        if ($num > 100) {
            return 2;
        } else {
            return 1;
        }
    }

    static function time_ob($ttt) {

        $ttt = str_replace('-', ':', $ttt);
        $tttt = explode(' ', $ttt);
        $time_i = [];
        foreach ($tttt as $er) {
            $tm = explode(':', $er);
            if (ctype_digit($tm[0]) AND ctype_digit($tm[1])) {
                $time_i[$tm[0]][] = $tm[1];
            }
        }
        //var_dump($time_i); die();
        $itog = [];
        foreach ($time_i as $hour => $min) {
            $itog[] = [$hour, $min];
        }
        // var_dump($itog); die();
        return $itog;
    }

    static function time_old_to_new($t) { // НЕ РАБОТАЕТ
        if ($t != '') {
            $ex = explode("/", $t);
            if (count($ex) == 1) {
                return [[json_encode(FuncHelper::time_ob($ex[0])), '', '', '', '', '', ''], 3, '1000000'];
            } elseif (count($ex) == 2) {
                return [[json_encode(FuncHelper::time_ob($ex[0])), '', '', '', '', json_encode(FuncHelper::time_ob($ex[1])), ''], 1, '1000010'];
            } else {
                return [[json_encode(FuncHelper::time_ob($ex[0])), '', '', '', '', '', ''], 3, '1000000'];
            }
        } else {
            return false;
        }
    }

    static function time_vmeste($tnew, $told) {
        //var_dump($tnew,$told);
        //   $q=array_merge($tnew, $told);
        // var_dump($q);
        $tim = [];
        $it = [];
        if (is_array($told)) {
            foreach ($told as $t) {
                $tim[$t[0]] = $t[1];
            }
        }
        if (is_array($tnew)) {
            foreach ($tnew as $t) {
                if (is_array($tim[$t[0]])) {
                    if (is_array($t[1])) {
                        $tim[$t[0]] = array_merge($tim[$t[0]], $t[1]);
                        asort($tim[$t[0]]);
                    }
                } else {
                    $tim[$t[0]] = $t[1];
                }
            }
        }
        foreach ($tim as $k => $t) {
            $it[] = [$k, $t];
        }
        var_dump($it);
        return json_encode($it);
    }

    static function getpos($route, $url) {
        $post = '';
        $proxy_num = rand(0, self::countproxy());
        // $url=;
        $city_id = $route->city_id; //$ok_id=66401;
        /*   foreach ($this->color as $rr=>$cc) {
          $color_l[]=$rr;
          } */
        /*     jsonrpc: "2.0",
          method: "getRoute",
          params: {
          sid: c.sid,
          mr_id: a
          },
          id: b */
        //  {"jsonrpc":"2.0","method":"getUnits","params":{"sid":"91860FE1-FAE2-443D-A4EB-958173AF5891","marshList":["17","27"]},"id":142}
        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj_new($url, $post, $proxy_num, $city_id);
        if (!$sid) {
            return false;
        }
        $sid = json_decode($sid);
        $sid = $sid->result->sid; //var_dump($sid); die();
        $post_pos = '{"jsonrpc":"2.0","method":"getUnits","params":{"sid":"' . $sid . '","marshList":["' . $route->temp_route_id . '"]},"id":2}';
        // $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        $all_marsh = FuncHelper::curlj_new($url, $post_pos, $proxy_num, $city_id);

        $all_marsh = json_decode($all_marsh);  // var_dump($all_marsh); die();
        if ($all_marsh) {
            return $all_marsh;
        } else {
            return false;
        }
    }
    
    /**
     * js донора
       function i(t, e, n, i) {
        var o = t + "~" + i + "~" + e + "~" + n,
            a = r(o),
            s = a.substr(0, 8) + "-" + a.substr(8, 4) + "-" + a.substr(12, 4) + "-" + a.substr(24, 4) + "-" + a.substr(28, 12);
        return {
            magicStr: a.substr(16, 8),
            guidStr: s
        }
    }

    function o(t, e) {
        var n = i(t.data.method, t.data.id, t.data.params.sid, e);
        t.url = t.url + "?m=" + n.guidStr, t.data.params.magic = n.magicStr
    }
     * 
     */
    static function mafik_old($id_num,$get,$sid) {
        $sha1n = sha1($get."-".$id_num."-".$sid);  //t.data.method, t.data.id, t.data.params.sid
        $magici = substr($sha1n,0, 8)."-".substr($sha1n,8, 4)."-".substr($sha1n,12, 4)."-".substr($sha1n,24, 4)."-".substr($sha1n,28, 12);//m GET
        $magicStr=substr($sha1n,16, 8); //magic 
        return [$magici,$magicStr];
    }
    static function mafik($id_num,$get,$sid,$city_alias) {
        $sha1n = sha1($get."~".$city_alias."~".$id_num."~".$sid);  //t.data.method, t.data.id, t.data.params.sid
        $magici = substr($sha1n,0, 8)."-".substr($sha1n,8, 4)."-".substr($sha1n,12, 4)."-".substr($sha1n,24, 4)."-".substr($sha1n,28, 12);//m GET
        $magicStr=substr($sha1n,16, 8); //magic 
        return [$magici,$magicStr];
    }
    static function getpos_magic($route, $url) {
        $post = '';
        /*$proxy_num = rand(0, self::countproxy());
        $city_id = $route->city_id; 

        $post = '{"jsonrpc": "2.0","method": "startSession","params": {},"id": 1}';
        $sid = FuncHelper::curlj_new($url, $post, $proxy_num, $city_id);
        if (!$sid) {
            return false;
        }
        */
        //die('f5');
        $city_id = $route->city_id; 
        $fcount=0;
        $sid=false;
        while(!$sid AND $fcount<20) {
            $proxy_num = rand(0, FuncHelper::countproxy());
            $post = '{"id": 1,"jsonrpc": "2.2","method": "startSession", "ts": '.time().',"params": {}}';
            $sid = FuncHelper::curlj_new($url[0], $post, $proxy_num, $city_id);
           //if ($_SERVER['REMOTE_ADDR']=='5.187.71.208') {
             //   var_dump($sid); die('6757567573gf');
           // }
            $id_num = 1;
            if ($sid) {
                @$sid = json_decode($sid); 
                @$sid = $sid->result->sid;
            }
            $fcount++;
        }
        if (!$sid) { die("error33456956457"); }
        $id_num = 2;
        if ($url[1]!='') {
            $mafic= FuncHelper::mafik($id_num,"getUnits",$sid,$url[1]);    
            $post_pos = '{"id":2,"jsonrpc":"2.2","method":"getUnits","params":{"sid":"' . $sid . '","marshList":["' . $route->temp_route_id . '"],"magic":"'.$mafic[1].'"},"ts":'.time().'}';
            // $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        } else {
            $mafic= FuncHelper::mafik_old($id_num,"getUnits",$sid);    
            $post_pos = '{"id":2,"jsonrpc":"2.0","method":"getUnits","params":{"sid":"' . $sid . '","marshList":["' . $route->temp_route_id . '"],"magic":"'.$mafic[1].'"}}';
            // $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        
        }
        $all_marsh = FuncHelper::curlj_new($url[0]."?m=".$mafic[0], $post_pos, $proxy_num, $city_id);
 //var_dump($url."?m=".$mafic[0], $post_pos, $proxy_num, $city_id,$all_marsh); die();
        $all_marsh = json_decode($all_marsh); 
        if ($all_marsh) {
            return $all_marsh;
        } else {
            return false;
        }
    }
    
    static function getunit_magic($route, $u_id, $url) {
        $post = '';
        $city_id = $route->city_id; 
        $fcount=0;
        $sid=false;
        if (is_array($url)) {
            $uri=$url[0];
        } else {
            $uri=$url;
        }
        while(!$sid AND $fcount<20) {
            $proxy_num = rand(0, FuncHelper::countproxy());
            $post = '{"id": 1,"jsonrpc": "2.2","method": "startSession", "ts": '.time().',"params": {}}';
            $sid = FuncHelper::curlj_new($uri, $post, $proxy_num, $city_id);
        //   var_dump($url[0], $post, $proxy_num, $city_id);
            $id_num = 1;
            if ($sid) {
                @$sid = json_decode($sid); 
                @$sid = $sid->result->sid;
            }
            $fcount++;
        }
        if (!$sid) { die("error11666111576"); }
        $id_num = 2;
        if (is_array($url)) {
            $mafic= FuncHelper::mafik($id_num,"getUnitArrive",$sid,$url[1]);    
            $post_pos = '{"id":2,"jsonrpc":"2.2","method":"getUnitArrive","params":{"sid":"' . $sid . '","u_id":"' . $u_id . '","magic":"'.$mafic[1].'"},"ts":'.time().'}';
            // $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        } else {
            $mafic= FuncHelper::mafik_old($id_num,"getUnitArrive",$sid);    
            $post_pos = '{"id":2,"jsonrpc":"2.0","method":"getUnitArrive","params":{"sid":"' . $sid . '","u_id":"' . $u_id . '","magic":"'.$mafic[1].'"}}';
            // $post_all_marsh='{"jsonrpc": "2.0","method": "getTransTypeTree","params": {"sid": "'.$sid.'","ok_id": "'.$ok_id.'"},"id": 2}';
        }
        $route_info = FuncHelper::curlj_new($uri."?m=".$mafic[0], $post_pos, $proxy_num, $city_id);
        $route_info = json_decode($route_info); 
        if ($route_info) {
            return $route_info;
        } else {
            return false;
        }
    }
}
