<?php

namespace app\model\callback;


class CallBackData {

    private $key;
    private $value;

    /**
     * CallBackData constructor
     *
     * @param $key string Klíč, pod kterým se budou data prezentovat
     * @param $value mixed Jakákoliv hodnota
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /*
     * Vrátí klíč dat.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Vrátí obsah dat
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


}