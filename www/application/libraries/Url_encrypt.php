<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Url_encrypt {

    public $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }

    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    function encode($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = URL_ENCRYPT_KEY;
        $secret_iv = URL_ENCRYPT_IV;
        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = $this->base64url_encode($output);
        return $output;
    }

    function decode($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = URL_ENCRYPT_KEY;
        $secret_iv = URL_ENCRYPT_IV;
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $string1 = $this->base64url_decode($string);
        $output = openssl_decrypt($string1, $encrypt_method, $key, 0, $iv);
        return $output;
    }

}

?>