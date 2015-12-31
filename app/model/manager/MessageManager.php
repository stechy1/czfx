<?php

namespace app\model\manager;


use app\model\database\Database;
use app\model\Message;
use app\model\service\exception\MyException;

/**
 * Class MessageManager - Třída obsluhující zprávy mezi uživately
 * @Inject Database
 * @package app\model\manager
 */
class MessageManager {

    const
        TABLE = "messages";

    /**
     * @var Database
     */
    private $database;

    /**
     * Odešle zprávu příjemci
     *
     * @param Message $message
     * @throws MyException Pokud se odeslání nepodaři
     */
    public function send (Message $message) {
        $fromDb = $this->database->insert(self::TABLE, $message->toArray());

        if (!$fromDb)
            throw new MyException("Zprávu se nepodařilo odeslat");
    }

    /**
     * Označí zprávu jako přečtenou
     *
     * @param Message $message
     * @throws MyException Pokud se označení zprávy nepodaří
     */
    public function markAsRead (Message $message) {
        $fromDb = $this->database->update(
            self::TABLE,
            ['message_read' => 1],
            "WHERE message_id = ?",
            [$message->getId()]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se zprávu označit jako přečtenou");
    }
}