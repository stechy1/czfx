<?php

namespace app\model\html\element\form\control\input\html5;


use app\model\html\element\form\control\input\AInputControll;

class RangeInput extends AInputControll {

    const TYPE = 'range';

    /**
     * RangeInput constructor.
     * @param string $name Název kontrolky.
     * @param null $value Aktuální hodnota.
     * @param null $min Minimální hodnota.
     * @param null $max Maximální hodnota.
     * @param null $step Krok, o kolik se bude hodnota měnit.
     * @param null $label Popisek.
     */
    public function __construct($name, $value = null,  $min = null, $max = null, $step = null, $label = null) {
        if(!empty($value) && is_numeric($value))
            $this->setValue($value);
        if(!empty($min) && is_numeric($min))
            $this->setMinValue($min);
        if(!empty($max) && is_numeric($max))
            $this->setMaxValue($max);
        if(!empty($step) && is_numeric($step))
            $this->setStep($step);

        return parent::__construct(self::TYPE, $name, $label);
    }
}