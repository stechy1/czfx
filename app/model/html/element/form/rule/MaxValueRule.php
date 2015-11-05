<?php

namespace app\model\html\element\form\rule;



use model\html\NameValuePair;

class MaxValueRule extends ARule {

    /**
     * MinValueRule constructor.
     * @param int $maxValue Maximální povolené číslo.
     */
    public function __construct($maxValue) {
        parent::__construct('Maximální číslo je: ' . $maxValue, $maxValue);
    }

    /**
     * Zvaliduje hodnotu.
     * @param $value mixed Validovaná hodnota.
     * @return boolean True, pokud je hodnota validní, jinak false.
     */
    public function validateRule($value) {
        return $value <= $this->rule;
    }

    function __toString() {
        return (new NameValuePair('max', $this->rule))->__toString();
    }
}