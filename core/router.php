<?php

use Controllers\Admin\AdminController;
use Controllers\Auth\AuthController;
use Controllers\Convocation\ConvocationController;
use Controllers\PageController;


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
// instatiation du controller a nul ca peter chez moi sinon
$controllerInstance = null;
$id = $_GET['id'] ?? null;

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
            // Ajout de la casse minuscule pour correspondre aux URLs
            case 'Convocation':
                $controllerInstance = new ConvocationController();
                break;

            default:
                echo "Controller non existant !";
                break;
        }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}

// Determiner l'action a appelé - On vérifie que le controller existe
if (isset($action) && $controllerInstance !== null) {
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
                // route des convocations
            case 'convocation':
                // On s'assure d'utiliser ConvocationController pour charger les données
                // même si on arrive via 'page-convocation'
                if (!($controllerInstance instanceof ConvocationController)) {
                    $controllerInstance = new ConvocationController();
                }
                $controllerInstance->convocationPage();
                break;
                // appele la methode invoke dans le controller qui permet de convoquer les joueres
            case 'invoke':
                $controllerInstance->invokePlayer();
                break;

            default:
                echo "Actions non existant !";
                break;
        }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}
