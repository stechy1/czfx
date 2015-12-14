<?php

namespace app\model\factory;


use app\model\database\IDatabase;
use app\model\ReportMessage;
use app\model\service\exception\MyException;

/**
 * Class ReportFactory
 * Továrna na hlášení od uživatelů
 * @Inject Database
 * @package app\model\factory
 */
class ReportFactory {

    /**
     * @var IDatabase
     */
    private $database;

    /**
     * Vytvoří novou zprávu u čistých dat
     *
     * @param $data array['report_type', 'content', 'read']
     * @return ReportMessage
     * @throws MyException Pokud se nepodaří vytvořit objekt představující zprávu
     */
    public function getReportFromRawData ($data) {
        if (!isset($data['read']))
            $data['read'] = false;

        if (!isset($data['time']))
            $data['time'] = time();

        if (!isset($_SESSION['user']['id']))
            throw new MyException("Uživatel není přihlášený");

        if ((empty($data['report_type']) && $data['report_type'] != 0) || empty($data['message']))
            throw new MyException("Nejsou vyplněny všechny položky");

        return new ReportMessage(-1, $_SESSION['user']['id'], $data['report_type'], $data['message'], $data['time'], $data['read']);
    }

    /**
     * Vrátí posledních X článků od přihlášeného uživatele
     *
     * @param $page int Aktuální stránka
     * @param $recordsOnPage int Počet článků, které se mají zobrazit
     * @return mixed Pole obsahující články
     * @throws MyException Pokud není nalezen žádný článek
     */
    public function getXReports ($page, $recordsOnPage) {
        $fromDb = $this->database->queryAll("SELECT report_id, report_by, report_message, report_type, report_read, report_date, user_nick
                                    FROM reports
                                    LEFT JOIN users ON user_id = reports.report_by
                                    ORDER BY report_read, report_date DESC LIMIT ?, ?", [($page - 1) * $recordsOnPage, $recordsOnPage]);

        if (!$fromDb)
            throw new MyException("Zatím nemáte žádné články");

        return $fromDb;
    }
}