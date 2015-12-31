<?php

namespace app\model;


class User {

    const
        AVATAR_SIZE = 140;

    private $id;
    private $hash;
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
     * @param $hash string Unikátní hash uživatele
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
    public function __construct ($id, $hash, $nick, $mail, $role, $online, $lastLogin, $firstLogin, $banned, $activated, $name, $age, $avatar, $region, $city, $motto, $skill) {
        $this->id = $id;
        $this->hash = $hash;
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
     * @return string
     */
    public function getNick () {
        return $this->nick;
    }

    /**
     * @param string $nick
     */
    public function setNick ($nick) {
        $this->nick = $nick;
    }

    /**
     * @return string
     */
    public function getMail () {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail ($mail) {
        $this->mail = $mail;
    }

    /**
     * @return UserRole
     */
    public function getRole () {
        return $this->role;
    }

    /**
     * @param UserRole $role
     */
    public function setRole ($role) {
        $this->role = $role;
    }

    /**
     * @return boolean
     */
    public function isOnline () {
        return $this->online;
    }

    /**
     * @param boolean $online
     */
    public function setOnline ($online) {
        $this->online = $online;
    }

    /**
     * @return int
     */
    public function getLastLogin () {
        return $this->lastLogin;
    }

    /**
     * @param int $lastLogin
     */
    public function setLastLogin ($lastLogin) {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return int
     */
    public function getFirstLogin () {
        return $this->firstLogin;
    }

    /**
     * @param int $firstLogin
     */
    public function setFirstLogin ($firstLogin) {
        $this->firstLogin = $firstLogin;
    }

    /**
     * @return boolean
     */
    public function isBanned () {
        return $this->banned;
    }

    /**
     * @param boolean $banned
     */
    public function setBanned ($banned) {
        $this->banned = $banned;
    }

    /**
     * @return boolean
     */
    public function isActivated () {
        return $this->activated;
    }

    /**
     * @param boolean $activated
     */
    public function setActivated ($activated) {
        $this->activated = $activated;
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName ($name) {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAge () {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge ($age) {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getAvatar () {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar ($avatar) {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getRegion () {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion ($region) {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getCity () {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity ($city) {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getMotto () {
        return $this->motto;
    }

    /**
     * @param string $motto
     */
    public function setMotto ($motto) {
        $this->motto = $motto;
    }

    /**
     * @return string
     */
    public function getSkill () {
        return $this->skill;
    }

    /**
     * @param string $skill
     */
    public function setSkill ($skill) {
        $this->skill = $skill;
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
