<?php

namespace App\Util;

use Symfony\Component\String as SymfonyString;

class StringUtil
{
    private function __construct() {}

    public static function randomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = \strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[\random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function prefix(string $string, string $prefix): string
    {
        if ($string == '') {
            return '';
        }
        return $prefix . $string;
    }

    public static function trimPrefix(string $string, string|array $prefix): string
    {
        return SymfonyString\u($string)->trimPrefix($prefix);
    }

    public static function redact(string $string, int $num = 3, array $sep = [' ']): string
    {
        $s = 0;
        for ($i = 0; $i < \strlen($string); $i++) {
            if (\in_array($string[$i], $sep)) {
                $s = 0;
                continue;
            }

            $s += 1;

            if ($s <= $num) {
                continue;
            }

            if ($s >= $num * 2) {
                $s = 0;
            }

            $string[$i] = '*';
        }
        return $string;
    }
}
