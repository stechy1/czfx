<?php

namespace app\model\html\element;


class DivElement extends AElement {

    const SIGN = "div";

    /**
     * DivElement constructor.
     * @param null $content
     */
    public function __construct($content = null) {
        parent::__construct(self::SIGN, $content);

        return $this;
    }

}