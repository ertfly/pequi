<?php

namespace Pequi;

use Pequi\Tools\Strings;

class Log
{
    public static function debug($msg)
    {
        @file_put_contents(PATH_LOGS . 'debug_' . date('Ymd\_His') . '_' . Strings::token() . '.log', $msg);
    }

    public static function error($msg)
    {
        @file_put_contents(PATH_LOGS . 'error_' . date('Ymd\_His') . '_' . Strings::token() . '.log', $msg);
    }

    public static function log($msg)
    {
        @file_put_contents(PATH_LOGS . 'log_' . date('Ymd\_His') . '_' . Strings::token() . '.log', $msg);
    }

    public static function user($user, $location, $msg)
    {
        @file_put_contents(PATH_LOGS . 'user_' . $user . '_' . $location . '_' . date('Ymd\_His') . '_' . Strings::token() . '.log', $msg);
    }
}
