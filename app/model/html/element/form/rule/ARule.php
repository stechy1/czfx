<?php

namespace app\model\html\element\form\rule;


abstract class ARule {

    //region Konstanty pro pravidla chování kontrolky
    /**
     * Pravidlo pro povinné pole
     */
    const RULE_REQUIRED = 0;
    /**
     * Pravidlo pro maximální délku
     */
    const RULE_MAX_LENGTH = 1;
    /**
     * Pravidlo pro heslo
     */
    const RULE_PASSWORD = 2;
    /**
     * Pravidlo pro datum a čas
     */
    const RULE_DATETIME = 3;
    /**
     * Pravidlo pro regulární výraz
     */
    const RULE_PATTERN = 4;
    /**
     * Pravidlo pro povinný soubor
     */
    const RULE_REQUIRED_FILE = 5;
    /**
     * Regulární výraz pro URL
     */
    const PATTERN_URL = '(http|https)://.*';
    /**
     * Regulární výraz pro celá čísla
     */
    const PATTERN_INTEGER = '[0-9]+';
    /**
     * Reglární výraz pro email
     */
    const PATTERN_EMAIL = '[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,4}$';
    //endregion

    protected $rule;
    protected $errorMessage;

    /**
     * ARule constructor.
     * @param $errorMessage string Chybová zpráva při nesplnění pravidla.
     * @param $rule string Samotné znění pravidla.
     */
    public function __construct($errorMessage, $rule = null) {
        $this->rule = $rule;
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * Vrátí chybovou zprávu pravidla.
     * @return string Chybová zpráva.
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * Zvaliduje hodnotu.
     * @param $value mixed Validovaná hodnota.
     * @return boolean True, pokud je hodnota validní, jinak false.
     */
    abstract public function validateRule($value);

    function __toString() {
        return 'implement rule __toString method';
    }
}