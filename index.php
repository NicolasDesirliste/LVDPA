<?php
require_once 'vendor/autoload.php';
// Tests de traitement de requêtes. 
error_log("URI: " . $_SERVER['REQUEST_URI']);
error_log("METHOD: " . $_SERVER['REQUEST_METHOD']);

use App\Controllers\HomeController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController;
use App\Controllers\ApiController;

$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('/LVDPA', '', $uri); // Enlever le préfixe

switch($uri) {
    case '/register':
        $controller = new RegisterController();
        $controller->showForm();
        break;
    
    case '/':
    case '':
        $controller = new HomeController();
        $controller->index();
        break;
    
    case '/login':
        $controller = new LoginController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->process();  // Traiter la connexion
        } else {
            $controller->showForm(); // Afficher le formulaire
        }
        break;

    case '/logout':
        $controller = new LoginController();
        $controller->logout();
        break;
        
    // Routes API
    case '/api/sidebar-menus':
        $controller = new ApiController();
        $controller->getSidebarMenus();
        break;
        
    case '/api/session-status':
        $controller = new ApiController();
        $controller->getSessionStatus();
        break;
        
    default:
        http_response_code(404);
        echo "Cette page est encore en cours de construction... revenez plus tard!";
}