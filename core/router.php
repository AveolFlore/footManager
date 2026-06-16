<?php

use Controllers\Admin\AdminController;
use Controllers\Auth\AuthController;
use Controllers\Convocation\ConvocationController;
use Controllers\PageController;
use Controllers\Presence\PresenceController;
use Controllers\Cotisation\CotisationController;
use Controllers\Reglement\ReglementController;
use Controllers\Performance\PerformanceController;
use Controllers\MatchSeanceController;
use Controllers\Galerie\GalerieController;
use Controllers\Equipe\EquipeController;
// use Controllers\MatchSeanceController;
use Controllers\ResultatMatchController;
use Controllers\Sanction\SanctionController;

// On réinitialise les variables pour le reste du routeur !
$route = $_SERVER['REQUEST_URI'];
$route = explode('?', $route)[0];
if (strlen($route) > 1) {
    $route = substr($route, 1);
}
$part = explode('-', $route);
$controllerName = $part[0] ?? '/';
if ($controllerName === 'presence' || $controllerName === 'sanction') {
    $action = $part[1] ?? 'index';
} else if ($controllerName === 'admin' && count($part) > 2) {
    // Pour les routes admin avec 3 parties (ex: admin-team-show)
    $action = $part[1] . $part[2];
} else {
    $action = $part[1] ?? 'home';
}



// instatiation du controller a nul ca peter chez moi sinon
$controllerInstance = null;
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
                if (in_array($action, ['team', 'createequipe', 'storeequipe', 'teamedit', 'teamupdate', 'teamdelete', 'teamsearchajax', 'teamshow'])) {
                    $controllerInstance = new EquipeController();
                } else {
                    $controllerInstance = new AdminController();
                }
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
                $controllerInstance = new MatchSeanceController();
                break;
            case 'galerie':
                $controllerInstance = new GalerieController();
                break;
            // Ajout de la casse minuscule pour correspondre aux URLs
            case 'Convocation':
                $controllerInstance = new ConvocationController();
                break;
            case 'convocation':
                $controllerInstance = new ConvocationController();
                break;
            case 'matchSeance':
                $controllerInstance = new MatchSeanceController();
                break;
            case 'resultatMatch':
                $controllerInstance = new ResultatMatchController();
                break;
            case 'sanction':
                $controllerInstance = new SanctionController();
                break;
            default:
                $controllerInstance = new PageController();
                break;
        }
    } catch (Exception $e) {
        echo 'erreur...' . $e->getMessage();
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
            case 'createequipe':
                $controllerInstance->createEquipePage();
                break;
            case 'details':
            case 'teamshow':
                $id = $_GET['id'] ?? null;
                $controllerInstance->showDetails($id);
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

            // NOUVELLES ROUTES
            case 'detail':
                $controllerInstance->matchDetailPage();
                break;
            case 'index':
                if ($controllerInstance instanceof PresenceController) {
                    $controllerInstance->index();
                } else {
                    $controllerInstance->homePage();
                }
                break;
            case 'marquer':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controllerInstance->marquerPresence();
                } else {
                    $controllerInstance->formulaireMarquage();
                }
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
                $controllerInstance->matchCreatePage();
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
            case 'edituser':
                $controllerInstance->editUserPage();
                break;
            case 'updateuser':
                $controllerInstance->updateUser();
                break;
            case 'deleteuser':
                $controllerInstance->deleteUser();
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
            case 'sanction':
                if (!($controllerInstance instanceof SanctionController)) {
                    $controllerInstance = new SanctionController();
                }
                $controllerInstance->index();
                break;
            // appele la methode invoke dans le controller qui permet de convoquer les joueres
            case 'invoke':
                $controllerInstance->invokePlayer();
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
            case 'destroy':
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
            case 'marquerpayee':
                $controllerInstance->marquerPayee();
                break;

            default:
                echo "Actions non existant !";
                break;
        }
    } catch (Exception $e) {
        echo 'erreur...' . $e->getMessage();
    }
}
