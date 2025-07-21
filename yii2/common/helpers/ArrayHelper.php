<?
namespace common\helpers;
class ArrayHelper extends \yii\helpers\BaseArrayHelper
{
    public static function map2($array, $from, $to, $to2)
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            $value2 = static::getValue($element, $to2);
            $result[$key] = $value2." | ".$value;
        }

        return $result;
    }
}