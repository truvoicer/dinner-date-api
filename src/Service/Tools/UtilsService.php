<?php
namespace App\Service\Tools;

class UtilsService
{
    public static function labelToName(string $string, $toUpper = false): string
    {
        $stringReplace = str_replace(" ", "_", str_replace("-", "_", trim($string)));
        if ($toUpper) {
            return strtoupper($stringReplace);
        }
        return strtolower($stringReplace);
    }

    public static function stringToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    public static function checkArrayField(string $field, array $array = []) {
        if (array_key_exists($field, $array)) {
            return $array[$field];
        }
        return null;
    }

    public static function randomStringGenerator(?int $count = 16) {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $input_length = strlen($permitted_chars);
            $random_string = '';
            for($i = 0; $i < $count; $i++) {
                $random_character = $permitted_chars[random_int(0, $input_length - 1)];
                $random_string .= $random_character;
            }
            return $random_string;
    }
}