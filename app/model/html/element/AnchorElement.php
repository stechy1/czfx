<?php

namespace app\model\html\element;


use app\model\html\NameValuePair;

class AnchorElement extends AElement {

    const SIGN = 'a';

    /**
     * AnchorElement constructor.
     * @param null $content
     */
    public function __construct($content = null) {
        parent::__construct(self::SIGN, $content);

        return $this;
    }

    public function setLocation($address) {
        $this->addAttribute(new NameValuePair('href', $address));

        return $this;
    }
}