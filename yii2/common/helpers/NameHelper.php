<?

namespace common\helpers;

class NameHelper extends \yii\helpers\BaseArrayHelper {

    public static function nameroute($r) {
        $result = 'fdg';
        return $result;
    }

    public static function slogs($text) {
        $RusA = "[абвгдеёжзийклмнопрстуфхцчшщъыьэюя]";
        $RusV = "[аеёиоуыэюя]";
        $RusN = "[бвгджзклмнпрстфхцчшщ]";
        $RusX = "[йъь]";

#main ruller
        $regs[] = "~(" . $RusX . ")(" . $RusA . $RusA . ")~iu";
        $regs[] = "~(" . $RusV . ")(" . $RusV . $RusA . ")~iu";
        $regs[] = "~(" . $RusV . $RusN . ")(" . $RusN . $RusV . ")~iu";
        $regs[] = "~(" . $RusN . $RusV . ")(" . $RusN . $RusV . ")~iu";
        $regs[] = "~(" . $RusV . $RusN . ")(" . $RusN . $RusN . $RusV . ")~iu";
        $regs[] = "~(" . $RusV . $RusN . $RusN . ")(" . $RusN . $RusN . $RusV . ")~iu";
        $regs[] = "~(" . $RusX . ")(" . $RusA . $RusA . ")~iu";
        $regs[] = "~(" . $RusV . ")(" . $RusA . $RusV . ")~iu";

        foreach ($regs as $cur_regxp) {
            $text = preg_replace($cur_regxp, "$1|$2", $text);
        }
        $text_array= explode("|", $text);
        return $text_array;
    }

    public static function replaceWordsInString($string) {

// Считываем содержимое файла в массив
        $replacements = file(__DIR__ . '/words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Создаем ассоциативный массив из пар "поиск => замена"
        $replacementPairs = array();
        foreach ($replacements as $replacement) {
            list($search, $replace) = explode('=>', $replacement);
            $replacementPairs[$search] = $replace;
        }

        $newString = str_replace(array_keys($replacementPairs), array_values($replacementPairs), $string);

      //  $newString = static::truncateLongWords($newString);
        return $newString;
    }
    
     public static function replaceWordsInStringWsokrat($string) {

        $newString = static::replaceWordsInString($string);

        $newString = static::truncateLongWords($newString);
        return $newString;
    }
    

    public static function truncateLongWords(string $input): string {
        // Регулярное выражение для поиска слов длиннее 17 символов
        $pattern = '/\b\p{L}{17,}\b/u';

        // Функция замены
        $replaceCallback = function ($matches) {
            $word = $matches[0];
            //$syllables = preg_split('/(?<!^)(?!$)/u', $word); // Разделение слова на слоги
            $syllables= self::slogs($word);
            $truncated_word = implode("", array_slice($syllables, 0, rand(3, 4))); // Выбор 3-4 слогов
            return $truncated_word . "."; // Добавление точки в конце
        };

        // Замена слов
        $result = preg_replace_callback($pattern, $replaceCallback, $input);

        return $result;
    }
}
