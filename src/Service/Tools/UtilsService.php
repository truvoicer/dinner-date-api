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

}