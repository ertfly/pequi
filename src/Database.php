<?php

namespace Pequi;

use Exception;
use PDO;
use Medoo\Medoo;
use MongoDB\Client;

class Database
{
    /**
     * @var Medoo|Client
     */
    private static $instance = [];

    /**
     *
     * @var array
     */
    private static $settings;

    /**
     * @return Medoo|Client
     */
    public static function getInstance($instanceName = 'default')
    {
        if (!is_file(getenv('PATH_ROOT') . 'database.php')) {
            throw new Exception('File database.php not exist.');
        }
        if (!self::$settings) {
            self::$settings = require_once getenv('PATH_ROOT') . 'database.php';
        }

        if (!isset(self::$settings[$instanceName])) {
            throw new Exception('Database instance name not exist.');
        }

        if (!isset(self::$instance[$instanceName])) {
            if (self::$settings[$instanceName]['driver'] != 'mongo') {
                self::$instance[$instanceName] = new Medoo([
                    'database_type' => self::$settings[$instanceName]['driver'],
                    'database_name' => self::$settings[$instanceName]['dbname'],
                    'server' => self::$settings[$instanceName]['host'],
                    'username' => self::$settings[$instanceName]['user'],
                    'password' => self::$settings[$instanceName]['pass'],
                    'port' => self::$settings[$instanceName]['port'],
                    'charset' => self::$settings[$instanceName]['charset'],
                    'option' => [
                        PDO::ATTR_CASE => PDO::CASE_NATURAL,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]
                ]);
            } else {
                $dbname = self::$settings[$instanceName]['dbname'];
                self::$instance[$instanceName] = (new Client(
                    'mongodb://' . self::$settings[$instanceName]['user'] . ':' . self::$settings[$instanceName]['pass'] . '@' . self::$settings[$instanceName]['host'] . ':' . self::$settings[$instanceName]['port'] . '/' . self::$settings[$instanceName]['dbname'] . '?authSource=' . self::$settings[$instanceName]['dbname']
                ))->$dbname;
            }
        }

        return self::$instance[$instanceName];
    }

    public static function closeInstance($instanceName = 'default')
    {
        if (isset(self::$instance[$instanceName])) {
            self::$instance[$instanceName] = null;
            unset(self::$instance[$instanceName]);
        }
    }
}
