<?php

namespace app\controller;


use app\model\service\Container;
use app\model\service\request\IRequest;

/**
 * Class RouterController
 * @Inject Container
 * @package app\controller
 */
class RouterController extends BaseController {

    /**
     * @var Container
     */
    private $container;
    /**
     * @var BaseController
     */
    protected $controller;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $controller = $request->getController() . 'controller';
        if ($controller == 'defaultcontroller')
            $this->redirect('index');

        $this->controller = $this->container->getInstanceOf($controller);
        if ($this->controller == null)
            $this->redirect('error');

        if ($request->isAjax())
            $this->controller->callBack = $this->container->getInstanceOf('ajaxcallback');

        $this->controller->onStartup();

        $action = $request->getAction();

        if (!method_exists($this->controller, $action)) {
            if ($request->isAjax()) {
                if ($request->hasPost()) {
                    $action = 'defaultPostAjaxAction';
                } else {
                    $action = 'defaultAjaxAction';
                }
            } else {
                if ($request->hasPost()) {
                    $action = 'defaultPostAction';
                } else {
                    $action = 'defaultAction';
                }
            }
        }

        call_user_func_array(array($this->controller, $action), array($request));

        $this->controller->onExit();

        if ($request->isAjax()) {
            echo $this->controller->callBack->buildMessage();
        } else {
            $this->data['description'] = $this->controller->header['description'];
            $this->data['key_words'] = $this->controller->header['key_words'];
            $this->data['title'] = $this->controller->header['title'];
            $this->data['page'] = $request->getController();

            // Nastavení hlavní šablony
            $this->view = 'base';
        }
    }
}