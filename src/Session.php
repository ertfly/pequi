<?php

namespace Pequi;

class Session
{

    private static $sessionId;
    private static $data = array();
    private static $prefix = 'app_';

    public static function init($prefix)
    {
        self::$prefix = $prefix . '_';
        session_start();
        if (!isset($_COOKIE[getenv('SESSION_NAME')])) {
            self::$sessionId = session_id();
            $_COOKIE[getenv('SESSION_NAME')] = self::$sessionId;
        } else {
            self::$sessionId = $_COOKIE[getenv('SESSION_NAME')];
        }

        setcookie(getenv('SESSION_NAME'), self::$sessionId, time() + getenv('SESSION_LIFETIME'), '/' . getenv('SESSION_NAME'));

        if (!is_dir(getenv('PATH_CACHE'))) {
            throw new \Exception('Diretório cache não existe');
        }

        if (!is_readable(getenv('PATH_CACHE'))) {
            throw new \Exception('Diretório cache não tem permissão de escrita');
        }

        if (!is_file(getenv('PATH_CACHE') . self::$sessionId . '_session')) {
            touch(getenv('PATH_CACHE') . self::$sessionId . '_session');
        }

        self::load();
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
        if (isset($_COOKIE[getenv('SESSION_NAME')])) {
            unset($_COOKIE[getenv('SESSION_NAME')]);
        }
        setcookie(getenv('SESSION_NAME'), null, -1, '/' . getenv('SESSION_NAME'));
        if (is_file(getenv('PATH_CACHE') . self::$sessionId . '_session')) {
            unlink(getenv('PATH_CACHE') . self::$sessionId . '_session');
        }
    }
}
