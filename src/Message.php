<?php

namespace Pequi;

class Message
{
    const MSG_ERROR = 'danger';
    const MSG_SUCCESS = 'success';
    const MSG_INFO = 'info';
    const MSG_WARNING = 'warning';

    public static $prefix = 'msg_';

    public static function create($type, $msg)
    {
        if (!self::validateType($type)) {
            throw new \Exception('Tipo informado é inválido');
        }
        Session::data(self::$prefix.$type, $msg);
    }

    public static function applyData(&$data)
    {
        if (!isset($data['messages'])) {
            $data['messages'] = array();
        }

        if (Session::item(self::$prefix.self::MSG_ERROR)) {
            $data['messages'][self::MSG_ERROR] = Session::item(self::$prefix.self::MSG_ERROR);
            Session::delete(self::$prefix.self::MSG_ERROR);
        }

        if (Session::item(self::$prefix.self::MSG_SUCCESS)) {
            $data['messages'][self::MSG_SUCCESS] = Session::item(self::$prefix.self::MSG_SUCCESS);
            Session::delete(self::$prefix.self::MSG_SUCCESS);
        }

        if (Session::item(self::$prefix.self::MSG_INFO)) {
            $data['messages'][self::MSG_INFO] = Session::item(self::$prefix.self::MSG_INFO);
            Session::delete(self::$prefix.self::MSG_INFO);
        }

        if (Session::item(self::$prefix.self::MSG_WARNING)) {
            $data['messages'][self::MSG_WARNING] = Session::item(self::$prefix.self::MSG_WARNING);
            Session::delete(self::$prefix.self::MSG_WARNING);
        }
    }

    private static function validateType($type)
    {
        switch ($type) {
            case self::MSG_ERROR:
                return true;
                break;
            case self::MSG_SUCCESS:
                return true;
                break;
            case self::MSG_INFO:
                return true;
                break;
            case self::MSG_WARNING:
                return true;
                break;
            default:
                return false;
                break;
        }
    }
}
