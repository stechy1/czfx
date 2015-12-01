<?php

namespace app\model;

/**
 * Class ReportMessage
 * Představuje zprávu odeslanou uživatelem na podporu
 * @package app\model
 */
class ReportMessage {

    const
        // Připomínka
        TYPE_REMARK = 0,
        // Návrh
        TYPE_FEATURE = 1,
        // Chyba
        TYPE_BUG = 2,
        // Pochvala
        TYPE_SUCCESS = 3;

    private $id;
    private $userID;
    private $userNick;
    private $reportType;
    private $message;
    private $read;
    private $date;

    /**
     * ReportMessage constructor
     *
     * @param int $id int ID zprávy
     * @param $userID int ID uživatele
     * @param $reportType int Typ zprávy
     * @param $message string Obsah zprávy
     * @param int $date Datum odeslání zprávy
     * @param bool $read True, pokud už byla zpráva přečtena. Výchozí je false
     * @param null $userNick Jméno uživatele
     */
    public function __construct ($id = -1, $userID, $reportType, $message, $date, $read = false, $userNick = null) {
        $this->id = $id;
        $this->userID = $userID;
        $this->userNick = $userNick;
        $this->reportType = $reportType;
        $this->message = $message;
        $this->read = $read;
        $this->date = (!empty($date)?$date:time());
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
     * @return int
     */
    public function getUserID () {
        return $this->userID;
    }

    /**
     * @param int $userID
     */
    public function setUserID ($userID) {
        $this->userID = $userID;
    }

    /**
     * @return null
     */
    public function getUserNick () {
        return $this->userNick;
    }

    /**
     * @param null $userNick
     */
    public function setUserNick ($userNick) {
        $this->userNick = $userNick;
    }

    /**
     * @return int
     */
    public function getReportType () {
        return $this->reportType;
    }

    /**
     * @param int $reportType
     */
    public function setReportType ($reportType) {
        $this->reportType = $reportType;
    }

    /**
     * @return string
     */
    public function getMessage () {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage ($message) {
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function isRead () {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead ($read) {
        $this->read = $read;
    }

    /**
     * @return int
     */
    public function getDate () {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate ($date) {
        $this->date = $date;
    }




    /**
     * Převede objekt na pole
     *
     * @return array
     */
    public function toArray() {
        return array(
            'report_by' => $this->userID,
            'report_type' => $this->reportType,
            'report_message' => $this->message,
            'report_read' => $this->read,
            'report_date' => $this->date
        );
    }
}