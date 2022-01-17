<?php

namespace PequiPHP;

use AnexusPHP\Business\Configuration\Repository\ConfigurationRepository;
use League\Plates\Engine;

class Template
{
    private static $setting;
    private static $locale;

    public static function init($locale = 'pt_BR')
    {
        if (!self::$setting && !self::$locale) {
            self::$locale = $locale;
            self::$setting = @json_decode(ConfigurationRepository::getValue('template_config'), true);
            if (!self::$setting) {
                self::$setting = [];
            }
            if (!isset(self::$setting[self::$locale])) {
                self::$setting[self::$locale] = [];
            }

            $template = ConfigurationRepository::getValue('template');
            $assetsPath = PATH_PUBLIC . 'assets' . DS . 'template' . DS . $template . DS . 'setting' . DS . self::$locale . DS;
            $templateFiles = scandir($assetsPath);
            unset($templateFiles[0]);
            unset($templateFiles[1]);
            if (count($templateFiles) < 2) {
                self::generateFiles();
            }
        }
    }

    public static function getSetting()
    {
        self::init();
        return self::$setting;
    }

    public static function getSettingByKey($name, $defaultValue = null, $isUpload = false)
    {
        self::init();
        if (!isset(self::$setting[self::$locale][$name])) {
            return $defaultValue;
        }

        return !$isUpload ? self::$setting[self::$locale][$name] : upload('template/' . self::$setting[self::$locale][$name]);
    }

    public static function generateFiles()
    {
        $template = ConfigurationRepository::getValue('template');
        $resourcePath = PATH_ROOT . 'src' . DS . 'App' . DS . 'Views' . DS . $template . DS . 'resource';
        $assetsPath = PATH_PUBLIC . 'assets' . DS . 'template' . DS . $template . DS . 'setting' . DS . self::$locale . DS;

        $engine = new Engine($resourcePath, 'css');

        $templateFiles = scandir($resourcePath);
        unset($templateFiles[0]);
        unset($templateFiles[1]);

        if (!empty($templateFiles)) {
            foreach ($templateFiles as $file) {
                $fileParts = explode('.', $file);
                array_pop($fileParts);
                $fileName = implode('.', $fileParts);

                $fileContent = $engine->render($fileName);

                $data = [
                    'file' => $assetsPath . $file,
                    'content' => $fileContent,
                ];
                Cron::execute('SaveTemplateFile', $data);
            }
        }
    }
}
