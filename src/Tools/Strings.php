<?php

namespace Pequi\Tools;

use Exception;

class Strings
{
    public static function token()
    {
        mt_srand();
        return strtoupper(hash('sha256', uniqid(mt_rand())));
    }

    public static function uriToClassName($uri)
    {
        if (!preg_match("/^[a-z0-9~%.:_\-]+$/i", $uri)) {
            throw new Exception('Existem caracteres na url são inválidos');
        }
        $uri = str_replace('-', ' ', $uri);
        $uri = ucwords($uri);
        return str_replace(' ', '', $uri);
    }

    public static function removeInvisibleCharacters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/i'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i'; // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    public static function formatCpfCnpj($cpfCnpj)
    {
        $cpfCnpj = self::onlyNumber($cpfCnpj);
        if (strlen($cpfCnpj) == 11) {
            return preg_replace("/([\d]{3})([\d]{3})([\d]{3})([\d]{2})/", "$1.$2.$3-$4", $cpfCnpj);
        } else if (strlen($cpfCnpj) == 14) {
            return preg_replace("/([\d]{2})([\d]{3})([\d]{3})([\d]{4})([\d]{2})/", "$1.$2.$3/$4-$5", $cpfCnpj);
        }
        return $cpfCnpj;
    }

    public static function uri($str, $separator = '-', $lowercase = FALSE)
    {
        if ($separator === 'dash') {
            $separator = '-';
        } elseif ($separator === 'underscore') {
            $separator = '_';
        }

        $q_separator = preg_quote($separator, '#');

        $trans = array(
            '&.+?;' => '',
            '[^\w\d _-]' => '',
            '\s+' => $separator,
            '(' . $q_separator . ')+' => $separator
        );

        $str = strip_tags($str);
        foreach ($trans as $key => $val) {
            $str = preg_replace('#' . $key . '#iu', $val, $str);
        }

        if ($lowercase === TRUE) {
            $str = strtolower($str);
        }

        return trim(trim($str, $separator));
    }

    public static function removeAccents($string = NULL)
    {
        $find = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í',
            'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á',
            'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô',
            'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', '}', ']', '°', '+', '(', ')', '*', '#', '@', '!', '#', '$', '%', '¨', ':', '’', '‘', ',', '.', ':', 'º'
        );
        $replace = array(
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i',
            'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 's', 'a', 'a',
            'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o',
            'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', ' ', '', '', ''
        );
        return str_replace($find, $replace, $string);
    }

    public static function lastUri($uri)
    {
        $arr = explode('/', $uri);
        return $arr[count($arr) - 1];
    }

    public static function smartToken($length = 11)
    {
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len = strlen($salt);
        $pass = '';
        mt_srand(10000000 * (float)microtime());
        for ($i = 0; $i < $length; $i++) {
            $pass .= $salt[mt_rand(0, $len - 1)];
        }
        return $pass;
    }

    public static function validUsername($username)
    {
        if (preg_match("/[^a-zA-Z0-9]/", $username)) {
            return false;
        }
        return true;
    }

    public static function validateZipCode($zipCode)
    {
        $zipCode = preg_replace("/[\D]/", "", $zipCode);
        if (strlen($zipCode) != 8) {
            return false;
        }
        return true;
    }

    public static function onlyNumber($str)
    {
        return preg_replace("/[^0-9]/", "", $str);
    }

    public static function classToClass($classname)
    {
        $arr = explode('\\', $classname);
        unset($arr[count($arr) - 1]);
        return implode('\\', $arr);
    }

    /* public static function classToPath($classname)
    {
        return PATH_ROOT . 'src' . DS . str_replace('\\', DS, $classname) . DS;
    } */

    public static function escapeSequenceDecode($str)
    {

        // [U+D800 - U+DBFF][U+DC00 - U+DFFF]|[U+0000 - U+FFFF]
        $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})
              |\\\u([\da-fA-F]{4})/sx';

        return preg_replace_callback($regex, function ($matches) {

            if (isset($matches[3])) {
                $cp = hexdec($matches[3]);
            } else {
                $lead = hexdec($matches[1]);
                $trail = hexdec($matches[2]);

                // http://unicode.org/faq/utf_bom.html#utf16-4
                $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
            }

            // https://tools.ietf.org/html/rfc3629#section-3
            // Characters between U+D800 and U+DFFF are not allowed in UTF-8
            if ($cp > 0xD7FF && 0xE000 > $cp) {
                $cp = 0xFFFD;
            }

            // https://github.com/php/php-src/blob/php-5.6.4/ext/standard/html.c#L471
            // php_utf32_utf8(unsigned char *buf, unsigned k)

            if ($cp < 0x80) {
                return chr($cp);
            } else if ($cp < 0xA0) {
                return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
            }

            return html_entity_decode('&#' . $cp . ';');
        }, $str);
    }

    public static function password($str)
    {
        return mb_convert_case(hash('sha256', $str), MB_CASE_UPPER);
    }

    public static function prepareEmailTemplate($subject, $content, $vars)
    {
        $variables = [];
        foreach ($vars as $key => $value) {
            $variables['{{' . $key . '}}'] = $value;
        }

        $subject = str_replace(array_keys($variables), array_values($variables), $subject);
        $body = str_replace(array_keys($variables), array_values($variables), $content);

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }

    public static function base64ToFile($fileEncoded, $pathDest, array $checkMimes, $errorMsg)
    {
        $mimeEncoded = substr($fileEncoded, 0, strpos($fileEncoded, ';'));
        $mimeEncoded = str_replace('data:', '', $mimeEncoded);

        if (!in_array($mimeEncoded, $checkMimes)) {
            throw new \Exception($errorMsg . ' (Solicitado: ' . $mimeEncoded . ', Disponível: ' . implode(', ', $checkMimes) . ')');
        }

        $decoded = substr($fileEncoded, strpos($fileEncoded, ',') + 1);
        $decoded = str_replace(' ', '+', $decoded);

        $extensions = array(
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'application/pdf' => 'pdf',
            'video/mp4' => 'mp4',
        );
        if (!isset($extensions[$mimeEncoded])) {
            throw new Exception('Mimetype não disponível para upload');
        }
        $filename = Strings::token() . '.' . $extensions[$mimeEncoded];
        @file_put_contents($pathDest . $filename, base64_decode($decoded));
        if (!is_file($pathDest . $filename)) {
            throw new \Exception('Ocorreu um erro ao realizar o upload do arquivo, favor tentar novamente.');
        }

        return $filename;
    }

    public static function formatarCep($cep)
    {
        $cep = self::onlyNumber($cep);
        if (strlen($cep) == 8) {
            return preg_replace("/([\d]{2})([\d]{3})([\d]{3})/", "$1.$2-$3", $cep);
        }
        return '';
    }

    public static function formatarTelefoneOuCelular($telefoneOuCelular)
    {
        $telefoneOuCelular = self::onlyNumber($telefoneOuCelular);
        if (strlen($telefoneOuCelular) == 10) {
            return preg_replace("/([\d]{2})([\d]{4})([\d]{4})/", "($1) $2-$3", $telefoneOuCelular);
        } else if (strlen($telefoneOuCelular) == 11) {
            return preg_replace("/([\d]{2})([\d]{5})([\d]{4})/", "($1) $2-$3", $telefoneOuCelular);
        }
        return '';
    }
}
