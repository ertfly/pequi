<?php

namespace Pequi\Tools;

use Pequi\Libraries\FormValidation\FormValidation;
use Pequi\Session;
use Pequi\Tools\Strings;
use Exception;

/**
 * Description of Uri
 *
 * @author Eric Teixeira
 */
class Request
{

    private static $post;

    public static function get($key, $description = null, $validations = null, $options = null)
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        if (is_array($_GET[$key])) {
            self::inputArray($_GET[$key]);
            return $_GET[$key];
        }

        $_GET[$key] = Strings::removeInvisibleCharacters($_GET[$key], FALSE);
        if (trim($_GET[$key]) == '' && !isset($description)) {
            return null;
        }

        if (isset($description) && isset($validations) && is_string($description) && is_array($validations) && count($validations) > 0) {
            $validation = new FormValidation($_GET[$key], $description, $validations, $options);
            if ($validation->hasNewValue()) {
                return $validation->getValue();
            }
        }

        return trim($_GET[$key]);
    }

    public static function gets()
    {
        if (!isset($_GET)) {
            return array();
        }
        self::inputArray($_GET);
        return $_GET;
    }

    private static function inputArray(&$array)
    {
        foreach ($array as &$value) {
            if (!is_array($value)) {
                $value = trim(Strings::removeInvisibleCharacters($value, FALSE));
                continue;
            }
            self::inputArray($value);
        }
    }

    public static function posts()
    {
        if (is_null(self::$post)) {
            self::$post = Form::getPost();
        }
        if (self::$post) {
            $_POST = self::$post;
        }
        if (!isset($_POST)) {
            return array();
        }
        self::inputArray($_POST);
        return $_POST;
    }

    public static function post($key, $description = null, $validations = null, $options = null)
    {
        if (is_null(self::$post)) {
            self::$post = Form::getPost();
        }
        if (self::$post) {
            $_POST = self::$post;
        }

        if (!isset($_POST[$key]) && !isset($description)) {
            return null;
        }

        if (!isset($_POST[$key]) && isset($description)) {
            $_POST[$key] = null;
        }

        if (is_array($_POST[$key])) {
            self::inputArray($_POST[$key]);
            return $_POST[$key];
        }

        $_POST[$key] = Strings::removeInvisibleCharacters($_POST[$key], FALSE);
        if (trim($_POST[$key]) == '' && !isset($description)) {
            return null;
        }

        if (isset($description) && isset($validations) && is_string($description) && is_array($validations) && count($validations) > 0) {
            $validation = new FormValidation($_POST[$key], $description, $validations, $options);
            if ($validation->hasNewValue()) {
                return $validation->getValue();
            }
        }

        return trim($_POST[$key]);
    }

    public static function getHttpMethod()
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            return false;
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getContent()
    {
        $data = file_get_contents('php://input');
        return $data;
    }

    public static function getData()
    {
        $data = file_get_contents('php://input');
        $data = @json_decode($data, true);
        return (isset($data) ? $data : false);
    }

    public static function getHeader($field)
    {
        if (isset($_SERVER['HTTP_' . strtoupper($field)])) {
            return Strings::removeInvisibleCharacters($_SERVER['HTTP_' . strtoupper($field)]);
        }

        if (isset($_SERVER[strtoupper($field)])) {
            return Strings::removeInvisibleCharacters($_SERVER[strtoupper($field)]);
        }

        if (isset($_SERVER[$field])) {
            return Strings::removeInvisibleCharacters($_SERVER[$field]);
        }

        return '';
    }

    public static function sendGetUrlRedirect($url, $ssl = true, $encoded = true, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($ssl === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($encoded === false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new Exception('Ocorreu um erro na sua requisição / Info: ' . json_encode($info, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return array(
            'response' => $response,
            'info' => $info
        );
    }

    /**
     * @param $url
     * @param $data
     * @param array $headers
     * @return array
     * @throws Exception
     */
    public static function sendPostJson($url, $data, $headers = [], $ssl = true, $encoded = true, $timeout = 30)
    {
        $ch = curl_init();
        $postFields = (is_array($data) ? json_encode($data) : $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Strings::escapeSequenceDecode($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($ssl === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($encoded === false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new Exception('Ocorreu um erro na sua requisição / Info: ' . json_encode($info, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return array(
            'response' => $response,
            'info' => $info
        );
    }

    /**
     * @param $url
     * @param array $headers
     * @return array
     * @throws Exception
     */
    public static function sendGetJson($url, $headers = [], $ssl = true, $encoded = true, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($ssl === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($encoded === false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new Exception('Ocorreu um erro na sua requisição / Info: ' . json_encode($info, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return array(
            'response' => $response,
            'info' => $info
        );
    }

    public static function sendDeleteJson($url, $headers = [], $ssl = true, $encoded = true, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($ssl === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($encoded === false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new Exception('Ocorreu um erro na sua requisição / Info: ' . json_encode($info, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return array(
            'response' => $response,
            'info' => $info
        );
    }

    public static function sendDeleteDataJson($url, $data = [], $headers = [], $ssl = true, $encoded = true, $timeout = 30)
    {
        $ch = curl_init();
        $postFields = (is_array($data) ? json_encode($data) : $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, Strings::escapeSequenceDecode($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($ssl === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($encoded === false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new Exception('Ocorreu um erro na sua requisição / Info: ' . json_encode($info, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return array(
            'response' => $response,
            'info' => $info
        );
    }

    public static function sendPutJson($url, $data, $headers = [], $ssl = true, $encoded = true, $timeout = 30)
    {
        $ch = curl_init();
        $postFields = (is_array($data) ? json_encode($data) : $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, Strings::escapeSequenceDecode($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($ssl === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($encoded === false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new Exception('Ocorreu um erro na sua requisição / Info: ' . json_encode($info, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return array(
            'response' => $response,
            'info' => $info
        );
    }

    /**
     * @param $key
     * @param null $description
     * @param null $validations
     * @param null $options
     * @return bool
     * @throws Exception
     */
    public static function json($key, $description = null, $validations = null, $options = null)
    {
        $data = self::getData();
        if (!isset($data[$key]) && !isset($description)) {
            return null;
        }

        if (!isset($data[$key]) && isset($description)) {
            $data[$key] = null;
        }

        if (is_array($data[$key])) {
            self::inputArray($data[$key]);
            return $data[$key];
        }

        $data[$key] = Strings::removeInvisibleCharacters($data[$key], FALSE);
        if (trim($data[$key]) == '' && !isset($description)) {
            return null;
        }

        if (isset($description) && isset($validations) && is_string($description) && is_array($validations) && count($validations) > 0) {
            $validation = new FormValidation($data[$key], $description, $validations, $options);
            if ($validation->hasNewValue()) {
                return $validation->getValue();
            }
        }

        return trim($data[$key]);
    }

    public static function jsons()
    {
        $data = self::getData();
        if ($data === false) {
            return [];
        }

        self::inputArray($data);
        return $data;
    }
}
