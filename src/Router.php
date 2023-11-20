<?php

namespace Pequi;

use Pequi\Tools\Request;
use Pequi\Tools\Response;
use Pequi\Tools\Strings;
use Exception;
use stdClass;

class Router
{
    private static $settings;
    private static $uri;
    private static $method;
    private static $parameters;
    private static $setting;
    private static $request;

    public static function loadHelper()
    {
        require_once dirname(__FILE__) . '/Helpers.php';
    }

    public static function loadSetting()
    {
        if (is_null(self::$settings)) {
            if (!is_file(getenv('PATH_ROOT') . 'routes.php')) {
                throw new Exception('File /routes.php in PATH_ROOT is missing');
            }
            self::$settings = require_once(getenv('PATH_ROOT') . 'routes.php');
        }
        if (isset(self::$settings['version']) && self::$settings['version'] === 2) {
            $settings = [];
            unset(self::$settings['version']);
            foreach (self::$settings as $key => $items) {
                if (preg_match("/Middleware$/", $key)) {
                    foreach ($items as $controller => $methods) {
                        if (!preg_match("/Controller$/", $controller)) {
                            continue;
                        }
                        foreach ($methods as $uri => $method) {
                            $settings[$uri] = [
                                'middleware' => $key,
                                'controller' => $controller,
                                'method' => $method,
                            ];
                        }
                    }
                    continue;
                }

                if (preg_match("/Controller$/", $key)) {
                    foreach ($items as $uri => $method) {
                        $settings[$uri] = [
                            'controller' => $key,
                            'method' => $method,
                        ];
                    }
                    continue;
                }
            }

            self::$settings = $settings;
        }
    }

    public static function start()
    {
        self::loadHelper();
        self::init();
    }

    private static function init()
    {
        self::loadSetting();
        if (is_null(self::$method)) {
            self::$method = strtolower(Request::getHttpMethod());
        }
        if (is_null(self::$uri)) {
            self::$uri = $_SERVER['REQUEST_URI'];
            self::$uri = Strings::removeInvisibleCharacters(self::$uri, false);
            self::$uri = explode('?', self::$uri);
            self::$uri = '/' . trim(self::$uri[0], '/');
        }
        if (is_null(self::$setting)) {
            if (isset(self::$settings[self::$method . ':' . self::$uri])) {
                self::$setting = self::$settings[self::$method . ':' . self::$uri];
            } else {
                if (isset(self::$settings['all:' . self::$uri])) {
                    self::$setting = self::$settings['all:' . self::$uri];
                } else {
                    $paths = explode('/', self::$uri);
                    foreach (self::$settings as $uri => $setting) {
                        if (!preg_match("/^all\:/", $uri) && strpos($uri, self::$method . ':') !== 0) {
                            continue;
                        }

                        $uri = str_replace(['get:', 'post:', 'put:', 'delete:', 'options:', 'all:'], '', $uri);
                        if (strpos($uri, '{') === false) {
                            continue;
                        }

                        $checkUri = preg_replace("/(\/\{.+\})/", "(/[a-zA-Z\-\.\_0-9]+|.*)", $uri);
                        $checkUri = str_replace('/', '\\/', $checkUri);

                        if (!preg_match("/^{$checkUri}$/", self::$uri)) {
                            continue;
                        }

                        $arrUri = explode('/', $uri);
                        $parameters = [];
                        for ($i = 0; $i < count($arrUri); $i++) {
                            if (isset($paths[$i]) && $arrUri[$i] == $paths[$i]) {
                                continue;
                            }
                            if (isset($setting['validate'])) {
                                if (!preg_match($setting['validate'], $paths[$i])) {
                                    continue;
                                }
                            }
                            $parameters[$arrUri[$i]] = isset($paths[$i]) ? $paths[$i] : null;
                        }

                        if (self::$uri != rtrim(str_replace(array_keys($parameters), array_values($parameters), $uri), '/')) {
                            continue;
                        }

                        self::$parameters = array_values($parameters);
                        self::$setting = $setting;
                    }
                }
            }
        }

        if (!is_null(self::$setting)) {
            self::open();
        } else {
            self::notFound();
        }
    }

    public static function getUri()
    {
        return self::$uri;
    }

    public static function open()
    {
        if (!isset(self::$setting['controller']) || !isset(self::$setting['method'])) {
            throw new Exception('The "controller" is missing');
        }
        if (!class_exists('\\' . self::$setting['controller'])) {
            self::notFound();
        }
        if (isset(self::$setting['middleware'])) {
            if (!class_exists('\\' . self::$setting['middleware'])) {
                throw new Exception('Class middleware ' . self::$setting['middleware'] . ' is missing');
            }
            if (!method_exists('\\' . self::$setting['middleware'], 'handler')) {
                throw new Exception('Method handler is missing');
            }
            if (is_null(self::$request)) {
                self::$request = new stdClass;
            }
            $middleware = '\\' . self::$setting['middleware'];
            $middleware::handler(self::$request);
        }

        $controller = new self::$setting['controller'];
        if (!method_exists($controller, self::$setting['method'])) {
            self::notFound();
        }

        if (is_null(self::$parameters)) {
            self::$parameters = [];
        }
        $method = self::$setting['method'];
        call_user_func_array([$controller, $method], self::$parameters);
    }

    public static function getRequest()
    {
        return self::$request;
    }

    public static function notFound()
    {
        Response::code(404);
        if (isset(self::$settings['404'])) {
            self::$setting = self::$settings['404'];
        } else {
            exit('404 page not found.');
        }
    }

    public static function getUrl($name, array $parameters = null, array $getParams = null)
    {
        self::loadSetting();
        $url = $name;
        foreach (self::$settings as $uri => $setting) {
            if (!isset($setting['name'])) {
                continue;
            }
            if ($setting['name'] != $name) {
                continue;
            }

            $url = explode(':', $uri)[1];
            break;
        }
        $names = explode('/', $url);
        for ($i = 0; $i < count($names); $i++) {
            if (!preg_match("/\{/", $names[$i])) {
                continue;
            }
            $var = str_replace(['{', '}', '?'], '', $names[$i]);
            if (!is_null($parameters) && isset($parameters[$var])) {
                $url = str_replace('{' . $var . '}', $parameters[$var], $url);
                $url = str_replace('{' . $var . '?}', $parameters[$var], $url);
                continue;
            }

            $url = str_replace('{' . $var . '}', '', $url);
            $url = str_replace('{' . $var . '?}', '', $url);
        }

        return '/' . trim($url, '/') . (!is_null($getParams) ? '?' . http_build_query($getParams) : '');
    }
}
