<?php

namespace app\model\html\element\form\control\input\html5;


use app\model\html\element\form\control\input\AInputControll;

class ColorInput extends AInputControll {

    const TYPE = 'color';

    /**
     * ColorInput constructor.
     * @param string $name Název kontrolky.
     * @param null $label Popisek.
     */
    public function __construct($name, $label = null) {
        return parent::__construct(self::TYPE, $name, $label);
    }
}