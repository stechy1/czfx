<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\manager\FileManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\util\StringUtils;
use ParsedownExtra;

/**
 * Class EditorController
 * @Inject FileManager
 * @Inject ArticleFactory
 * @Inject CategoryFactory
 * @package app\controller
 */
class EditorController extends BaseController {

    /**
     * @var FileManager
     */
    private $filemanager;
    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var CategoryFactory
     */
    private $categoryfactory;

    private function getTempDir($action) {
        switch ($action) {
            case "attachments":
                if (isset($_SESSION['storage']) && isset($_SESSION['storage']['article'])) {
                    $a = $this->articlefactory->getArticleFromSession();
                    $c = $this->categoryfactory->getCategoryFromArticle($a);

                    $catDir = $this->filemanager->getDirectory(FileManager::FOLDER_CATEGORY);
                    return $this->filemanager->getAttachmentsFolder($catDir . $c->getUrl() . "/" . $a->getUrl());
                }
                break;

        }

        return $this->filemanager->getTmpDirectory();
    }

    public function uploadAjaxAction (IRequest $request) {
        if (!$request->hasFiles()) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Žádný soubor se nepodařilo nahrát", CallBackMessage::DANGER));
        }

        $params = $request->getParams();
        array_shift($params);
        if (empty($params)) {$this->callBack->setFail(); return;}
        $action = array_shift($params);

        $file = $request->getFile('file');
        $tmpName = $file['tmp_name'];
        $name = $file['name'];
        $tmpDir = $this->getTempDir($action);
        try {
            $this->filemanager->moveUploadedFiles($tmpName, $tmpDir . $name);
        } catch(MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::INFO));
        }
    }

    public function removeAjaxAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        if (empty($params)) {$this->callBack->setFail(); return;}
        $action = array_shift($params);
        $tmpDir = $this->getTempDir($action);

        if (empty($params)) {
            $this->callBack->setFail();
            return;
        }

        switch ($action) {
            case "attachments":

                $attachment = array_shift($params);
                if (empty($params)) {$this->callBack->setFail(); return;}
                $extension = array_shift($params);
                $attachment = StringUtils::removeDangerousLetters($attachment);

                $file = $tmpDir . $attachment . "." . $extension;
                if (!$this->filemanager->recursiveDelete($file)) {
                    $this->callBack->setFail();
                    $this->callBack->addMessage(new CallBackMessage("Soubor se nepodařilo smazat", CallBackMessage::INFO));
                }
                break;

        }
    }

    public function getAjaxAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        if (empty($params)) {$this->callBack->setFail(); return;}
        $action = array_shift($params);

        $tmpDir = $this->getTempDir($action);
        $files = array_values(FileManager::getFilesFromDirectory($tmpDir));
        if (empty($files)) {
            $this->callBack->setFail();
            return;
        }

        $relPath = $this->filemanager->getRelativePath($tmpDir);

        $this->callBack->addData(new CallBackData('files', $files));
        $this->callBack->addData(new CallBackData('path', $relPath));
    }

    public function convertPostAjaxAction (IRequest $request) {
        if (!$request->hasPost()) {
            $this->callBack->setFail();
            return;
        }

        $text = $request->getPost('text');
        if ($text == null) {
            $this->callBack->setFail();
            return;
        }

        $this->callBack->addData(new CallBackData('text', ParsedownExtra::instance()->text($text)));
    }
}