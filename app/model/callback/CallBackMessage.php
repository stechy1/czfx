<?php

namespace app\model\callback;


class CallBackMessage {

    const SUCCESS = 0;
    const WARNING = 1;
    const DANGER = 2;
    const INFO = 3;

    private $types = array('success', 'warning', 'danger', 'info');

    private $content;
    private $type;

    /**
     * CallBackMessage constructor.
     * @param $content string Obsah zprávy pro uživatele
     * @param $type int Typ zprávy. Výchozí je SUCCESS.
     */
    public function __construct($content, $type = CallBackMessage::SUCCESS)
    {
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * @return string Vrátí obsah zprávy.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string Vrátí typ zprávy.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array Pøevede zprávu do pole.
     */
    public function toArray() {
        return array('content' => $this->content, 'type' => $this->types[$this->type]);
    }
}