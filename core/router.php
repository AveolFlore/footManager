<?php

use Controllers\Admin\AdminController;
use Controllers\Auth\AuthController;
use Controllers\PageController;
use Controllers\Equipe\EquipeController;

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

$controllerName = $part[0] ?? '/';
$action = $part[1] ?? 'home';
$id = $_GET['id'] ?? null;

// var_dump($route);

// Determiner le controller a appelé
if (isset($controllerName)) {
    try {
        switch ($controllerName) {
            case '/':
            case '':
                $controllerInstance = new PageController();
                break;

            case 'page':
                $controllerInstance = new PageController();
                break;
            case 'auth':
                $controllerInstance = new AuthController();
                break;
            case 'admin':
                if (in_array($action, ['team', 'createequipe', 'storeequipe', 'teamedit', 'teamupdate', 'teamdelete', 'teamsearchajax'])) {
                    $controllerInstance = new \Controllers\Equipe\EquipeController();
                } else {
                    // On passe le pdo au contrôleur du chef
                    $controllerInstance = new \Controllers\Admin\AdminController($pdo ?? (new \Config\Database())->connect());
                }
                break;
            default:
                $controllerInstance = new PageController();
                break;
        }
    } catch (Exception $e) {
        echo 'erreur...' . $e->getMessage();
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
            //ajouter par Marcel
            case 'createequipe':
                $controllerInstance->createEquipePage();
                break;

            case 'details':
                $id = $_GET['id'] ?? null;
                $controller = new \Controllers\Equipe\EquipeController();
                $controller->showDetails($id);
                break;

            case 'storeequipe':
                $controllerInstance->storeEquipe($_POST);
                break;

            case 'teamedit':
                $controllerInstance->editEquipePage($id);
                break;

            case 'teamupdate':
                $controllerInstance->updateEquipe($_POST, $id);
                break;

            case 'teamdelete':
                $controllerInstance->deleteEquipe($id);
                break;

            case 'teamsearchajax':
                $controllerInstance->searchAJAX();
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

            default:
                echo 'Actions non existant !';
                break;
        }
    } catch (Exception $e) {
        echo 'erreur...' . $e->getMessage();
    }
}
