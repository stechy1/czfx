<?php

namespace app\model\html\element\form\control\input;



class SubmitInput extends AInputControll {

    const TYPE = 'submit';

    /**
     * SubmitInput constructor.
     * @param string $name Název kontrolky.
     * @param null $label Popisek.
     */
    public function __construct($name, $label = null) {
        parent::__construct(self::TYPE, $name);
        $this->setValue($label);
    }

    public final function addRule($rule) {}
}