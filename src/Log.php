<?php

namespace Pequi;

class Log
{
    public static function debug($msg, $name = null)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg, JSON_PRETTY_PRINT);
        }
        $content = 'DEBUG(' . ($name ? $name . ' ' : '') . date('Y-m-d H:i:s') .  '): ' . $msg . chr(10);
        @file_put_contents(PATH_LOGS . 'debug_' . date('Ymd') . '.log', $content, FILE_APPEND | LOCK_EX);
    }

    public static function error($msg, $name = null)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg, JSON_PRETTY_PRINT);
        }
        $content = 'ERROR(' . ($name ? $name . ' ' : '') . date('Y-m-d H:i:s') .  '): ' . $msg . chr(10);
        @file_put_contents(PATH_LOGS . 'error_' . date('Ymd') . '.log', $content, FILE_APPEND | LOCK_EX);
    }

    public static function log($msg, $name = null)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg, JSON_PRETTY_PRINT);
        }
        $content = 'LOG(' . ($name ? $name . ' ' : '') . date('Y-m-d H:i:s') .  '): ' . $msg . chr(10);
        @file_put_contents(PATH_LOGS . 'log_' . date('Ymd') . '.log', $content, FILE_APPEND | LOCK_EX);
    }
}
