<?php

namespace app\model\manager;


use app\model\database\Database;
use app\model\factory\UserFactory;
use app\model\Relationship;
use app\model\service\exception\MyException;
use app\model\User;


/**
 * Class RelationshipManager - Správce vztahů mezi uživateli
 * @Inject Database
 * @Inject UserFactory
 * @package model\manager
 */
class RelationshipManager {

    const
        TABLE = "relationships";

    /**
     * @var Database
     */
    private $database;
    /**
     * @var UserFactory
     */
    private $userfactory;

    /**
     * Získá vztah mezi přihlášeným a vybraným uživatelem
     *
     * @param User $friend
     * @return Relationship
     * @throws MyException Pokud
     */
    public function getFriendRelationship (User $friend) {
        $userID = $_SESSION['user']['id'];
        $friendID = $friend->getId();

        if ($userID == $friendID)
            throw new MyException("Nelze získat vzdah mezi jedním a tím samým uživatelem");

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $user = $this->userfactory->getUserFromSession();

        $fromDb = $this->database->queryItself("SELECT relationship_status
            FROM relationships
            WHERE relationship_user_one_id = ? AND relationship_user_two_id = ?", [$userID, $friendID]);

        return new Relationship($user, $friend, $fromDb);
    }

    /**
     * Vytvoří nový požadavek o přátelství
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří vytvořit nový požadavek o přátelství
     */
    public function addFriend (User $friend) {
        $userID = $_SESSION['user']['id'];
        $action_user_id = $userID;
        $friendID = $friend->getId();

        if ($userID == $friendID)
            throw new MyException("Nelze navázat přítelství mezi jedním a tím samým účtem");

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $rel = $this->getFriendRelationship($friend);
        if ($rel->isStatus(Relationship::STATUS_PENDING)) {
            $this->acceptFriendRequest($friend);
        } else {
            $relationship = array(
                'relationship_user_one_id' => $userID,
                'relationship_user_two_id' => $friendID,
                'relationship_status' => Relationship::STATUS_PENDING,
                'relationship_action_user_id' => $action_user_id);
            try {
                $fromDb = $this->database->insert(self::TABLE, $relationship);
            } catch (\PDOException $ex) {
                throw new MyException("Přátelství již existuje, nebo vás uživatel blokuje");
            }

            if (!$fromDb)
                throw new MyException("Nepovedlo se navázat přítelství");
        }
    }

    /**
     * Přijme požadavek o přátelství
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří přijmout požadavek o přátelství
     */
    public function acceptFriendRequest (User $friend) {
        $userID = $_SESSION['user']['id'];
        $friendID = $friend->getId();

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $fromDb = $this->database->update(self::TABLE, ['relationship_status' => Relationship::STATUS_ACCEPTED], "WHERE relationship_user_one_id = ? AND relationship_user_two_id = ? AND relationship_status = ?", [$userID, $friendID, Relationship::STATUS_PENDING]);

        if (!$fromDb)
            throw new MyException("Nepovedlo se potvrdit přátelství");
    }

    /**
     * Zruší žádost o přátelství
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří zrušit žádost o přátelství
     */
    public function declineFriendRequest (User $friend) {
        $userID = $_SESSION['user']['id'];
        $action_user_id = $userID;
        $friendID = $friend->getId();

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $fromDb = $this->database->update(self::TABLE, ['relationship_status' => Relationship::STATUS_DECLINED, 'relationship_action_user_id' => $action_user_id], "WHERE relationship_user_one_id = ? AND relationship_user_two_id = ?", [$userID, $friendID]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se přijmout žádost o přátelství");
    }

    /**
     * Zruší odeslanou žádost o přátelství
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří zrušit podanou žádost o přátelství
     */
    public function cancelFriendRequest (User $friend) {
        $userID = $_SESSION['user']['id'];
        $friendID = $friend->getId();

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $fromDb = $this->database->delete(self::TABLE, "WHERE relationship_user_one_id = ? AND relationship_user_two_id = ? AND relationship_status = ?", [$userID, $friendID, Relationship::STATUS_PENDING]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se zrušit žádost o přátelství");
    }

    /**
     * Odebere uživatele ze seznamu přátel
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří odebrat uživatele ze seznamu přátel
     */
    public function unfriend (User $friend) {
        $userID = $_SESSION['user']['id'];
        $friendID = $friend->getId();

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $fromDb = $this->database->delete(self::TABLE, "WHERE relationship_user_one_id = ? AND relationship_user_two_id = ? AND relationship_status = ?", [$userID, $friendID, Relationship::STATUS_ACCEPTED]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se zrušit žádost o přátelství");
    }

    /**
     * Přidá uživatele do seznamu blokovaných
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří přidat uživatele do seznamu blokovaných
     */
    public function block (User $friend) {
        $userID = $_SESSION['user']['id'];
        $action_user_id = $userID;
        $friendID = $friend->getId();

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $fromDb = $this->database->update(self::TABLE, ['relationship_status' => Relationship::STATUS_BLOCKED, 'relationship_action_user_id' => $action_user_id], "WHERE relationship_user_one_id = ? AND relationship_user_two_id = ?", [$userID, $friendID]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se blokovat přítele");
    }

    /**
     * Odebere uživatele ze seznamu blokovaných
     *
     * @param User $friend
     * @throws MyException Pokud se nepodaří zrušení blokace uživatele
     */
    public function unblockFriend (User $friend) {
        $userID = $_SESSION['user']['id'];
        $friendID = $friend->getId();

        if ($userID > $friendID) {
            $temp = $userID;
            $userID = $friendID;
            $friendID = $temp;
        }

        $fromDb = $this->database->delete(self::TABLE, "WHERE relationship_user_one_id = ? AND relationship_user_two_id = ? AND relationship_status = ?", [$userID, $friendID, Relationship::STATUS_BLOCKED]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se blokovat přítele");
    }

    /**
     * Vrátí uživatele dle typu vazby
     *
     * @param $status int
     * @return array|null
     */
    private function getRelations ($status) {
        $userID = $_SESSION['user']['id'];

        return $this->database->queryAll("SELECT
              IF(u1.user_id != ?, u1.user_id, u2.user_id) AS friend_id,
              IF(u1.user_id != ?, u1.user_nick, u2.user_nick) AS friend_nick,
              IF(u1.user_id != ?, u1.user_avatar, u2.user_avatar) AS friend_avatar
             FROM relationships
             LEFT JOIN users u1 ON u1.user_id = relationship_user_one_id
             LEFT JOIN users u2 ON u2.user_id = relationship_user_two_id
             WHERE (relationship_user_one_id = ? OR relationship_user_two_id = ?) AND relationships.relationship_status = ? AND relationships.relationship_action_user_id = ?", [$userID, $userID, $userID, $userID, $userID, $status, $userID]);
    }

    /**
     * Vrátí všechny přátele
     *
     * @return array|null
     */
    public function getFriendList () {
        $userID = $_SESSION['user']['id'];

        $fromDb = $this->database->queryAll("SELECT
              IF(u1.user_id != ?, u1.user_id, u2.user_id) AS friend_id,
              IF(u1.user_id != ?, u1.user_nick, u2.user_nick) AS friend_nick,
              IF(u1.user_id != ?, u1.user_avatar, u2.user_avatar) AS friend_avatar
             FROM relationships
             LEFT JOIN users u1 ON u1.user_id = relationship_user_one_id
             LEFT JOIN users u2 ON u2.user_id = relationship_user_two_id
             WHERE (relationship_user_one_id = ? OR relationship_user_two_id = ?) AND relationships.relationship_status = ?", [$userID, $userID, $userID, $userID, $userID, Relationship::STATUS_ACCEPTED]);

        return ($fromDb) ? $fromDb : null;
    }

    /**
     * Vrátí všechny žádosti o přátelství od přihlášeného uživatele
     *
     * @return array|null
     */
    public function getMyFriendRequests () {
        $fromDb = $this->getRelations(Relationship::STATUS_PENDING);

        return ($fromDb) ? $fromDb : null;
    }

    /**
     * Vrátí všechny žádosti o přátelství pro přihlášeného uživatele
     *
     * @return array|null
     */
    public function getFriendRequests () {
        $userID = $_SESSION['user']['id'];

        $fromDb = $this->database->queryAll("SELECT
              IF(u1.user_id != ?, u1.user_id, u2.user_id) AS friend_id,
              IF(u1.user_id != ?, u1.user_nick, u2.user_nick) AS friend_nick,
              IF(u1.user_id != ?, u1.user_avatar, u2.user_avatar) AS friend_avatar
             FROM relationships
             LEFT JOIN users u1 ON u1.user_id = relationship_user_one_id
             LEFT JOIN users u2 ON u2.user_id = relationship_user_two_id
             WHERE (relationship_user_one_id = ? OR relationship_user_two_id = ?) AND relationships.relationship_status = ? AND relationships.relationship_action_user_id != ?", [$userID, $userID, $userID, $userID, $userID, Relationship::STATUS_PENDING, $userID]);

        return ($fromDb) ? $fromDb : null;
    }

    /**
     * Vrátí všechny blokované uživatele
     *
     * @return array|null
     */
    public function getBlockedFriends () {
        $fromDb = $this->getRelations(Relationship::STATUS_BLOCKED);

        return ($fromDb) ? $fromDb : null;
    }


}