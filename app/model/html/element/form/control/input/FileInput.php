<?php

namespace app\html\element\form\control\input;


use app\model\html\element\form\control\input\AInputControll;
use app\model\html\NameValuePair;

class FileInput extends AInputControll {

    const TYPE = 'fil';

    /**
     * FileInput constructor.
     * @param string $name Název kontrolky.
     * @param null $label Popisek.
     */
    public function __construct($name, $label = null) {
        return parent::__construct(self::TYPE, $name, $label);
    }

    /**
     * Specifikuje typ souboru.
     * @param $accept string Typ souboru
     * @return $this Vrátí sám sebe.
     */
    public function setAccept ($accept) {
        $this->addAttribute(new NameValuePair('accept', $accept));

        return $this;
    }
}