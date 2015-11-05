<?php

namespace app\model\html\element\form\control\input\html5;


use app\model\html\element\form\control\input\AInputControll;

class DateTimeLocalInput extends AInputControll {

    const TYPE = 'datetime-local';

    /**
     * Month constructor.
     * @param string $name Název kontrolky.
     * @param null $value Aktuální hodnota.
     * @param null $min Minimální hodnota.
     * @param null $max Maximální hodnota.
     * @param null $label Popisek.
     */
    public function __construct($name, $value = null,  $min = null, $max = null, $label = null) {
        if(!empty($value))
            $this->setValue($value);
        if(!empty($min))
            $this->setMinValue($min);
        if(!empty($max))
            $this->setMaxValue($max);

        return parent::__construct(self::TYPE, $name, $label);
    }
}