<?php

namespace Pequi;

use Exception;

class SessionApi
{

    private static $sessionId;
    private static $data = array();
    private static $prefix = 'app_';

    public static function init($prefix, $sessionId)
    {
        self::$prefix = $prefix . '_';
        self::$sessionId = $sessionId;

        if (!is_dir(getenv('PATH_CACHE'))) {
            throw new \Exception('Diretório cache não existe');
        }

        if (!is_readable(getenv('PATH_CACHE'))) {
            throw new \Exception('Diretório cache não tem permissão de escrita');
        }

        if (!is_file(getenv('PATH_CACHE') . self::$sessionId . '_session')) {
            throw new Exception('Session not found');
        }

        self::load();
    }

    public static function create($sessionId)
    {
        if (is_file(getenv('PATH_CACHE') . $sessionId . '_session')) {
            throw new Exception('Session not create beacause file exists.');
        }

        touch(getenv('PATH_CACHE') . $sessionId . '_session');
    }

    public static function exist($sessionId)
    {
        if (!is_file(getenv('PATH_CACHE') . $sessionId . '_session')) {
            return false;
        }

        return true;
    }

    public static function data($name, $value)
    {
        self::$data[self::$prefix . $name] = $value;
        self::save();
    }

    public static function item($name, $delete = false)
    {
        if (!isset(self::$data[self::$prefix . $name])) {
            return false;
        }
        $value = self::$data[self::$prefix . $name];
        if ($delete) {
            self::delete($name);
        }
        return $value;
    }

    public static function delete($name)
    {
        if (!isset(self::$data[self::$prefix . $name])) {
            return;
        }
        unset(self::$data[self::$prefix . $name]);
        self::save();
    }

    private static function load()
    {
        $content = file_get_contents(getenv('PATH_CACHE') . self::$sessionId . '_session');
        if (empty($content)) {
            return;
        }
        self::$data = unserialize($content);
    }

    private static function save()
    {
        file_put_contents(getenv('PATH_CACHE') . self::$sessionId . '_session', serialize(self::$data));
    }

    public static function id()
    {
        return self::$sessionId;
    }

    public static function destroy()
    {
        if (is_file(getenv('PATH_CACHE') . self::$sessionId . '_session')) {
            unlink(getenv('PATH_CACHE') . self::$sessionId . '_session');
        }
    }
}
