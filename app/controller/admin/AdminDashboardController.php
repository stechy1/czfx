<?php

namespace app\controller\admin;


use app\model\service\request\IRequest;

class AdminDashboardController extends AdminBaseController {

    private static $pages = array(
        "user-manager"   =>
            [
                "header" => "Správce uživatelů",
                "descr"  => "Zobrazí přehled všech uživatelů",
                "img"    => "user-manager",
                "img-alt"=> "Správce uživatelů"
            ],
        "report-manager" =>
            [
                "header" => "Správce podpory",
                "descr"  => "Zobrazí přehled všech hlášení od uživatelů",
                "img"    => "report-manager",
                "img-alt"=> "Správce podpory"
            ],
        "user-role"      =>
            [
                "header" => "Správce uživatelských rolí",
                "descr"  => "Zobrazí správce rolí uživatelů",
                "img"    => "role-manager",
                "img-alt"=> "Správce uživatelských rolí"
            ],
        "forum-manager"  =>
            [
                "header" => "Správce fora",
                "descr"  => "Zobřazí správce fora",
                "img"    => "forum-manager",
                "img-alt"=> "Správce fora"
            ],
        "category-manager"=>
            [
                "header" => "Správce kategorii článků",
                "descr"  => "Zobřazí správce kategorii článků",
                "img"    => "category-manager",
                "img-alt"=> "Správce článků"
            ],
        "article-manager"=>
            [
                "header" => "Správce článků",
                "descr"  => "Zobrazí správce článků",
                "img"    => "article-manager",
                "img-alt"=> "Správce článků"
            ],
        "settings"      =>
            [
                "header" => "Nastavení",
                "descr"  => "Zobrazí nastavení systému",
                "img"    => "admin-settings",
                "img-alt"=> "Nastavení"
            ]
    );


    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {

        $this->data['pages'] = self::$pages;

        $this->header['title'] = "Dashboard";
        $this->view = 'dashboard';
    }


}