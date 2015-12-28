<?php

namespace app\controller\api;


use app\controller\api\ApiBaseController;
use app\model\manager\ForumManager;
use app\model\service\request\IRequest;
use app\model\snippet\ForumPostSnippet;
use app\model\snippet\PostSnippet;
use model\factory\RenderFactory;

/**
 * Class ApiRenderController
 * @Inject ForumManager
 * @package controller\api
 */
class ApiRenderController extends ApiBaseController {

    /**
     * @var ForumManager
     */
    private $forummanager;

    public function defaultAction (IRequest $request) {
        header('Content-Type:text/plain');
        if (!$request->hasParams(1)) {
            echo "Nebyly nalezeny žádné parametry...";
            exit;
        }

        $params = $request->getParams();

        $renderControl = array_shift($params);

        if (empty($params)) {
            echo "Není uveden další požadovaný parametr...";
            exit;
        }

        switch($renderControl) {
            case "forum-post":
                $postHash = array_shift($params);
                $postArray = $this->forummanager->getPostByHash($postHash);
                $post = new ForumPostSnippet($postArray);
                echo $post->render();
                exit;
                break;

            default:
                echo "Renderovací komponenta nebyla nalezena";
                break;
        };

    }

    public function forumpostAction (IRequest $request) {
        header('Content-Type:application/json');
        if (!$request->hasParams(1)) {
            echo "Nebyly nalezeny žádné parametry...";
            exit;
        }
        $params = $request->getParams();
        array_shift($params);

        $postHash = array_shift($params);
        $postArray = $this->forummanager->getPostByHash($postHash);
        $post = new ForumPostSnippet($postArray);
        $post1= new PostSnippet($postArray);

        $toSend = array();
        $toSend[] = $post->render();
        $toSend[] = $post1->render();

        echo json_encode($toSend);
        exit;
    }


}