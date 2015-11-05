<?php

namespace app\model\html\element\form\control\input;



use app\model\html\NameValuePair;

class ButtonInput extends AInputControll {

    const TYPE = 'button';

    /**
     * TextInput constructor.
     * @param string $name Název kontrolky.
     * @param null $label Popisek.
     */
    public function __construct($name, $label = null) {
        $this->setValue($label);
        return parent::__construct(self::TYPE, $name);
    }

    /**
     * Nastaví událost po kliknutí na tlačítko.
     * Předpokládá se javascriptový řetězec, proto je vypnuté escapování.
     * @param string $function
     */
    public function setOnClick ($function) {
        $this->addAttribute(new NameValuePair('onclick', $function, false));
    }

    public final function addRule($rule) {}


}