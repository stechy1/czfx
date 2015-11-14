<?php

namespace app\model\util;


class MyParsedown extends \ParsedownExtra {

    /**
     * @param $Line
     * @param array|null $Block
     * @return array|void
     */
    protected function blockTable ($Line, array $Block = null) {
        $block =  parent::blockTable($Line, $Block);

        if ($block != null) {
            $block['element']['attributes'] = array();
            $block['element']['attributes']['class'] = "table table-striped table-bordered";
        }

        return $block;
    }

    /**
     * @param $Excerpt
     * @return array|void
     */
    protected function inlineImage ($Excerpt) {
        $image =  parent::inlineImage($Excerpt);

        if ($image != null)
            $image['element']['attributes']['class'] = "img-responsive";

        return $image;
    }

    /**
     * @param $Excerpt
     * @return array|void
     */
    protected function inlineLink ($Excerpt) {
        $link = parent::inlineLink($Excerpt);

        if ($link != null)
            $link['element']['attributes']['target'] = "_blank";

        return $link;
    }


}