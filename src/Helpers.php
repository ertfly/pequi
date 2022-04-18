<?php

use Pequi\Router;
use Pequi\Tools\Form;
use Pequi\Tools\Request;
use Pequi\Tools\Response;
use Pequi\Translate;

function url($name, array $parameters = null, array $getParams = null)
{
    return Router::getUrl($name, $parameters, $getParams);
}

function url_absolute($name, array $parameters = null, array $getParams = null)
{
    $protocol = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? "https://" : "http://";
    $domainName = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '/');
    return rtrim($protocol . $domainName . url($name, $parameters, $getParams), '/');
}

function request()
{
    return Router::getRequest();
}

/**
 * @param string|null $index Parameter index name
 * @param string|null $defaultValue Default return value
 * @param array ...$methods Default methods
 * @return \Pecee\Http\Input\InputHandler|array|string|null
 */
function input($index = null, $defaultValue = null, $method)
{
    $value = null;
    if ($index !== null) {
        switch ($method) {
            case 'get':
                $value = Request::get($index);
                break;
            case 'post':
                $value = Request::post($index);
                break;
            case 'json':
                $value = Request::json($index);
                break;
            default:
                $value = Request::get($index);
                break;
        }
        if (!$value) {
            return $defaultValue;
        }
    }
    return $value;
}

/**
 * @param [type] $name
 * @param boolean $isGet
 * @return string|int|array
 */
function input_form($name, $defaultValue = null, $isGet = false)
{
    return Form::input($name, $defaultValue, $isGet);
}

function input_selected($name, $value, $defaultValue = null, $checkGet = false)
{
    return Form::selected($name, $value, $defaultValue, $checkGet);
}

function input_checked($name, $value, $defaultValue = null, $checkGet = false)
{
    return Form::checked($name, $value, $defaultValue, $checkGet);
}

/**
 * @param string $method
 * @param string $index
 * @param string $description
 * @param array $validations
 * @param array $options
 * @return \Pecee\Http\Input\InputHandler|array|string|null
 */
function input_validation($method, $index, $description = null, $validations = [], $options = [])
{
    $value = null;
    if ($index !== null) {
        switch ($method) {
            case 'json':
                $value = Request::json($index, $description, $validations, $options);
                break;
            case 'get':
                $value = Request::get($index, $description, $validations, $options);
                break;
            case 'post':
                $value = Request::post($index, $description, $validations, $options);
                break;
        }
    }
    return $value;
}

function input_json($index, $defaultValue = null)
{
    $value = $defaultValue;
    if ($index !== null) {
        $value = Request::json($index);
    }
    if (!$value) {
        $value = $defaultValue;
    }
    return $value;
}

/**
 * @param string $url
 * @param int|null $code
 */
function redirect($url, $code = null): void
{
    if ($code !== null) {
        Response::code($code);
    }

    Response::redirect($url);
}

/**
 * Undocumented function
 *
 * @param array $data
 * @param integer $code
 * @param string $msg
 * @return array
 */
function responseApi(array $data, $code = 0, $msg = 'Success')
{
    Response::json([
        'response' => [
            'code' => $code,
            'msg' => $msg,
        ],
        'data' => $data,
    ]);
}

/**
 * Undocumented function
 *
 * @param \Exception $e
 * @return array
 */
function responseApiError(\Exception $e)
{
    $code = -1;
    if ($e->getCode() > 0) {
        $code = $e->getCode();
    }
    Response::json([
        'response' => [
            'code' => $code,
            'msg' => $e->getMessage(),
        ],
        'data' => [],
    ]);
}

//Retona a url do asset em questão
function asset($path, $time = true)
{
    $fileUrl = url_absolute('assets/' . $path);
    $fileUrl = rtrim($fileUrl, '/');
    $fileDir = PATH_PUBLIC . 'assets' . DS . $path;

    if ($time && file_exists($fileDir)) {
        $fileUrl .= '?v=' . filemtime($fileDir);
    }

    return $fileUrl;
}

function upload($path, $time = false)
{
    $fileUrl = url_absolute('uploads/' . $path);
    $fileUrl = rtrim($fileUrl, '/');
    $fileDir = PATH_PUBLIC . 'uploads' . DS . $path;

    if ($time && file_exists($fileDir)) {
        $fileUrl .= '?v=' . filemtime($fileDir);
    }

    return $fileUrl;
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function translate($var, $key, $defaultValue = null, $trim = false)
{
    return Translate::get($var, $key, $defaultValue, $trim);
}

/**
 * Recebe uma string xml e formata os dados para retornar 
 * um array contendo os valores (values) e indices (indexes)
 * @param string $data
 * @return array 
 */
function xmlFormatter($data)
{
    $p = xml_parser_create();
    xml_parse_into_struct($p, $data, $values, $indexes);
    xml_parser_free($p);
    return ['values' => $values, 'indexes' => $indexes];
}

/**
 * Undocumented function
 *
 * @param array|null $attr
 * @return string
 */
function htmlAttr(?array $attr)
{
    if (!is_array($attr) || count($attr) == 0) {
        return;
    }

    $str = '';
    foreach ($attr as $k => $v) {
        $str .= ' ' . $k . '="' . $v . '"';
    }

    return $str;
}
