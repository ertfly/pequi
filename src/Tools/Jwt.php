<?php

namespace PequiPHP\Tools;

class Jwt
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /*
     * Create a jwt token
     * 
     * @param array $data
     * @return string
     */
    public function encode(array $data): string
    {
        // Header json
        $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);

        // Payload json
        $payload = json_encode($data);

        // Convert then to base64
        $header = $this->base64UrlEncode($header);
        $payload = $this->base64UrlEncode($payload);

        // Creating and converting signature with key
        $signature = hash_hmac('sha256', $header . "." . $payload, $this->secret, true);
        $signature = $this->base64UrlEncode($signature);

        return $header . "." . $payload . "." . $signature;
    }

    /*
     * Validate a jwt token
     * 
     * @param string $token
     * @return bool|array
     */
    public function decode($token)
    {
        if (!empty($token)) {
            $split = explode('.', $token);
            if (count($split) == 3) {

                $signature = hash_hmac('sha256', $split[0] . "." . $split[1], $this->secret, true);
                $bsig = $this->base64UrlEncode($signature);

                if ($bsig == $split[2]) {
                    return json_decode($this->base64UrlDecode($split[1]), true);
                }
            }
        }
        return false;
    }

    /*
     * Private function to encode url base64
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /*
     * Private function to decode url base64
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
