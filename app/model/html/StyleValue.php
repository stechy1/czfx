<?php

namespace app\model\html;


class StyleValue {

    /**
     * @var NameValuePair
     */
    private $nameValuePair;

    /**
     * StyleValue constructor.
     * @param $key string Klíč.
     * @param $value mixed Hodnota;
     */
    public function __construct($key, $value) {
        $this->nameValuePair = new NameValuePair($key, $value);

        return $this;
    }

    /**
     * @return string Vrátí klíč.
     */
    public function getKey() {
        return $this->nameValuePair->getKey();
    }

    /**
     * @return mixed Vrátí hodnotu.
     */
    public function getValue() {
        return $this->nameValuePair->getValue();
    }

    function __toString() {
        return $this->nameValuePair->getKey() . ': ' . $this->nameValuePair->getValue() . '; ';
    }

}