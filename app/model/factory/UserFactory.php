<?php

namespace app\model\factory;


use app\model\database\Database;
use app\model\service\exception\MyException;
use app\model\User;
use app\model\UserRole;


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
     * @throws MyException Pokud uživatel není přihlášený.
     */
    public function getUserFromSession () {
        if (!isset($_SESSION['user']))
            throw new MyException("Žádný uživatel není přihlášený");

        return $this->getUserByID($_SESSION['user']['id']);
    }

    /**
     * Získá uživatelská data na základě uživatelského ID
     *
     * @param $userID int Uživatelské ID.
     * @return User Novou referenci na uživatele.
     * @throws MyException Pokud uživatelské ID neodpovídá žádnému záznamu.
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
            throw new MyException("Uživatel neexistuje");

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

    /**
     * Získá počet registrovaných uživatelů
     *
     * @return int Počet regisrovaných uživatelů
     */
    public function getUserCount () {
        return $this->database->queryItself("SELECT COUNT(user_id) FROM users");
    }

    /**
     * Vrátí posledních X registrovaných uživatelů
     *
     * @param $page int Aktuální stránka.
     * @param $recordsOnPage int Počet uživatelů, které se mají zobrazit.
     * @return mixed Pole obsahující uživatele.
     * @throws MyException Pokud není nalezen žádný uživatel.
     */
    public function getXUsers($page, $recordsOnPage) {
        $fromDb = $this->database->queryAll("SELECT user_id, user_nick, user_mail, user_first_login, user_last_login, user_banned, user_online, user_activated
                                    FROM users
                                    ORDER BY users.user_first_login, user_activated LIMIT ?, ?", [($page - 1) * $recordsOnPage, $recordsOnPage]);

        if (!$fromDb)
            throw new MyException("Nebyli nalezeni žádní uživatelé");

        return $fromDb;
    }
}