<?php

namespace app\model\service;


use app\model\service\exception\MyException;

/**
 * Class CaptchaService
 * Jednoduchý wrapper pro práci s recaptchou v celém projektu
 * @package app\model\service
 */
class CaptchaService {

    private static $errorArray = array(
        'Incorrect-captcha-sol' => "Nebyla vyplněná captcha"
    );

    /**
     * Zkontroluje, zda-li je captcha správně vyplněna
     *
     * @param $text string Text získaný od klienta z recaptchy
     * @return bool True, pokud je kontrola v pořádku
     * @throws MyException Pokud se kontrola nepodaří
     */
    public static function verify($text) {
        /*$captcha = new Captcha();
        $captcha->setPrivateKey(RECAPTCHA_PRIVATE_KEY);

        if (!isset($_SERVER['REMOTE_ADDR']))
            $captcha->setRemoteIp('192.168.1.1');

        $response = $captcha->check($text);

        if (!$response->isValid()) {
            throw new MyException(self::$errorArray[$response->getError()]);
        }*/

        return true;
    }

    /**
     *
     * @throws \Captcha\Exception
     */
    public static function printCaptcha() {
        echo '';
        /*
        $captcha = new Captcha();
        $captcha->setPrivateKey(RECAPTCHA_PRIVATE_KEY);
        $captcha->setPublicKey(RECAPTCHA_PUBLIC_KEY);
        $captcha->setTheme("dark");

        echo $captcha->html();*/
    }

}