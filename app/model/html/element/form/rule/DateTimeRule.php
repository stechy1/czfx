<?php

namespace app\model\html\element\form\rule;


class DateTimeRule extends ARule {
    
    const RULE = '[0-3]?[0-9]\.[0-1]?[0-9]\.[0-9]{4}\s[0-2]?[0-9]\:[0-5]?[0-9](\:[0-5]?[0-9])?';

    /**
     * DateTimeRule constructor.
     */
    public function __construct() {
        parent::__construct('Hodnota musí být ve formátu: dd.mm.yyyy hh:mm(:ss)');
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