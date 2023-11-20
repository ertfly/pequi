<?php

namespace Pequi;

use Pequi\Tools\Strings;
use Exception;

class Cron
{
    public static function execute($command, $data = [], $debug = false)
    {
        if (!is_file(getenv('PATH_CRON') . $command . '.php')) {
            throw new Exception('Arquivo de comando não existe');
        }

        $token = Strings::token();

        if (count($data) > 0) {
            file_put_contents(getenv('PATH_TMP') . $token . '.tmp', json_encode($data));
        }
        $strCommand = '/usr/bin/php ' . getenv('PATH_CRON') . $command . '.php' .  (count($data) > 0 ? ' "' . $token . '.tmp"' : '') . ' &';
        $handle = popen($strCommand, 'r');
        if ($debug) {
            $output = $strCommand . '\n';
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
        @unlink(getenv('PATH_TMP') . $tmpFile);
    }

    public static function isRuning($command)
    {
        $output = shell_exec('/bin/ps aux');
        if (strpos($output, $command . '.php ') !== false) {
            return true;
        }

        return false;
    }

    public static function stop($command)
    {
        $strCommand = '/usr/bin/pkill -f "' . getenv('PATH_CRON') . $command . '.php"';
        $handle = popen($strCommand, 'r');
        pclose($handle);
    }
}
