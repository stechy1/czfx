<?php

namespace app\model\service;


use Exception;
use ReCaptcha;

class CaptchaService {

    private static $errorArray = array(
        'missing-input' => "Nebyla vyplnená captcha"
    );

    public static function verify($text) {
        $reCaptcha = new ReCaptcha(RECAPTCHA_KEY);
        $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $text);

        if (!$response->success) {
            $err = (isset(self::$errorArray[$response->errorCodes]))? self::$errorArray[$response->errorCodes] : "Neznámá chyba" ;
            throw new Exception($err);
        }

        return true;
    }

}