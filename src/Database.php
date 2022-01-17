<?php

namespace PequiPHP;

use Exception;
use PDO;
use Medoo\Medoo;
use MongoDB\Client;

class Database
{
    /**
     * @var Medoo
     */
    private static $instance = [];

    /**
     *
     * @var array
     */
    private static $settings;

    /**
     * @return Medoo
     */
    public static function getInstance($instanceName = 'default')
    {
        if (!is_file(PATH_ROOT . 'database.php')) {
            throw new Exception('File database.php not exist.');
        }
        if (!self::$settings) {
            self::$settings = require_once PATH_ROOT . 'database.php';
        }

        if (!isset(self::$settings[$instanceName])) {
            throw new Exception('Database instance name not exist.');
        }

        if (!isset(self::$instance[$instanceName])) {
            if (self::$settings[$instanceName]['driver'] != 'mongo') {
                throw new Exception('This core only database use mongo');
            }

            $dbname = self::$settings[$instanceName]['dbname'];
            self::$instance[$instanceName] = (new Client(
                'mongodb://' . self::$settings[$instanceName]['user'] . ':' . self::$settings[$instanceName]['pass'] . '@' . self::$settings[$instanceName]['host'] . ':' . self::$settings[$instanceName]['port'] . '/' . self::$settings[$instanceName]['dbname'] . '?authSource=' . self::$settings[$instanceName]['dbname']
            ))->$dbname;
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
