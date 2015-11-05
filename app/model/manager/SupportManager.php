<?php

namespace app\model\manager;


use app\model\database\Database;
use Exception;

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
     * @param $data array
     * @throws Exception
     */
    public function addReport($data) {
        if($data['antispam'] != date('Y'))
            throw new Exception("Chybně vyplněný antispam");


        $this->database->insert(SupportManager::TBL_NAME, ['report_by' => $_SESSION['user']['user_id'], 'report_message' => $data['message']]);
    }
}