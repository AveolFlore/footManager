<?php

use Controllers\Admin\AdminController;
use Controllers\Auth\AuthController;
use Controllers\ConvocationController;
use Controllers\MatchSeanceController;
use Controllers\PageController;
use Controllers\ResultatMatchController;


// on recupère la route demandée par l'utilisateur
$route = $_SERVER['REQUEST_URI'];

// on traite la route pour enlever les paramètres de requête
$route = explode('?', $route)[0];

// supprimer les slashes de début
if (strlen($route) > 1) {
    $route = substr($route, 1);
}

// Diviser la chaine par le tiret
$part = explode('-', $route);

//Determiner le controller et l'action
$controllerName = $part[0] ?? '/';
$action = $part[1] ?? 'home';
$id = $_GET['id'] ?? null;

$controllerInstance = null;
// var_dump($route);

// Determiner le controller a appelé
if (isset($controllerName)) {
    try {
        switch ($controllerName) {

            case '/':
                $controllerInstance = new PageController();
                break;

            case 'page':
                $controllerInstance = new PageController();
                break;
            case 'auth':
                $controllerInstance = new AuthController();
                break;
            case 'admin':
                $controllerInstance = new AdminController();
                break;

            // debut des controlleurs ajoutées par renaud  
            // instanciation du controlleur match_seance
            case 'matchSeance':
                $controllerInstance = new MatchSeanceController();
                break;
            // instanciation du controlleur convocation
            case 'convocation':
                $controllerInstance = new ConvocationController();
                break;
            // instanciation du controlleur resultat_match
            case 'resultatMatch':
                $controllerInstance = new ResultatMatchController();
                break;
            // fin des controlleurs    

            default:
                echo "Controller non existant !";
                break;
        }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}

// Determiner l'action a appelé
if (isset($action)) {
    try {
        switch ($action) {
            case 'home':
                $controllerInstance->homePage();
                break;
            case 'redirect':
                $controllerInstance->redirectPage();
                break;
            case 'team':
                $controllerInstance->teamPage();
                break;
            case 'match':
                $controllerInstance->matchPage();
                break;
            case 'traning':
                $controllerInstance->traningPage();
                break;
            case 'rule':
                $controllerInstance->rulePage();
                break;
            case 'finance':
                $controllerInstance->cotisationPage();
                break;
            case 'galery':
                $controllerInstance->galeryPage();
                break;
            case 'classement':
                $controllerInstance->classementPage();
                break;
            case 'admin':
                $controllerInstance->adminPage();
                break;
            case 'admincreateuser':
                $controllerInstance->adminCreateUserPage();
                break;
            case 'adminstoreuser':
                $controllerInstance->createUserByAdmin($_POST);
                break;
            case 'validateuser':
                $controllerInstance->validateUser();
                break;
            case 'rejetuser':
                $controllerInstance->refuseUser();
                break;
            case 'signup':
                $controllerInstance->registerJoueur($_POST, $_POST);
                break;
            case 'signin':
                $controllerInstance->login();
                break;
            case 'logout':
                $controllerInstance->logout();
                break;
            case 'login':
                $controllerInstance->loginPage();
                break;
            case 'register':
                $controllerInstance->registerPage();
                break;

            // les actions ajoutées par renaud   

            //  les actions pour afficher les vues de la rubrique match

            case 'matchlist':
                $controllerInstance->matchPage();
                break;
            case 'matchcreate':
                $controllerInstance->matchCreatePage();
                break;
            case 'matchedit':
                $controllerInstance->matchEditPage();
                break;
            case 'matchdetail':
                $controllerInstance->matchDetailPage();
                break;
            case 'matchconvocations':
                $controllerInstance->matchConvocationsPage();
                break;

            // les actions du controller match_seance
            case 'list':
                $controllerInstance->index();
                break;
            case 'show':
                $controllerInstance->read_one((int) $id);
                break;
            case 'create':
                $controllerInstance->store($_POST);
                break;
            case 'edit':
                $controllerInstance->edit((int) $id);
                break;
            case 'update':
                $controllerInstance->update($_POST);
                break;
            case 'publish':
                $controllerInstance->publier((int) $id);
                break;
            case 'close':
                $controllerInstance->terminer((int) $id);
                break;
            case 'delete':
                $controllerInstance->destroy((int) $id);
                break;

            // actions du controller convocation par renaud
            case 'convoclist':
                $controllerInstance->index((int) $id);
                break;
            case 'suggest':
                $controllerInstance->get_suggestion();
                break;
            case 'convocsave':
                $controllerInstance->store($_POST);
                break;
            case 'convocdelete':
                $controllerInstance->destroy((int) $id);
                break;


            // actions du controller resultat_match
            case 'resultatshow':
                $controllerInstance->index((int) $id);
                break;
            case 'resultatsave':
                $controllerInstance->store($_POST);
                break;
            case 'resultatupdate':
                $controllerInstance->update($_POST);
                break;
            
            // fin des actions de renaud    

            default:
                echo "Actions non existant !";
                break;
        }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}
