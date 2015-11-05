<?php

namespace app\model\html\element\form\rule;


class DisabledRule extends ARule {

    /**
     * DisabledRule constructor.
     */
    public function __construct() {
        parent::__construct('Kontrolka je neaktivní');
    }


    /**
     * Zvaliduje hodnotu.
     * @param $value mixed Validovaná hodnota.
     * @return boolean True, pokud je hodnota validní, jinak false.
     */
    public function validateRule($value) {
        // TODO: Implement validateRule() method.
    }

    public function __toString () {
        return 'disabled';
    }
}