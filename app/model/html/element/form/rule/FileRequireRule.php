<?php

namespace app\model\html\element\form\rule;


/**
 * Class FileRequireRule
 * @package model\html\element\form\rule
 * Třída představuje pravidlo pro povinné přiložení souboru.
 */
class FileRequireRule extends ARule {

    /**
     * RequiredRule constructor.
     */
    public function __construct() {
        return parent::__construct('Soubor je povinný');
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
        return 'required';
    }
}