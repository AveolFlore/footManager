<?php

use Controllers\Admin\AdminController;
use Controllers\Auth\AuthController;
use Controllers\PageController;
use Controllers\Presence\PresenceController;
use Controllers\Cotisation\CotisationController;
use Controllers\Reglement\ReglementController;
use Controllers\Performance\PerformanceController;
use Controllers\Match_seance\Match_seanceController;
use Controllers\Galerie\GalerieController;


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
            case 'presence':
                $controllerInstance = new PresenceController();
                break;
            case 'cotisation':
                $controllerInstance = new CotisationController();
                break;
            case 'reglement':
                $controllerInstance = new ReglementController();
                break;
            case 'performance':
                $controllerInstance = new PerformanceController();
                break;
            case 'match':
                $controllerInstance = new Match_seanceController(); 
                break;
            case 'galerie':
                $controllerInstance = new GalerieController();
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
            
            // NOUVELLES ROUTES
            case 'detail':
                $controllerInstance->matchDetailPage();
                break;
            case 'marquer':
                $controllerInstance->marquerPresence();
                break;
            case 'payer':
                $controllerInstance->payerCotisation();
                break;
            case 'proposer':
                $controllerInstance->proposerReglement();
                break;
            case 'voter':
                $controllerInstance->voter();
                break;
            case 'enregistrer':
                $controllerInstance->enregistrerPerformances();
                break;
            case 'creer':
                $controllerInstance->creerMatch();
                break;
            case 'upload':
                $controllerInstance->upload();
                break;
            case 'delete':
                $controllerInstance->delete();
                break;
            case 'changeteam':
                $controllerInstance->changeTeam();
                break;

            default:
                echo "Actions non existant !";
                break;
        }
    } catch (Exception $e) {
        echo "erreur..." . $e->getMessage();
    }
}
