<?php


namespace app\model\html\element\form;


interface IFormFactory extends IValidated{

    /**
     * Vyrenderuje formulář.
     * @return string
     */
    public function render();

    /**
     * Metoda zjistí, jestli byl formulář odeslán.
     * @return boolean TRUE, jestli byl formulář odeslán, jinak FALSE.
     */
    public function isPostBack();

}