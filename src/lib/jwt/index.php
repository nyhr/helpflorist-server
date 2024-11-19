<?php

class JWT {
    private $secret;

    public function __construct() {
        $settings = parse_ini_file(__DIR__ . "/../../../settings.ini", true);
        $this->secret = $settings["jwt"]["secret"];
    }

    // Helper function to Base64URL encode
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Helper function to Base64URL decode
    private function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function createToken($payload) {
        $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac("sha256", $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function verifyAndRefreshToken($token) {
        if($this->verifyToken($token)) {
            $payload = $this->getPayload($token);
            $payload["exp"] = time() + 3600;
            return $this->createToken($payload);
        }
        return false;
    }

    public function verifyToken($token) {
        $parts = explode(".", $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
        $signature = $this->base64UrlDecode($base64UrlSignature);

        $expectedSignature = hash_hmac("sha256", $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);

        return hash_equals($expectedSignature, $signature);
    }

    public function getPayload($token) {
        $parts = explode(".", $token);
        if (count($parts) !== 3) {
            return null;
        }
        $base64UrlPayload = $parts[1];
        $payload = $this->base64UrlDecode($base64UrlPayload);
        return json_decode($payload, true);
    }

    public function getHeader($token) {
        $parts = explode(".", $token);
        if (count($parts) !== 3) {
            return null;
        }
        $base64UrlHeader = $parts[0];
        $header = $this->base64UrlDecode($base64UrlHeader);
        return json_decode($header, true);
    }

    public function getSignature($token) {
        $parts = explode(".", $token);
        if (count($parts) !== 3) {
            return null;
        }
        return $parts[2];
    }
}

?>
