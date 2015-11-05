<?php

namespace app\model\html;


use app\model\html\element\AElement;

class HtmlBuilder {

    private $container = array();
    private $html;

    /**
     * HtmlBuilder constructor.
     * @param $container AElement|string
     */
    public function __construct($container = null) {
        if($container != null)
            $this->container[] = $container;
        $this->html = '';
    }

    /**
     * Přidá element do pole pro sestavení.
     * @param AElement $element HTML prvek.
     */
    public function addElement(AElement $element) {
        $this->container[] = $element;
    }

    /**
     * Sestaví validní html kód pro elementy.
     * @return $this
     */
    public function build() {
        /** @var $element AElement */
        foreach($this->container as $element) {
            $this->html .= $element->render();
        }
        return $this;
    }

    /**
     * Vrátí validní HTML kód prvku.
     * @return string HTML kód.
     */
    public function render() {
        if($this->html == null)
            $this->build();

        return $this->html;
    }

    function __toString() {
        return $this->render();
    }
}