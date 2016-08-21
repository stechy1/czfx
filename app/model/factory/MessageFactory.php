<?php

namespace app\model\factory;


use app\model\ConversationRoom;
use app\model\database\Database;
use app\model\Message;
use app\model\service\exception\MyException;
use app\model\User;
use app\model\util\StringUtils;

/**
 * Class MessageFactory
 * @Inject Database
 * @Inject UserFactory
 * @package app\model\factory
 */
class MessageFactory {

    /**
     * @var Database
     */
    private $database;
    /**
     * @var UserFactory
     */
    private $userfactory;

    /**
     * Vytvoří hash místnosti
     *
     * @param $friendID
     * @return array
     */
    private function createRoomHash ($friendID) {
        $user = $this->userfactory->getUserFromSession();
        $friend = $this->userfactory->getUserByID($friendID);

        $uHash = $user->getHash();
        $fHash = $friend->getHash();

        if ($user->getId() < $friend->getId())
            $hash = $uHash . $fHash; else
            $hash = $fHash . $uHash;

        $userHash = $hash;
        $hash = substr(StringUtils::createHash($hash, $userHash), 10, 74);

        return ['user_hash' => $userHash, 'hash' => $hash];
    }

    /**
     * Vytvoří novou konverzační místnost na základě hashe
     *
     * @param $hashInfo
     * @return int ID vytvořené místnosti
     * @throws MyException
     */
    public function createConversationRoom ($hashInfo) {
        try {
            $fromDb = $this->database->insert("conversation_rooms", ['conversation_room_hash' => $hashInfo['hash'], 'conversation_room_user_hash' => $hashInfo['user_hash']]);
            if (!$fromDb)
                throw new MyException("Nepodařilo se vytvořit novou konverzační místnost");

            return $this->database->getLastId();
        } catch (\PDOException $ex) {
            throw new MyException("Nepodařilo se vytvořit novou konverzační místnost");
        }
    }

    /**
     * Vrátí jednu konverzační místnost na základě členů konverzace
     *
     * @param $param int|string
     * @return ConversationRoom
     * @throws MyException
     */
    public function getConversationRoom ($param) {
        $hash = $param;
        $generateRoomHash = is_numeric($param);
        $hashInfo = array();
        $userHash = null;

        if ($generateRoomHash) {
            $hashInfo = $this->createRoomHash($param);
            $userHash = $hashInfo['user_hash'];
            $hash = $hashInfo['hash'];
        }

        $fromDb = $this->database->queryOne("SELECT conversation_room_id, conversation_room_hash, conversation_room_user_hash
            FROM conversation_rooms
            WHERE conversation_room_hash = ?", [$hash]);

        if (!$fromDb) {
            if ($generateRoomHash) {
                $id = $this->createConversationRoom($hashInfo);
                $fromDb = array('conversation_room_id' => $id, 'conversation_room_hash' => $hash, 'conversation_room_user_hash' => $userHash);
            } else
                throw new MyException("Místnost nebyla nalezena");
        }

        return new ConversationRoom($fromDb['conversation_room_id'], $fromDb['conversation_room_hash'], $fromDb['conversation_room_user_hash']);
    }

    /**
     * Vytvoří novou zprávu
     *
     * @param $content string
     * @param ConversationRoom $room
     * @return Message
     * @throws MyException
     */
    public function getMessageFromRawData ($content, ConversationRoom $room) {
        if (trim($content) == "")
            throw new MyException("Obsah zprávy nemůže být prázdný");

        $user = $this->userfactory->getUserFromSession();

        return new Message(-1, $user, $room, $content);
    }

    /**
     * Vrátí z každé konverzace jednu zprávu
     *
     * @return array|null
     * @throws MyException
     */
    public function getAllMessages () {
        $user = $this->userfactory->getUserFromSession();
        $userHash = $user->getHash();

        $fromDb = $this->database->queryAll(
            "SELECT
              conversation_room_id,
              conversation_room_hash,
              messages.message_id,
              messages.message_content,
              messages.message_time,
              messages.message_read,
              users.user_id,
              users.user_nick,
              users.user_avatar
            FROM conversation_rooms
              LEFT JOIN (messages
                LEFT JOIN users ON users.user_id = messages.message_from) ON messages.message_room_id = conversation_room_id
            WHERE conversation_room_user_hash LIKE ? AND messages.message_id IN (SELECT MAX(message_id)
                                                                                 FROM messages
                                                                                 WHERE message_room_id =
                                                                                       conversation_room_id)
            GROUP BY conversation_room_id
            ORDER BY message_time DESC", ["%$userHash%"]);

        if (!$fromDb)
            throw new MyException("Nemáte žádné zprávy");

        return $fromDb;
    }

    /**
     * Vrátí všechny zprávy s vybraným uživatelem
     *
     * @param ConversationRoom $room
     * @return array|null
     * @throws MyException
     */
    public function getMessages (ConversationRoom $room) {

        $fromDb = $this->database->queryAll("SELECT message_id, message_content, message_time, message_read,
                    users.user_id, users.user_nick, users.user_avatar
            FROM messages
            LEFT JOIN users ON users.user_id = message_from
            WHERE message_room_id = ?
            GROUP BY message_id, message_content, message_time, message_read", [$room->getId()]);

        if (!$fromDb)
            return null;

        return $fromDb;
    }

    public function getMessageFromHash ($hash) {

    }
}