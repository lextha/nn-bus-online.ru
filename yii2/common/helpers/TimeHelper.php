<?

namespace common\helpers;
use DateTime; // Добавляем использование глобального класса DateTime
use DateTimeZone;
use yii;

class TimeHelper extends \yii\helpers\BaseArrayHelper {

    private static function get_time_form_json($tjson,$day_week) {
        //  var_dump($tjson);
        $PCREpattern = '/\r\n|\r|\n/u';
        $tjson = preg_replace($PCREpattern, '', $tjson);
        //  var_dump($tjson);
        // $tjson=
        $json = json_decode($tjson, true);
        //   echo "<pre>";  var_dump($json); echo "</pre>"; die();
        $text = '';
        //   var_dump($json); die();
        /// Ищем максимальное число минут, для заполнения ячеек в строке
        $q = 0;
        $itogq = 0;
        if (is_array($json)) {

            foreach ($json as $key => $j) {
                if ($q > $itogq) {
                    $itogq = $q;
                } // 
                if ($key === 'ps') {
                    
                } else {
                    if ($j[0] != 'legend') {
                        $q = 0;
                        foreach ($j[1] as $key => $t) {
                            if (!isset($j[1][$key - 1]) OR (isset($j[1][$key - 1]) AND ($t != $j[1][$key - 1]))) { // от задвоения одинакового времени
                                $q++;
                            }
                        }
                    }
                }
            }
            //////////
            $flag_nachalo=1; // флаг для отметки следующего транспорта
            $text .= '<table class="table"><tbody><tr><th>час</th><th colspan="' . $itogq . '">минуты</th></tr>';
            $text_legend='';
            foreach ($json as $key => $j) {
                if ($key === 'ps') {
                    //   var_dump($j); die();
                    foreach ($j as $t) {
                        if ($t != '') {
                            $text_legend .= "<div class='legendtext'>" . $t . "</div>";
                        }
                    }
                } else {
                    if ($j[0] != 'legend') {

                        $text .= '<tr><th>' . $j[0] . '</th>';
                        $qq = 0;
                        $currentTime = new DateTime("now", new DateTimeZone(Yii::$app->params['DateTimeZone']));
                        $currentTimeFormatted = $currentTime->format('H:i'); // Форматируем текущее время в формат "часы:минуты"
                        $dayOfWeek = $currentTime->format('N'); // Получаем номер дня недели (от 1 до 7)
                        foreach ($j[1] as $key => $t) {
                            if (!isset($j[1][$key - 1]) OR (isset($j[1][$key - 1]) AND ($t != $j[1][$key - 1]))) { // от задвоения одинакового времени
                                preg_match('/(\d{1,2})\((.+)\)/', $t, $new_t);
                                if (count($new_t) > 0) {
                                    $targetTime = new DateTime($j[0].":".$new_t[1],new DateTimeZone(Yii::$app->params['DateTimeZone']));  
                                    $targetTimeFormatted = $targetTime->format('H:i'); // Форматируем заданное время в формат "часы:минуты"
                                    if (strtotime($currentTimeFormatted) > strtotime($targetTimeFormatted) AND ($dayOfWeek==$day_week)) { $re=' class="table__fade"'; } else { $re='';}
                                    $text .= "<td style='color:" . $new_t[2] . ";'".$re.">" . $new_t[1] . "</td>";
                                } else {
                                    $targetTime = new DateTime($j[0].":".$t,new DateTimeZone(Yii::$app->params['DateTimeZone']));  
                                    $targetTimeFormatted = $targetTime->format('H:i'); // Форматируем заданное время в формат "часы:минуты"
                                    if (strtotime($currentTimeFormatted) > strtotime($targetTimeFormatted) AND ($dayOfWeek==$day_week)) { $re=' class="table__fade"'; } else { if ($flag_nachalo AND ($dayOfWeek==$day_week)) { $re=' class="is_active"'; $flag_nachalo=0; } else { $re=''; } }
                                    $text .= "<td".$re.">" . $t . "</td>";
                                }
                                $qq++;
                            }
                        }
                       // $text .=$qq.'+'.$itogq;
                        if ($qq < $itogq) {
                            for ($i = $qq; $i < $itogq;$i++) {
                                $text .= "<td></td>";
                            }
                        }
                        $text .= '</tr>';
                    }
                }
            } $text .= '</tbody></table>';
        }
        /*  die(); */

        //  var_dump($text);
        $text .="<div class=\"table-description\"><p class=\"table-description__text\"><span class=\"table-description__td is_active\">15</span>Время ближайшего транспорта</p><p class=\"table-description__text\"><span class=\"table-description__td --fade\">15</span>Время прошедшего транспорта</p><p class=\"table-description__text\">".$text_legend."</p></div>";
        
        return $text;
    }

