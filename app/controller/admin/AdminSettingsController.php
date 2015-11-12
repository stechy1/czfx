<?php

namespace app\controller\admin;


use app\model\service\request\IRequest;

class AdminSettingsController extends AdminBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = "Nastavení";
        $this->view = 'settings';
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