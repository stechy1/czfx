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
     * CallBackMessage constructor
     *
     * @param $content string Obsah zprávy pro uživatele
     * @param $type int Typ zprávy. Výchozí je SUCCESS
     */
    public function __construct($content, $type = CallBackMessage::SUCCESS)
    {
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * Vrátí obsah zprávy
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Vrátí typ zprávy
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Převede zprávu na asociativní pole
     *
     * @return array
     */
    public function toArray() {
        return array('content' => $this->content, 'type' => $this->types[$this->type]);
    }
}