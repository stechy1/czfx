<?php

namespace app\model\manager;


use app\model\database\Database;
use app\model\service\exception\MyException;

/**
 * Class SupportManager
 * @Inject Database
 * @package app\model\manager
 */
class SupportManager {

    const TBL_NAME = 'reports';

    /**
     * @var Database
     */
    private $database;

    /**
     * Přidá report do systému
     *
     * @param $data array
     * @throws MyException Pokud se nahlášení chyby nepodaří
     */
    public function addReport($data) {
        $this->database->insert(SupportManager::TBL_NAME, ['report_by' => $_SESSION['user']['id'], 'report_message' => $data['message']]);
    }
}