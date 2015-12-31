<?php

namespace app\model;


/**
 * Class Message - Třída představující jednu odeslanou zprávu
 * @package model
 */
class Message {

    /**
     * @var int
     */
    private $id;
    /**
     * @var User
     */
    private $from;
    /**
     * @var ConversationRoom
     */
    private $room;
    /**
     * @var string
     */
    private $content;
    /**
     * @var int
     */
    private $time;
    /**
     * @var bool
     */
    private $read;


    /**
     * Message constructor.
     * @param int $id ID zprávy
     * @param User $from Od koho je zpráva
     * @param ConversationRoom $room
     * @param string $content Obsah zprávy
     * @param $time int Čas, kdy byla zpráva poslána
     * @param bool $read True, pokud už je zpráva přečtená, jinak false
     * @internal param User $to Pro koho je zpráva
     */
    public function __construct ($id = -1, User $from, ConversationRoom $room, $content, $time = null, $read = false) {
        $this->id = $id;
        $this->from = $from;
        $this->room = $room;
        $this->content = $content;
        $this->time = (!empty($time)) ? $time : time();
        $this->read = $read;
    }

    /**
     * @return int
     */
    public function getId () {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId ($id) {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getFrom () {
        return $this->from;
    }

    /**
     * @param User $from
     */
    public function setFrom ($from) {
        $this->from = $from;
    }

    /**
     * @return User
     */
    public function getTo () {
        return $this->to;
    }

    /**
     * @param User $to
     */
    public function setTo ($to) {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getContent () {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent ($content) {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getTime () {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime ($time) {
        $this->time = $time;
    }

    /**
     * @return boolean
     */
    public function isRead () {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead ($read) {
        $this->read = $read;
    }



    /**
     * @return array
     */
    public function toArray() {
        return array(
            'message_from' => $this->from->getId(),
            'message_room_id' => $this->room->getId(),
            'message_content' => $this->content,
            'message_time' => $this->time,
            'message_read' => $this->read
        );
    }
}