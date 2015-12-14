<?php

namespace app\model\manager;


use app\model\database\Database;
use app\model\ReportMessage;
use app\model\service\exception\MyException;

/**
 * Class SupportManager
 * @Inject Database
 * @package app\model\manager
 */
class SupportManager {


    /**
     * @var Database
     */
    private $database;

    /**
     * Přidá report do systému
     *
     * @param ReportMessage $message
     * @throws MyException
     */
    public function addReport(ReportMessage $message) {
        $fromDb = $this->database->insert("reports", $message->toArray());

        if (!$fromDb)
            throw new MyException("Nepovedlo se přidat zprávu");

    }

    /**
     * Označí zprávu za přečtenou
     *
     * @param $id int ID označené zprávy
     * @throws MyException Pokud se označení zprávy nepodaří
     */
    public function markAsRead($id) {
        $arr = ["report_read" => 1];
        $fromDb = $this->database->update("reports", $arr, "WHERE report_id = ?", [$id]);

        if (!$fromDb)
            throw new MyException("Nepovedlo se označit zprávu za přečtenou");
    }

    /**
     * Vrátí počet všech zpráv
     *
     * @return int
     */
    public function getCount() {
        return $this->database->queryItself("SELECT COUNT(report_id) FROM reports");
    }

    /**
     * Vrátí počet nepřečtených zpráv
     * @return int
     */
    public function getUnreadedCount() {
        return $this->database->queryItself("SELECT COUNT(report_id) FROM reports WHERE report_read = 0");
    }

    /**
     * Odstraní zprávu z databáze
     *
     * @param $repID int ID zprávy
     * @throws MyException Pokud se nepodaři zprávu odstranit
     */
    public function delete ($repID) {
        $fromDb = $this->database->delete("reports", "WHERE report_id = ?", [$repID]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se zprávu odstranit");
    }
}