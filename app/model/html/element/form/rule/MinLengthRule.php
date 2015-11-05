<?php

namespace app\html\element\form\rule;


use model\html\element\form\rule\ARule;

class MinLengthRule extends ARule {

    /**
     * RequiredRule constructor.
     * @param int $minLength
     */
    public function __construct($minLength) {
        return parent::__construct('Minimální délka hodnoty je: ' . $minLength, '{' . $minLength . '}');
    }

    /**
     * Zvaliduje hodnotu.
     * @param $value mixed Validovaná hodnota.
     * @return boolean True, pokud je hodnota validní, jinak false.
     */
    public function validateRule($value) {
        return true;
    }

    function __toString() {
        return '';
    }
}