<?php

namespace app\model\html\element;


use model\html\NameValuePair;
use model\html\StyleValue;

interface IDecorative {

    /**
     * Nastaví elementu ID.
     * @param $id string Název IDcka.
     * @return $this
     */
    public function setID($id);

    /**
     * Přidá elementu třídu stylu.
     * @param $class string|array Název třídy.
     * @return $this Vrátí sám sebe.
     */
    public function addClass($class);

    /**
     * Přidá danému elementu styl.
     * @param $style StyleValue|array Nový styl.
     * @return $this Vrátí sám sebe.
     */
    public function addStyle($style);

    /**
     * Přidá danému elementu atribut.
     * @param $attribute NameValuePair|string|array Nový atribut.
     * @return $this Vrátí sám sebe.
     */
    public function addAttribute($attribute);
}