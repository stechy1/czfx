<?php

namespace app\controller\admin;


use app\model\callback\CallBackMessage;
use app\model\service\request\IRequest;

class AdminSettingsController extends AdminBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $config = file_get_contents("app/config/config.json");
        $config = (array) json_decode($config);


        $this->data['values'] = $config;

        $this->header['title'] = "Nastavení";
        $this->view = 'settings';
    }

    public function updatePostAjaxAction (IRequest $request) {
        $config = file_get_contents("app/config/config.json");
        $config = json_decode($config, true);

        $key = $request->getPost("key");
        if ($key == null) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co nastavovat", CallBackMessage::DANGER));
            return;
        }

        $value = $request->getPost("value", $config[$key]["value"]);
        $config[$key]["key"] = $value;

        //file_put_contents("app/config/config.json", json_encode($config));
        $realConfig = "<?php\n";
        foreach($config as $key => $value) {
            $realConfig .= 'define("' . $key . '", ';
            if ($value['type'] == "text")
                $realConfig .= '"';
            $realConfig .= $value['key'];
            if ($value['type'] == "text")
                $realConfig .= '"';
            $realConfig .= ');' . "\n";
        }

        file_put_contents("app/config/config.php", $realConfig);
}
}
// TODO nastavení připojení k databázi
// TODO nastavení zapnutí hezkých url adres
// TODO nastavení veškerých konstant
/*
 * ArticleManagement/ARTICLE_ON_PAGE;
 * Index/ARTICLE_COUNT, POST_COUNT;
 * AdminArticleManager/ARTICLES_ON_PAGE;
 * AdminCategoryManager/CATEGORIES_ON_PAGE;
 * AdminReportManager/REPORTS_ON_PAGE;
 * AdminUserManager/USERS_ON_PAGE;
 */
// TODO nastavení veřejných a privátních klíčů služeb třetích stran
/*
 * RECAPTCHA
 * FACEBOOK
 * GOOGLE
 * TWITTER
 * OPEN_ID
 */