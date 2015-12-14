<?php

namespace app\model;


class User {

    const
        AVATAR_SIZE = 140;

    private $id;
    private $nick;
    private $mail;
    private $role;
    private $online;
    private $lastLogin;
    private $firstLogin;
    private $banned;
    private $activated;

    private $name;
    private $age;
    private $avatar;
    private $region;
    private $city;
    private $motto;
    private $skill;

    /**
     * User constructor.
     * @param $id int ID uživatele.
     * @param $nick string Přezdívka uživatele.
     * @param $mail string E-mail uživatele.
     * @param $role UserRole Role uživatele.
     * @param $online bool True, pokud je uživatel online, jinak false.
     * @param $lastLogin int Poslední přihlášení.
     * @param $firstLogin int První přihlášení.
     * @param $banned bool True, pokud je uživatel zabanován, jinak false.
     * @param $activated bool True, pokud je uživatel prověřený.
     * @param $name string Jméno uživatele
     * @param $age int Věk uživatele
     * @param $avatar string Obrázek uživatele.
     * @param $region string
     * @param $city string Město
     * @param $motto string Motto
     * @param $skill string Skil
     */
    public function __construct ($id, $nick, $mail, $role, $online, $lastLogin, $firstLogin, $banned, $activated, $name, $age, $avatar, $region, $city, $motto, $skill) {
        $this->id = $id;
        $this->nick = $nick;
        $this->mail = $mail;
        $this->role = $role;
        $this->online = $online;
        $this->lastLogin = $lastLogin;
        $this->firstLogin = $firstLogin;
        $this->banned = $banned;
        $this->activated = $activated;

        $this->name = $name;
        $this->age = $age;
        $this->avatar = $avatar;
        $this->region = $region;
        $this->city = $city;
        $this->motto = $motto;
        $this->skill = $skill;
    }


    /**
     * @return int
     */
    public function getId () {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNick () {
        return $this->nick;
    }

    /**
     * @return string
     */
    public function getMail () {
        return $this->mail;
    }

    /**
     * @return UserRole
     */
    public function getRole () {
        return $this->role;
    }

    /**
     * @return boolean
     */
    public function isOnline () {
        return $this->online;
    }

    /**
     * @return int
     */
    public function getLastLogin () {
        return $this->lastLogin;
    }

    /**
     * @return int
     */
    public function getFirstLogin () {
        return $this->firstLogin;
    }

    /**
     * @return boolean
     */
    public function isBanned () {
        return $this->banned;
    }

    /**
     * @return boolean
     */
    public function isActivated () {
        return $this->activated;
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getAge () {
        return $this->age;
    }

    /**
     * @return string
     */
    public function getAvatar () {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getRegion () {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCity () {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getMotto () {
        return $this->motto;
    }

    /**
     * @return string
     */
    public function getSkill () {
        return $this->skill;
    }

    public function toArray() {
        return array(
            'user_id' => $this->id,
            'user_nick' => $this->nick,
            'user_mail' => $this->mail,
            'user_role' => $this->role,
            'user_online' => $this->online,
            'user_last_login' => $this->lastLogin,
            'user_first_login' => $this->firstLogin,
            'user_banned' => $this->banned,
            'user_activated' => $this->activated,

            'user_name' => $this->name,
            'user_age' => $this->age,
            'user_avatar' => $this->avatar,
            'user_region' => $this->region,
            'user_city' => $this->city,
            'user_motto' => $this->motto,
            'user_skill' => $this->skill
        );
    }
}
