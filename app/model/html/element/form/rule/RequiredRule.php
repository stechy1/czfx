<?php

namespace app\model\html\element\form\rule;



/**
 * Class RequiredRule
 * @package model\html\element\form\rule
 * Třída představuje pravidlo povinného vstupního textového pole.
 */
class RequiredRule extends ARule {

    /**
     * RequiredRule constructor.
     */
    public function __construct() {
        return parent::__construct('Povinné pole');
    }

    /**
     * Zvaliduje hodnotu.
     * @param $value mixed Validovaná hodnota.
     * @return boolean True, pokud je hodnota validní, jinak false.
     */
    public function validateRule($value) {
        return !empty($value);
    }

    function __toString() {
        return 'required';
    }


}