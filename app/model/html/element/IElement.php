<?php

namespace app\model\html\element;


interface IElement {

    /**
     * Nastaví obsah.
     * @param $content AElement|string Obsah elementu.
     * @return $this
     */
    public function addContent($content);

    /**
     * Sestavý validní HTML kód.
     */
    public function build();

    /**
     * @return string Vrátí validní HTML kód, pokud není sestavený, tak ho sestaví.
     */
    public function render();
}