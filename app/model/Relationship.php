<?php

namespace app\model;


/**
 * Class Relationship - Třída představuje vzdat mezi dvěma uživateli
 * @package app\model
 */
class Relationship {

    const
        STATUS_NONE = 0,
        STATUS_PENDING = 1,
        STATUS_ACCEPTED = 2,
        STATUS_DECLINED = 3,
        STATUS_BLOCKED = 4;

    /**
     * @var User
     */
    private $userOne;
    /**
     * @var User
     */
    private $userTwo;
    /**
     * @var int
     */
    private $status;

    /**
     * Relationship constructor
     * @param User $userOne První uživatel, obvykle ten, co je přihlášený
     * @param User $userTwo Druhý uživatel
     */
    public function __construct (User $userOne, User $userTwo, $status) {
        $this->userOne = $userOne;
        $this->userTwo = $userTwo;
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getUserOne () {
        return $this->userOne;
    }

    /**
     * @return User
     */
    public function getUserTwo () {
        return $this->userTwo;
    }

    /**
     * @return int
     */
    public function getStatus () {
        return $this->status;
    }

    /**
     * Zjistí, zda-li status mezi dvěma uživateli odpovídá požadovanému
     *
     * @param $status
     * @return bool True, pokud odpovídá, jinak false
     */
    public function isStatus ($status) {
        return $status == $this->status;
    }

}