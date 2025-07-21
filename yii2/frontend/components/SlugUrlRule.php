<?php

namespace frontend\components;

use yii\web\UrlRuleInterface;
use common\models\City;
use common\models\Route;
use common\models\Station;
use common\models\Stationmany;
use yii\helpers\Url;
use common\models\News;

class SlugUrlRule implements UrlRuleInterface {

    public static function slugify($text, $tt) { //UPDATE `route` SET `alias2`=NULL WHERE `city_id`=61
        $string = mb_strtolower($text, 'UTF-8');
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);
        $string = preg_replace('/[\r\n\t]+/', ' ', $string);

        $table = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g',
            'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh', 'ь' => '',
            'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya', ' ' => '-', '«' => "", '»' => "", '№' => "", '.' => "x"
        ];

        $output = str_replace(
                array_keys($table),
                array_values($table), $string
        );

        $output = str_replace("--", "-", $output);
        $output = str_replace("--", "-", $output);
        $output = str_replace("--", "-", $output);
        $output = str_replace("--", "-", $output);

        $output = preg_replace('/|[^\w-]+|/u', '', $output); //var_dump($output);
        // $output = preg_replace('/[\x00-\x1F\x7F]/u', '', $output);

        $output = str_replace("--", "-", $output);
        $output = str_replace("--", "-", $output);

        $text = trim($output, "-");

        if (empty($text)) {
            return 'n-a';
        }
        if ($tt == 1) {
            return $text;
        } elseif ($tt == 2) {
            return "t" . $text;
        } elseif ($tt == 3) {
            return "tr" . $text;
        } elseif ($tt == 4) {
            return "m" . $text;
        } elseif ($tt == 5) {
            return "e" . $text;
        }
    }

    public static function writePageToFile($url, $id) { // не используется
        // Открываем файл для записи
        $file = fopen(__DIR__ . '/pages.txt', 'a');

        // Записываем адрес страницы и ее идентификатор в файл
        fwrite($file, "{$url}|{$id}\n");

        // Закрываем файл
        fclose($file);
    }

    public static function getPageIdFromFile($url) {// не используется
        // Считываем содержимое файла в массив
        $pages = file(__DIR__ . '/pages.txt', FILE_IGNORE_NEW_LINES);

        if (isset($pages[0]) AND $pages[0] != '') {
            // Ищем идентификатор страницы по ее адресу
            foreach ($pages as $page) {
                list($pageUrl, $pageId) = explode('|', $page);

                if ($pageUrl == $url) {
                    return $pageId;
                }
            }
        }
        // Если страница не найдена, возвращаем null
        return false;
    }

    public function createUrl($manager, $route, $params) {
        if ($route == 'site/route' && isset($params['id'])) {

            if (isset($params['route'])) {
                /*  if (is_array($params['route'])) {
                  if ($params['route']['alias2'] != NULL) {
                  return $params['route']['alias2'];
                  } else {
                  $alias = self::slugify($params['route']['number']);
                  $r = Route::find()->where(['id' => $params['route']['id']])->one();
                  $r->alias2 = $alias;
                  $r->save();
                  return $alias;
                  }
                  } */
                //  echo $params['route']->alias2;
                if ($params['route']->alias2 != NULL) {
                    return $params['route']->alias2;
                } else {
                    $alias = self::slugify($params['route']->number, $params['route']->type_transport);
                    $r = Route::find()->where(['id' => $params['route']->id])->one();
                    $r->alias2 = $alias;
                    $r->save();
                    return $alias;
                }
                return $params['route']->alias2;
            } else {
                $r = Route::find()->where(['id' => $params['id']])->one();
                return $r->alias2;
            }
        } elseif ($route == 'site/news' && isset($params['id'])) {
            $post = News::find()->where(['id' => $params['id']])->one();
            return "news/" . $post->alias2;
        } elseif ($route == 'site/newslist') {
            return "news";
        }
        return false;
    }

    public function parseRequest($manager, $request) {

        $pathInfo = explode("/", $request->pathInfo); //var_dump($pathInfo); die();


        if (count($pathInfo) == 1 && $pathInfo[0] != 'news') {
            $endpath = array_pop($pathInfo);
            // var_dump($id); die();
            if ($endpath) {
                $post = Route::find()->where(['alias2' => $endpath, 'city_id' => \Yii::$app->params['city_id']])->andWhere("version=1 OR version=4")->one();
                if ($post != null) {
                    return ['site/route', ['id' => $post->id, 'route' => $post]];
                }
            }
        } elseif (count($pathInfo) == 2 && $pathInfo[0] == 'newslist') {
            \Yii::$app->response->redirect("/news", 301)->send();
            \Yii::$app->end();
            die();
        } elseif (isset($pathInfo[0]) && $pathInfo[0] == 'news') {
            if (count($pathInfo) == 2) {// страница новости
                //var_dump($pathInfo); die();
                $endtpath = $pathInfo[1];
                $post = News::find()->where(['alias2' => $endtpath, 'city_id' => \Yii::$app->params['city_id']])->one();
                if ($post != null) {
                    return ['site/news', ['id' => $post->id]];
                } else {
                    return false;
                }
            } elseif (count($pathInfo) == 1) { //список новостей
                //  $firstpath = $pathInfo[0];
                $post = News::find()->where(['city_id' => \Yii::$app->params['city_id']])->count();
                if ($post > 0) {
                    return ['site/newslist', ['id' => \Yii::$app->params['city_id']]];
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}

?>