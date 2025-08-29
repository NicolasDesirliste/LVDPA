<?php
// app/controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Classe HomeController
 * 
 * Responsabilité : Gérer l'affichage de la page d'accueil
 */
class HomeController extends Controller {
    
    /**
     * Affiche la page d'accueil
     */
    public function index(): void {
        // chargement simple de la vue home.php
        
        
        $this->render('home');
    }
}