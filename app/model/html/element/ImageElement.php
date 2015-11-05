<?php

namespace app\model\html\element;


use app\model\html\NameValuePair;

class ImageElement extends AElement {

    const SIGN = 'img';
    
    /**
     * DivElement constructor.
     * @param null $content
     */
    public function __construct($content = null) {
        parent::__construct(self::SIGN, $content);

        return $this;
    }

    /**
     * Nastaví zdrojovou cestu k obrázku.
     * @param $source string
     * @return $this Vrátí sám sebe
     */
    public function setSource($source) {
        $this->addAttribute(new NameValuePair('src', $source));

        return $this;
    }

    /**
     * Nastaví popis obrázku.
     * @param $alt string
     * @return $this Vrátí sám sebe
     */
    public function setAlt ($alt) {
        $this->addAttribute(new NameValuePair('alt', $alt));

        return $this;
    }

    /**
     * Nastaví šířku obrázku.
     * @param $width int
     * @return $this Vrátí sám sebe
     */
    public function setWidth($width) {
        $this->addAttribute(new NameValuePair('width', $width . 'px'));

        return $this;
    }

    /**
     * Nastaví výšku obrázku.
     * @param $height int
     * @return $this Vrátí sám sebe
     */
    public function setHeight ($height) {
        $this->addAttribute(new NameValuePair('height', $height));

        return $this;
    }
}