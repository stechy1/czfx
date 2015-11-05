<?php

namespace app\model\html\element\form\control\input;



class HiddenInput extends AInputControll {

    const TYPE = 'hidden';

    /**
     * HiddenInput constructor.
     * @param string $name Název kontrolky.
     */
    public function __construct($name) {
        parent::__construct(self::TYPE, $name);

        return $this;
    }
}