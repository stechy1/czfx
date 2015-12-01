<?php

namespace app\controller\admin;


use app\controller\BaseController;
use app\model\callback\CallBackMessage;
use app\model\service\exception\MyException;

class AdminBaseController extends BaseController {

    /**
     * AdminBaseController constructor.
     */
    public function __construct () {
        $this->pathToView .= "admin/";
    }

    public function onStartup () {
        try {
            $this->validateUser();
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('login');
        }
    }

    /**
     * Zvaliduje uživatele.
     *
     * @param bool $mustBeActivated True, pokud se vyžaduje aktivovanej učet.
     * @param int $role Volitelný parametr na upřesnění roli uživatele.
     * @return bool True, pokud má uživatel dostatečné oprávnění, jinak false.
     * @throws MyException Pokud uživatel nemá dostatečná oprávnění.
     */
    public function validateUser ($role = null, $mustBeActivated = true) {
        parent::validateUser(USER_ROLE_ADMIN);
    }


}