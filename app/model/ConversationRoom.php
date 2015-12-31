<?php

namespace app\model;


/**
 * Class ConversationRoom - Třída představující jednu konverzační místnost
 * @package app\model
 */
class ConversationRoom {

    private $id;
    private $hash;
    private $userHash;

    /**
     * ConversationRoom constructor
     * @param $id int
     * @param $hash string
     * @param $userHash
     */
    public function __construct ($id, $hash, $userHash) {
        $this->id = $id;
        $this->hash = $hash;
        $this->userHash = $userHash;
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
     * @return string
     */
    public function getHash () {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash ($hash) {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getUserHash () {
        return $this->userHash;
    }

    /**
     * @param mixed $userHash
     */
    public function setUserHash ($userHash) {
        $this->userHash = $userHash;
    }


}