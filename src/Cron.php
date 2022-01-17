<?php

namespace PequiPHP;

use PequiPHP\Tools\Strings;
use Exception;

class Cron
{
    public static function execute($command, $data = [], $debug = false)
    {
        if (!is_file(PATH_CRON . $command . '.php')) {
            throw new Exception('Arquivo de comando não existe');
        }

        $token = Strings::token();

        if (count($data) > 0) {
            file_put_contents(PATH_TMP . $token . '.tmp', json_encode($data));
        }
        $handle = popen('/usr/bin/php ' . PATH_CRON . $command . '.php "' .  $token . '.tmp' . '" &', 'r');
        if ($debug) {
            $output = '/usr/bin/php ' . PATH_CRON . $command . '.php "' .  $token . '.tmp' . '" &\n';
            if ($handle) {
                while ($tmp = fgets($handle)) {
                    $output .= $tmp;
                }
                $output .= "\n\nResult = " . pclose($handle);
            }
            Log::debug($output);
        } else {
            pclose($handle);
        }
    }

    public static function close($tmpFile)
    {
        @unlink(PATH_TMP . $tmpFile);
    }

    public static function isRuning($command)
    {
        $output = shell_exec('/bin/ps aux');
        if (strpos($output, $command . '.php ') !== false) {
            return true;
        }

        return false;
    }
}
