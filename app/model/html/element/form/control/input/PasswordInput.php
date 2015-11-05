<?php

namespace app\model\html\element\form\control\input;


class PasswordInput extends AInputControll {

    const TYPE = 'password';

    /**
     * PasswordInput constructor.
     * @param string $name Název kontrolky.
     * @param null $label Popisek.
     */
    public function __construct($name, $label = null) {
        return parent::__construct(self::TYPE, $name, $label);
    }
}