<?php

namespace app\controller\admin;


use app\model\callback\CallBackMessage;
use app\model\manager\ConfigManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class AdminSettingsController
 * @Inject ConfigManager
 * @package app\controller\admin
 */
class AdminSettingsController extends AdminBaseController {

    const
        CONFIG_FILE = "app/config/config";

    /**
     * @var ConfigManager
     */
    private $configmanager;

    public function onStartup () {
        parent::onStartup();

        $this->configmanager->setConfigFile(self::CONFIG_FILE);
        $this->configmanager->loadConfig();
    }

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $config = $this->configmanager->getConfig();

        $this->data['values'] = $config;

        $this->header['title'] = "Nastavení";
        $this->view = 'settings';
    }

    public function updatePostAjaxAction (IRequest $request) {
        try {
            $key = $request->getPost("key");
            $value = $request->getPost("value");

            $this->configmanager->updateConfig($key, $value);

            $this->configmanager->saveConfig();
            $this->callBack->addMessage(new CallBackMessage("Hodnota byla úspěšně změněna"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function setasdefaultPostAjaxAction (IRequest $request) {
        try {
            $this->configmanager->saveDefaultConfig($request->getPost());
            $this->callBack->addMessage(new CallBackMessage("Nastavení bylo úspěšně uloženo"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }


}
