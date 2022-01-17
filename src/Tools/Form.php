<?php

namespace PequiPHP\Tools;

use PequiPHP\Session;

class Form
{
    private static $post = null;

    public static function selected($name, $value, $defaultValue = null, $checkGet = false)
    {
        $value = trim($value);
        if ((self::input($name, $defaultValue, $checkGet) == $value)) {
            return ' selected="selected"';
        }
        return '';
    }

    public static function checked($name, $value, $defaultValue = null, $checkGet = false)
    {
        $value = trim($value);
        if ((self::input($name, $defaultValue, $checkGet) == $value)) {
            return ' checked="checked"';
        }
        return '';
    }

    public static function input($name, $defaultValue = null, $isGet = false)
    {

        if (self::$post === null) {
            self::$post = Session::item('post', true);
        }

        // Session::item('post')
        if (!$isGet && self::$post) {
            if (isset(self::$post[$name])) {
                if (!is_array(self::$post[$name]) && trim(self::$post[$name]) != '') {
                    return trim(self::$post[$name]);
                } else {
                    return $defaultValue;
                }
            }
        } else {
            if ($isGet) {
                $value = input($name, null, 'get');
                if (!is_array($value) && trim($value) != '') {
                    return trim($value);
                } else {
                    return $defaultValue;
                }
            }
        }

        return $defaultValue;
    }

    /**
     * Get the value of post
     */
    public static function getPost()
    {
        if (self::$post === null) {
            self::$post = Session::item('post', true);
            if(!self::$post){
                self::$post = [];
            }
        }
        return self::$post;
    }
}
