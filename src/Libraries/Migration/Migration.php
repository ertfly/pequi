<?php

namespace Pequi\Libraries\Migration;

use Pequi\Libraries\Migration\MigrationInterface;

class Migration
{
    public static function init(MigrationInterface $config, $id, $db)
    {
        if (!$config->getId()) {
            $config
                ->setId($id)
                ->setValue(0)
                ->setDescription('Versões do migration');
            $config->insert($db);
            include PATH_MIGRATIONS . 'install.php';
        }

        if (is_file(PATH_MIGRATIONS . ($config->getValue() + 1) . '.php')) {
            self::loadScripts($config, $db);
        }
    }

    public static function loadScripts(MigrationInterface $config, $db)
    {
        $version = $config->getValue();
        $check = true;
        while ($check) {
            $version++;
            if (!is_file(PATH_MIGRATIONS . $version . '.php')) {
                break;
            }
            $config->setValue($version);
            $config->update($db);
            include PATH_MIGRATIONS . $version . '.php';
        }
    }
}
