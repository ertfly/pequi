<?php

namespace PequiPHP\Tools;

use PequiPHP\Constants\ResponseCharset;
use PequiPHP\Constants\ResponseMime;
use Exception;

/**
 * Description of Response
 *
 * @author Eric Teixeira
 */
class Response
{
    public static function html($content, $charset = null)
    {
        if (!isset($charset)) {
            $charset = ResponseCharset::UTF8;
        }
        header('content-type: ' . ResponseMime::HTML . '; charset: ' . $charset);
        echo $content;
        $content = null;
        $charset = null;
        exit();
    }

    public static function css($content)
    {
        header('content-type: ' . ResponseMime::CSS);
        echo $content;
        $content = null;
        exit();
    }

    public static function json($json, $httpCode = 200, $gzip = false)
    {
        $content = json_encode($json);
        header('content-type: ' . ResponseMime::JSON . '; charset: utf-8');
        self::code($httpCode);
        ob_start(($gzip ? 'ob_gzhandler' : null));
        echo $content;
        ob_end_flush();
        $content = null;
        exit(0);
    }

    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * @param    string $uri URL
     * @param    string $method Redirect method
     *            'auto', 'location' or 'refresh'
     * @param    int $code HTTP Response status code
     * @return    void
     */
    public static function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) or !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }

        switch ($method) {
            case 'js':
                exit('<script>document.location.href=\'' . $uri . '\'</script>');
                break;
            case 'refresh':
                header('Refresh:0;url = ' . $uri);
                break;
            default:
                header('Location: ' . $uri, TRUE, $code);
                break;
        }
        exit;
    }

    public static function code($code)
    {
        http_response_code($code);
    }

    public static function printJson($value)
    {
        echo '<pre>';
        echo json_encode($value, JSON_PRETTY_PRINT);
        echo '<pre>';
        exit();
    }
}
