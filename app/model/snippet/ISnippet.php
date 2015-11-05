<?php

namespace app\model\snippet;


interface ISnippet {
    /**
     * Sestaví snippet
     * @return ISnippet
     */
    public function build();

    /**
     * Vyrenderuje snippet
     * @return string Validní html kod
     */
    public function render();
}