<?php

namespace app\model\html\element\form;


interface IControl extends IValidated{

    /**
     * Nastaví hodnotu kontrolce
     * @param $value mixed Hodnota.
     * @return IControl
     */
    public function setValue($value);

    /**
     * Vrátí hodnotu kontrolky.
     * @return mixed
     */
    public function getValue();
}