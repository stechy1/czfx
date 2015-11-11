<?php

namespace app\model\service;


use Exception;
use ReCaptcha;

/**
 * Class CaptchaService
 * Jednoduchý wrapper pro práci s recaptchou v celém projektu
 * @package app\model\service
 */
class CaptchaService {

    private static $errorArray = array(
        'missing-input' => "Nebyla vyplnená captcha"
    );

    /**
     * Zkontroluje, zda-li je captcha správně vyplněna
     *
     * @param $text string Text získaný od klienta z recaptchy
     * @return bool True, pokud je kontrola v pořádku
     * @throws Exception Pokud se kontrola nepodaří
     */
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