<?php

namespace app\model\html\element\form\control;



use app\model\html\element\form\rule\MaxLengthRule;
use app\model\html\NameValuePair;

class TextAreaControl extends AFormControl {

    const SIGN = 'textarea';

    /**
     * TextAreaControl constructor.
     * @param string $name Název kontrolky.
     * @param null $label Popisek.
     */
    public function __construct($name, $label = null) {
        return parent::__construct(self::SIGN, $name, $label);
    }

    public function setValue($value) {
        $this->addContent($value);

        return $this;
    }

    /**
     * Nastaví autofocus po načtení stránky.
     * @return $this Vrátí sám sebe.
     */
    public function setAutofocus() {
        $this->addAttribute('autofocus');

        return $this;
    }

    /**
     * Nastaví viditelnou šířku text area.
     * @param $count
     * @return $this Vrátí sám sebe.
     */
    public function setCols($count) {
        $this->addAttribute(new NameValuePair('cols', $count));

        return $this;
    }

    /**
     * Nastaví maximální počet znaků.
     * @param $maxLength int Maximální počet znaků
     * @return $this Vrátí sám sebe.
     */
    public function setMaxLength($maxLength) {
        $this->addRule(new MaxLengthRule($maxLength));

        return $this;
    }

    /**
     * Nastaví viditelný počet řádek text area.
     * @param $count
     * @return $this Vrátí sám sebe
     */
    public function setRows ($count) {
        $this->addAttribute(new NameValuePair('rows', $count));

        return $this;
    }

    /**
     * Nastaví zalamování řádků.
     * @return $this Vrátí sám sebe.
     */
    public function wrap() {
        $this->addAttribute('wrap');

        return $this;
    }
}