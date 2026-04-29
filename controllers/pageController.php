<?php

namespace Controllers;

class PageController
{
    // Constructor

    public function homePage()
    {
        require_once "../views/pages/default/home.php";
    }
    public function redirectPage()
    {
        require_once "../views/pages/default/redirect.php";
    }
    public function teamPage()
    {
        require_once "../views/pages/historique_equipe/index.php";
    }
    public function matchPage()
    {
        require_once "../views/pages/match_seance/index.php";
    }
    public function traningPage()
    {
        require_once "../views/pages/entrainement/index.php";
    }
    public function rulePage()
    {
        require_once "../views/pages/reglement/index.php";
    }
    public function cotisationPage()
    {
        require_once "../views/pages/cotisation/index.php";
    }
    public function galeryPage()
    {
        require_once "../views/pages/galerie/index.php";
    }
    public function classementPage()
    {
        require_once "../views/pages/classement/index.php";
    }
    public function adminCreateUserPage()
    {
        require_once "../views/pages/admin/create-user.php";
    }
    public function adminPage()
    {
        require_once "../views/pages/admin/index.php";
    }
    public function registerPage()
    {
        require_once "../views/pages/auth/register.php";
    }
    public function loginPage()
    {
        require_once "../views/pages/auth/login.php";
    }


    // les functions pour afficher tous les vues du rubrique matchs
    public function matchListPage()
    {
        require_once "../views/pages/match_seance/list.php";
    }
    public function matchCreatePage()
    {
        require_once "../views/pages/match_seance/create.php";
    }
    public function matchEditPage()
    {
        require_once "../views/pages/match_seance/edit.php";
    }
    public function matchDetailPage()
    {
        require_once "../views/pages/match_seance/detail.php";
    }
    public function matchConvocationsPage()
    {
        require_once "../views/pages/match_seance/convocations.php";
    }
    public function matchIndexPage()
    {
        require_once "../views/pages/match_seance/index.php";
    }
}
