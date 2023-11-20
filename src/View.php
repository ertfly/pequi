<?php

namespace Pequi;

use Pequi\Constants\ResponseMime;
use Pequi\Tools\Response;
use Exception;

class View
{
    private $data = [];
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

    public function render($file, $data = [], $return = false, $mime = ResponseMime::HTML, $gzip = false)
    {
        $data = array_merge($this->data, $data);
        foreach ($data as $varName => $varValue) {
            ${$varName} = $varValue;
        }
        $filename = $this->directory . '/' . $file . '.' . $this->fileExtension;
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

    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
}
