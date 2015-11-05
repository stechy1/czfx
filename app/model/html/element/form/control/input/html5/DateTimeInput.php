<?php

namespace app\model\html\element\form\control\input\html5;


use app\model\html\element\form\control\input\AInputControll;
use app\model\html\element\form\rule\DateTimeRule;

class DateTimeInput extends AInputControll {

    const TYPE = 'datetime';

    /**
     * DateTimeInput constructor.
     * @param string $name Název kontrolky.
     * @param null $value Aktuální hodnota.
     * @param null $min Minimální hodnota.
     * @param null $max Maximální hodnota.
     * @param null $label Popisek.
     */
    public function __construct($name, $value = null,  $min = null, $max = null, $label = null) {
        parent::__construct(self::TYPE, $name, $label);
        if(!empty($value))
            $this->setValue($value);
        if(!empty($min))
            $this->setMinValue($min);
        if(!empty($max))
            $this->setMaxValue($max);

        $this->addRule(new DateTimeRule());

        return $this;
    }
}