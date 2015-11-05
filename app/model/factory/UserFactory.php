<?php

namespace app\model\factory;


use app\model\database\Database;
use app\model\User;
use app\model\UserRole;
use Exception;


/**
 * Class UserFactory
 * @Inject Database
 * @package app\model\factory
 */
class UserFactory {

    /**
     * @var Database
     */
    private $database;
    
    /**
     * Vrátí novou referenci na uživatele ze session.
     *
     * @return User Novou referenci na uživatele ze session.
     * @throws Exception Pokud uživatel není přihlášený.
     */
    public function getUserFromSession () {
        if (!isset($_SESSION['user']))
            throw new Exception("Žádný uživatel není přihlášený");

        return $this->getUserByID($_SESSION['user']['id']);
    }

    /**
     * Získá uživatelská data na základě uživatelského ID
     *
     * @param $userID int Uživatelské ID.
     * @return User Novou referenci na uživatele.
     * @throws Exception Pokud uživatelské ID neodpovídá žádnému záznamu.
     */
    public function getUserByID ($userID) {
        $fromDb = $this->database->queryOne("SELECT user_id, user_nick, user_mail, user_role, user_online, user_first_login,
                      user_last_login, user_banned, user_activated,
                      user_name, user_avatar, user_region, user_city,
                      user_motto, user_skill, user_age
                      FROM users
                      WHERE user_id = ?",
            [$userID]);

        if (!$fromDb)
            throw new Exception("Uživatel neexistuje");

        return new User(
            $fromDb['user_id'],
            $fromDb['user_nick'],
            $fromDb['user_mail'],
            new UserRole($fromDb['user_role']),
            $fromDb['user_online'],
            $fromDb['user_last_login'],
            $fromDb['user_first_login'],
            $fromDb['user_banned'],
            $fromDb['user_activated'],

            $fromDb['user_name'],
            $fromDb['user_age'],
            $fromDb['user_avatar'],
            $fromDb['user_region'],
            $fromDb['user_city'],
            $fromDb['user_motto'],
            $fromDb['user_skill']
        );
    }

}