<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\manager\FileManager;
use app\model\service\request\IRequest;
use app\model\util\ParsedownExtra;
use Exception;

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

    public function uploadAjaxAction (IRequest $request) {
        if (!$request->hasFiles()) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Žádný soubor se nepodařilo nahrát", CallBackMessage::DANGER));
        }

        $file = $request->getFile('attachment');
        $tmpName = $file['tmp_name'];
        $name = $file['name'];
        if (isset($_SESSION['storage']) && isset($_SESSION['storage']['article'])) {
            $a = $this->articlefactory->getArticleFromSession();
            $c = $this->categoryfactory->getCategoryFromArticle($a);

            $catDir = $this->filemanager->getDirectory(FileManager::FOLDER_CATEGORY);
            $tmpDir = $this->filemanager->getAttachmentsFolder($catDir . $c->getUrl() . "/" . $a->getUrl());
        }
        else
            $tmpDir = $this->filemanager->getTmpDirectory();
        try {
            move_uploaded_file($tmpName, $tmpDir . $name);
        } catch(Exception $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::INFO));
        }
    }

    public function getAjaxAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->callBack->setFail();
            return;
        }

        $tmpDir = "";
        if (isset($_SESSION['storage'])) {
            if (isset($_SESSION['storage']['article'])) {
                $a = $this->articlefactory->getArticleFromSession();
                $c = $this->categoryfactory->getCategoryFromArticle($a);

                $catDir = $this->filemanager->getDirectory(FileManager::FOLDER_CATEGORY);
                $tmpDir = $this->filemanager->getAttachmentsFolder($catDir . $c->getUrl() . "/" . $a->getUrl());
            }

        } else
            $tmpDir = $this->filemanager->getTmpDirectory();

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

        $convertor = new ParsedownExtra();
        $this->callBack->addData(new CallBackData('text', $convertor->text($text)));
    }
}