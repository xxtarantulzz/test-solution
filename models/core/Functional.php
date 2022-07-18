<?php

namespace app\models\core;

use Yii;

class Functional
{
    static public function getToken()
    {
        return base64_encode('xtarantulz-solution');
    }

    static public function translite($value)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'і' => 'i', 'ї' => 'i',
        );

        $value = mb_strtolower($value);
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    static public function upperFirst($str)
    {
        $array = explode('-', $str);
        $result = '';
        foreach ($array as &$tmp) {
            $first = mb_strtoupper(mb_substr($tmp, 0, 1));
            $last = mb_strtolower(mb_substr($tmp, 1, mb_strlen($tmp) - 1));
            $tmp = $first . $last;
        }

        $result = implode('-', $array);

        return $result;
    }

    static public function getMiniImage($url)
    {
        $path = $url;
        $tmp_array = explode('/', $path);
        $name = $tmp_array[count($tmp_array) - 1];
        unset($tmp_array[count($tmp_array) - 1]);
        $path = implode('/', $tmp_array);
        $path = $path . "/mini/" . $name;
        if (!file_exists(Yii::getAlias('@frontend/web') . $path)) {
            $path = $url;
            if (!file_exists(Yii::getAlias('@frontend/web') . $path)) $path = '/img/no_image.png';
        }

        return $path;
    }

    static function floorDecimal($value, $decimals = 2)
    {
        $value = round($value, 8);

        $a = explode('.', $value);
        if (!isset($a[1])) $a[1] = '';
        $a[1] = substr($a[1], 0, $decimals);
        while (strlen($a[1]) < $decimals) {
            $a[1] = $a[1] . '0';
        }
        return $a[0] . '.' . $a[1];
    }

    static function getNormalId($value, $len = 6)
    {
        while (strlen($value) < $len) {
            $value = '0' . $value;
        }

        return $value;
    }

    static function getNameFiles($files)
    {
        $result = "";

        if (is_array($files)) {
            foreach ($files as &$file) {
                $tmp = explode("/", $file);
                $file = $tmp[count($tmp) - 1];
            }
            $result = implode(", ", $files);
        } elseif ($files) {
            $tmp = explode("/", $files);
            $result = $tmp[count($tmp) - 1];
        }

        return $result;
    }
}
