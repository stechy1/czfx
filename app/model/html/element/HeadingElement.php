<?php

namespace app\model\html\element;


class HeadingElement extends AElement{

    const SIGN = 'h';
    const H1 = 1;
    const H2 = 2;
    const H3 = 3;
    const H4 = 4;
    const H5 = 5;
    const H6 = 6;


    /**
     * HeadingElement constructor.
     * @param $heading int
     * @param null $content
     */
    public function __construct($heading = self::H1, $content = null) {
        parent::__construct(self::SIGN . $heading, $content);

        return $this;
    }

}