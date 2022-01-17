<?php

namespace PequiPHP;

use PequiPHP\Constants\ResponseMime;
use PequiPHP\Tools\Response;
use Exception;

class View
{
    private $directory;
    private $fileExtension;
    public function __construct($directory, $fileExtension = 'phtml')
    {
        if (!is_dir($directory)) {
            throw new Exception('Directory view is missing');
        }
        $this->directory = $directory;
        $this->fileExtension = $fileExtension;
    }

    public function render($file, $data = array(), $return = false, $mime = ResponseMime::HTML, $gzip = false)
    {
        foreach ($data as $varName => $varValue) {
            ${$varName} = $varValue;
        }
        $filename = $this->directory . DS . $file . '.' . $this->fileExtension;
        if (!is_file($filename)) {
            throw new Exception('View ' . $filename . ' is missing');
        }
        ob_start(($gzip && !$return ? 'ob_gzhandler' : null));
        include($filename);
        $content = ob_get_contents();
        ob_end_clean();

        $file = null;
        $data = null;
        $filename = null;

        if ($return) {
            return $content;
        }
        if ($mime == ResponseMime::HTML) {
            Response::html($content);
        } else if ($mime == ResponseMime::CSS) {
            Response::css($content);
        } else {
            throw new Exception('Mimetype inválido');
        }
    }
}
