<?php

namespace app\controller;


use app\model\service\request\IRequest;

class UserMessageController extends BaseController {

    /**
     * Výchozí reakce kontroleru na ajaxový požadavek
     * @param IRequest $request
     */
    public function defaultAjaxAction (IRequest $request) {
        $messages = $this->getMessages();

        if (!$messages)
            $this->callBack->setFail();
        else
            $this->callBack->addMessages($messages);
    }


}