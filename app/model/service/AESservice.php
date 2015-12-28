<?php

namespace model\service;


use phpseclib\Crypt\AES;

class AESservice {

    public static function encrypt ($text) {
        $cipher = new AES();
        $cipher->setKey(AES_PRIVATE_KEY);
        return $cipher->encrypt($text);
    }

    public static function decrypt ($text) {
        $cipher = new AES();
        $cipher->setKey(AES_PRIVATE_KEY);
        return $cipher->decrypt($text);
    }
}