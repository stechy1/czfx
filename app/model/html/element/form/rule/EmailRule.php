<?php

namespace app\model\html\element\form\rule;



class EmailRule extends ARule {

    const RULE = '[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,4}$';

    /**
     * EmailRule constructor.
     */
    public function __construct() {
        parent::__construct('E-mail nemá správný formát');
    }

    /**
     * Zvaliduje hodnotu.
     * @param $value mixed Validovaná hodnota.
     * @return boolean True, pokud je hodnota validní, jinak false.
     */
    public function validateRule($value) {
        return preg_match('~^' . self::RULE . '$~u', $value);
    }

    function __toString() {
        return '';
    }


}