    public static function time_g($time_text, $type_day, $day_week) {
        $text = '';
        if (is_array($time_text)) {
            //  echo "<pre>";var_dump($time_text,$type_day,$day_week);
            $date = $day_week;
            //  self::get_time_form_json($time_text=$time_text[0];
            if ($type_day == 1) {
                if ($date < 6) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } else {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                }
            } elseif ($type_day == 2) {
                if ($date == 1) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 2) {
                    return self::get_time_form_json($time_text['tuesday'], $day_week) ?? '';
                } elseif ($date == 3) {
                    return self::get_time_form_json($time_text['wednesday'], $day_week) ?? '';
                } elseif ($date == 4) {
                    return self::get_time_form_json($time_text['thursday'], $day_week) ?? '';
                } elseif ($date == 5) {
                    return self::get_time_form_json($time_text['friday'], $day_week) ?? '';
                } elseif ($date == 6) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } elseif ($date == 7) {
                    return self::get_time_form_json($time_text['sunday'], $day_week) ?? '';
                }
            } elseif ($type_day == 4) {
                if ($date < 6) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 6) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } elseif ($date == 7) {
                    return self::get_time_form_json($time_text['sunday'], $day_week) ?? '';
                }
            } elseif ($type_day == 5) {
                if ($date < 6) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 6) {
                if ($date > 5) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 7) {
                if ($date < 7) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 7) {
                    return self::get_time_form_json($time_text['sunday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 8) {
                if ($date < 6) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 6) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 9) {
                if ($date < 5) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 5) {
                    return self::get_time_form_json($time_text['friday'], $day_week) ?? '';
                } elseif ($date > 5) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 10) {
                if ($date == 6) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } elseif ($date == 7) {
                    return self::get_time_form_json($time_text['sunday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 11) {
                if ($date < 5) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 5) {
                    return self::get_time_form_json($time_text['friday'], $day_week) ?? '';
                } elseif ($date == 6) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 12) {
                if ($date < 5) {
                    return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
                } elseif ($date == 5) {
                    return self::get_time_form_json($time_text['friday'], $day_week) ?? '';
                } elseif ($date == 6) {
                    return self::get_time_form_json($time_text['saturday'], $day_week) ?? '';
                } elseif ($date == 7) {
                    return self::get_time_form_json($time_text['sunday'], $day_week) ?? '';
                } else {
                    $text .= "<span style='color:red;'>Не работает</span>";
                    return $text;
                }
            } elseif ($type_day == 3) {

                return self::get_time_form_json($time_text['monday'], $day_week) ?? '';
            }
        } else {
            return "";
        }
    }

    public static function time_helper2($string) {
        $PCREpattern = '/\r\n|\r|\n/u';
        $string = preg_replace($PCREpattern, '', $string);
        $json = json_decode($string, true);
        $text1 = '';
        $text2 = '';
        if (is_array($json)) {

            $text1_arr = [];
            foreach ($json as $key => $j) {
                if ($key === 'ps') {
                    foreach ($j as $t) {
                        $text2 .= $t;
                    }
                } else {
                    foreach ($j[1] as $t) {
                        /* preg_match('/(\d{1,2})\((.+)\)/', $t, $new_t);
                          //var_dump($new_t);
                          if (count($new_t)>0) {
                          $text.="<span style='color:".$new_t[2].";'>".$j[0].":".$new_t[1]."</span>";
                          } else { */
                        if ($t != '') {
                            $text1_arr[] = $j[0] . ":" . $t;
                        }
                        // }
                    }
                }
            }
            $text1 = implode(" ", $text1_arr);
        }
        /*  die();
          var_dump($text); */
        return [$text1, $text2];
    }

    /**
     * Формирует строку для записи в БД, из строк времени и легенды
     *
     * @param string $string_time строка веремени "10:20 15:22 16:55(red)"
     * @param string $string_legend строка легенды, любая запись в html
     * @return bool whether the email was sent
     */
    public static function time_to_db($string_time, $string_legend) {
        $time = explode(" ", $string_time);
        $it = [];

        foreach ($time as $t) {    //var_dump($t);
            $t = trim($t);
            if ($t == '') {
                // $it[$tt[0]][]=''; 
            } else {
                $tt = explode(":", $t);
                if (isset($tt[1])) {
                    $it[$tt[0]][] = $tt[1];
                }
            }
        }
        $itog = [];
        foreach ($it as $key => $t) {
            $itog[] = [$key, $t];
        }
        $itog['ps'] = [$string_legend];
        return json_encode($itog, JSON_UNESCAPED_UNICODE);
    }
}
