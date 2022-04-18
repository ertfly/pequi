<?php

namespace Pequi\Tools;

class Git
{
    public static function describe()
    {
        $handle = popen('cd ' . PATH_ROOT . ' && git describe | xargs git show -s --format=format:"Author: %an%nDate: %cd"', 'r');
        $output = '';
        while ($tmp = fgets($handle)) {
            $output .= $tmp;
        }
        pclose($handle);
        $handle = null;
        return $output;
    }
}
