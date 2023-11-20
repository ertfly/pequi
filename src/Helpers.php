<?php

use Pequi\Router;
use Pequi\Tools\Form;
use Pequi\Tools\Request;
use Pequi\Tools\Response;

function url($name, array $parameters = null, array $getParams = null)
{
    return Router::getUrl($name, $parameters, $getParams);
}

function url_absolute($name = null, $parameters = null, ?array $getParams = null)
{

    $protocol = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? "https://" : "http://";
    $domainName = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '/');
    $base = $protocol . $domainName;
    if (getenv('BASE_URL')) {
        $base = getenv('BASE_URL');
    }
    return rtrim($base . url($name, $parameters, $getParams), '/');
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
    $code = 1;
    if ($e->getCode() > 0) {
        $code = $e->getCode();
    }
    Response::json([
        'response' => [
            'code' => $code,
            'msg' => $e->getMessage(),
        ],
        'data' => null,
    ]);
}

//Retona a url do asset em questão
function asset($path, $time = true)
{
    $fileUrl = url_absolute('assets/' . $path);
    $fileUrl = rtrim($fileUrl, '/');
    $fileDir = getenv('PATH_PUBLIC') . 'assets/' . $path;

    if ($time && file_exists($fileDir)) {
        $fileUrl .= '?v=' . filemtime($fileDir);
    }

    return $fileUrl;
}

function upload($path, $time = false)
{
    $fileUrl = url_absolute('uploads/' . $path);
    $fileUrl = rtrim($fileUrl, '/');
    $fileDir = getenv('PATH_PUBLIC') . 'uploads/' . $path;

    if ($time && file_exists($fileDir)) {
        $fileUrl .= '?v=' . filemtime($fileDir);
    }

    return $fileUrl;
}
