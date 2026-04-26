<?php

use Controllers\Auth\AuthController;
use Controllers\PageController;


// on recupère la route demandée par l'utilisateur
$route = $_SERVER['REQUEST_URI'];

// on traite la route pour enlever les paramètres de requête
$route = explode('?', $route)[0];

// supprimer les slashes de début
if(strlen($route) > 1 ) {
    $route = substr($route, 1);
}

// Diviser la chaine par le tiret
$part = explode('-', $route);

//Determiner le controller et l'action
$controllerName = $part[0] ?? '/';
$action = $part[1] ?? 'home';
$id = $_GET['id'] ?? null;

// var_dump($route);

// Determiner le controller a appelé
if(isset($controllerName)){
    try {
        switch($controllerName){

        case '/':
            $controllerInstance = new PageController();
            break;

        case 'page':
            $controllerInstance = new PageController();
            break;
        case 'auth':
            $controllerInstance = new AuthController();
            break;

        default:
        echo "Controller non existant !";
        break;
    }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}

// Determiner l'action a appelé
if(isset($action)){
    try {
        switch($action){
        case 'home':
            $controllerInstance->homePage();
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
        echo "Actions non existant !";
        break;
    }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}

?>