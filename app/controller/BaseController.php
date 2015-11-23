<?php

namespace app\controller;


use app\model\callback\AjaxCallBack;
use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\service\request\IRequest;
use app\model\service\exception\MyException;


/**
 * Class BaseController
 * @Inject UserFactory
 * @package app\controller
 */
abstract class BaseController {

    const
        DEF_PATH_TO_VIEW = "app/view/";

    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @property $data array  Pole, jehož indexy jsou poté viditelné v šabloně jako běžné proměnné
     */
    protected $data = array();
    /**
     * @property $view string Název šablony bez přípony
     */
    protected $view = "";
    /**
     * @property $header PageHeader Hlavička HTML stránky
     */
    protected $header = array('title' => '', 'key_words' => '', 'description' => '');
    /**
     * @var AjaxCallBack
     */
    protected $callBack;
    /**
     * @var string
     */
    protected $pathToView = self::DEF_PATH_TO_VIEW;

    /**
     * Ošetří proměnnou pro výpis do HTML stránky
     *
     * @param null $x Proměnná pro ošetření
     * @return array|string|null
     */
    private function check($x = null)
    {
        if (!isset($x))
            return null;
        elseif (is_string($x))
            return htmlspecialchars($x, ENT_QUOTES);
        elseif (is_array($x)) {
            foreach ($x as $k => $v)
                $x[$k] = $this->check($v);
            return $x;
        } else
            return $x;
    }

    /**
     * Vyrenderuje pohled
     */
    public function renderView () {
        if ($this->view) {
            extract($this->check($this->data));
            extract($this->data, EXTR_PREFIX_ALL, "");
            require($this->pathToView . $this->view . ".phtml");
        }
    }

    /**
     * Přidá zprávu pro uživatele
     *
     * @param CallBackMessage $callBackMessage Zpráva pro uživatele
     */
    public function addMessage (CallBackMessage $callBackMessage) {
        if (isset($_SESSION['messages']))
            $_SESSION['messages'][] = serialize($callBackMessage); else
            $_SESSION['messages'] = array(serialize($callBackMessage));
    }

    /**
     * @param $messages array Pole zpráv pro uživatele.
     */
    public function addMessages ($messages) {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * Vrátí zprávy pro uživatele
     *
     * @return array Pole zpráv
     */
    public function getMessages () {
        if (isset($_SESSION['messages'])) {
            $messages = $_SESSION['messages'];
            unset($_SESSION['messages']);
            $data = array();
            foreach ($messages as $message)
                $data[] = unserialize($message);

            return $data;
        } else
            return array();
    }

    /**
     * Přesměruje na dané URL
     *
     * @param $url string Dané url
     */
    public function redirect ($url) {
        if (defined("UGLY_URL") && UGLY_URL == "true")
            $url = 'index.php?c=' . $url;
        header("Location: /$url");
        header("Connection: close");
        exit;
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
        $user = null;
        try {
            $user = $this->userfactory->getUserFromSession();
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage("Nejste přihlášen!", CallBackMessage::DANGER));
            $this->redirect("login");
        }

        if (!$user->isActivated()) {
            if ($mustBeActivated) {
                $this->addMessage(new CallBackMessage("Účet musíte nejdříve ověřit", CallBackMessage::INFO));
                $this->redirect("check-code");
            }
            $this->addMessage(new CallBackMessage("Účet není ověřený", CallBackMessage::INFO));
        }


        if (!$role)
            return true;

        return $user->getRole()->valid($role);
    }

    /**
     * Provede se před hlavním zpracováním požadavku v kontroleru
     */
    public function onStartup () {
    }

    /**
     * Provede se po zpracování hlavního požadavku v kontroleru
     */
    public function onExit () {
    }

    /**
     * Výchozí akce kontroleru
     *
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     *
     * @param IRequest $request
     */
    public function defaultPostAction (IRequest $request) {
        $this->redirect('index');
    }

    /**
     * Výchozí reakce kontroleru na ajaxový požadavek
     *
     * @param IRequest $request
     */
    public function defaultAjaxAction (IRequest $request) {
        $this->callBack->setFail();
    }

    /**
     * Výchozí reakce kontroleru na ajaxový požadavek s postem
     *
     * @param IRequest $request
     */
    public function defaultPostAjaxAction (IRequest $request) {
        $this->callBack->setFail();
    }
}