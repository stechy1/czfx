<?php

namespace app\model\html\element;


use model\html\NameValuePair;

class ParagraphElement extends AElement {

    const SIGN = "p";

    /**
     * ParagraphElement constructor.
     * @param null $content
     */
    public function __construct($content = null) {
        parent::__construct(self::SIGN, $content);

        return $this;
    }

    /**
     * Nastaví nadpis odstavce.
     * @param $title string Nadpis odstavce.
     * @return $this Vrátí sám sebe.
     */
    public function setTitle($title) {
        $this->addAttribute(new NameValuePair('title', $title));

        return $this;
    }
}