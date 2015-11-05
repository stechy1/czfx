<?php

namespace app\model\html;


final class NameValuePair {

    private $key;
    private $value;
    private $escape;

    /**
     * NameValuePair constructor.
     * @param $key string Klíč.
     * @param $value mixed Hodnota;
     * @param bool $escape True, pokud se má hodnota escapovat, jinak false.
     */
    public function __construct($key, $value, $escape = true) {
        $this->key = $key;
        $this->value = $value;
        $this->escape = $escape;
    }

    /**
     * @return string Vrátí klíč.
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @return mixed Vrátí hodnotu.
     */
    public function getValue() {
        return $this->value;
    }

    function __toString() {
        return $this->key . '="' . (($this->escape)? htmlspecialchars($this->value) : $this->value) . '"';
    }


}