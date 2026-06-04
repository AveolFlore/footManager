<?php

namespace Controllers;

require_once __DIR__ . '/../middleware/Admin.php';

class PageController
{
    // Constructor

    public function homePage()
    {
        require_once __DIR__ . '/../views/pages/default/home.php';
    }
    public function redirectPage()
    {
        require_once __DIR__ . '/../views/pages/default/redirect.php';
    }
    public function teamPage()
    {
        require_once __DIR__ . '/../views/pages/historique_equipe/index.php';
    }
    public function matchPage()
    {
        require_once __DIR__ . '/../views/pages/match_seance/index.php';
    }
    public function traningPage()
    {
        require_once __DIR__ . '/../views/pages/entrainement/index.php';
    }
    public function rulePage()
    {
        require_once __DIR__ . '/../views/pages/reglement/index.php';
    }
    public function cotisationPage()
    {
        require_once __DIR__ . '/../views/pages/cotisation/index.php';
    }
    public function galeryPage()
    {
        require_once __DIR__ . '/../views/pages/galerie/index.php';
    }
    public function classementPage()
    {
        require_once __DIR__ . '/../views/pages/classement/index.php';
    }
    public function adminCreateUserPage()
    {
        requireAdmin();
        require_once __DIR__ . '/../views/pages/admin/create-user.php';
    }
    public function adminPage()
    {
        requireAdmin();
        require_once __DIR__ . '/../views/pages/admin/index.php';
    }
    public function registerPage()
    {
        require_once __DIR__ . '/../views/pages/auth/register.php';
    }
    public function loginPage()
    {
        require_once __DIR__ . '/../views/pages/auth/login.php';
    }
    public function matchDetailPage()
    {
        require_once __DIR__ . '/../views/pages/match_seance/detail.php';
    }
    // redirection vers  la page de convocation
    public function convocationPage()
    {
        require_once __DIR__ . '/../views/pages/convocation/index.php';
    }
    // les functions pour afficher tous les vues du rubrique matchs
    public function matchCreatePage()
    {
        require_once __DIR__ . '/../views/pages/match_seance/create.php';
    }
    public function matchEditPage()
    {
        require_once __DIR__ . '/../views/pages/match_seance/edit.php';
    }
    public function matchConvocationsPage()
    {
        require_once __DIR__ . '/../views/pages/match_seance/convocations.php';
    }
    
    public function sanctionPage()
    {
        require_once __DIR__ . '/../views/pages/sanction/index.php';
    }
}
