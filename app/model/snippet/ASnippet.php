<?php

namespace app\model\snippet;


abstract class ASnippet implements ISnippet {

    protected $html;

    /**
     * Render the snippet.
     * @return string Valid html code.
     */
    public function render() {
        if($this->html == null)
            $this->build();

        return $this->html;
    }

    public function __toString() {
        return $this->render();
    }

}