<?php

namespace Pequi\Tools;

class Number
{

    public static function toDecimal($number, $dec = 2)
    {
        $before = 0;
        $after = 0;
        if (preg_match("/[^\d\,\.]/", $number)) {
            return 0;
        }
        if (preg_match("/^[\d]{1,}$/", $number)) {
            $before = $number;
        }
        if (preg_match("/([0-9\,\.]{0,})\.([0-9]{1,})$/", $number, $match)) {
            $before = str_replace('.', '', str_replace(',', '', $match[1]));
            $after = $match[2];
        }

        if (preg_match("/([0-9\,\.]{0,})\,([0-9]{1,})$/", $number, $match)) {
            $before = str_replace('.', '', str_replace(',', '', $match[1]));
            $after = $match[2];
        }
        return number_format($before . '.' . $after, $dec, '.', '');
    }

    public static function key($size = 6)
    {
        $alphabet = '1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public static function keyAlphaNumeric($size = 6)
    {
        $alphabet = '1234567890ABCDEFGHIJLMNOPQRSTUVXZ';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public static function corrigirCoordenadas($coordenada)
    {
        return (preg_match("/\-/", $coordenada) ? '-' . number_format(str_replace('-', '', $coordenada), 7, '.', '') : number_format(str_replace('-', '', $coordenada), 7, '.', ''));
    }

    public static function intNull($var)
    {
        if (!is_double($var) && !is_int($var) && !is_string($var) && !is_bool($var) && trim($var) == '') {
            return null;
        }

        return intval($var);
    }

    public static function doubleNull($var)
    {
        if (!is_double($var) && !is_int($var) && !is_string($var) && !is_bool($var) && trim($var) == '') {
            return null;
        }

        return doubleval($var);
    }
}
