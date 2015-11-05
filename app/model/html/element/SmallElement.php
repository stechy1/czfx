<?php

namespace app\model\html\element;


class SmallElement extends AElement {

    const SIGN = "small";

    /**
     * DivElement constructor.
     * @param null $content
     */
    public function __construct($content = null) {
        parent::__construct(self::SIGN, $content);

        return $this;
    }
}