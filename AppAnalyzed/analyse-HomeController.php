<?php
// app/controllers/HomeController.php
namespace App\Controllers; // Déclaration du namespace pour l'autoloading

use App\Core\Controller; // Import de la classe Controller de base
use App\Services\SidebarService; // Import du service SidebarService

/**
 * Classe HomeController
 * 
 * Responsabilité : Gérer l'affichage de la page d'accueil
 * - Prépare les données pour la vue
 * - Utilise SidebarService pour les menus
 */
class HomeController extends Controller { // Déclaration de la classe qui hérite de Controller
    private SidebarService $sidebarService; // Propriété privée typée SidebarService
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        parent::__construct(); // Appelle le constructeur de la classe parente Controller
        $this->sidebarService = new SidebarService(); // Instancie un nouveau SidebarService
    }
    
    /**
     * Affiche la page d'accueil
     */
    public function index(): void { // Méthode publique index avec type de retour void
        // Récupérer les menus filtrés selon les permissions
        $sidebarMenus = $this->sidebarService->getFilteredMenus(); // Appelle getFilteredMenus() et stocke le résultat
        
        // Préparer les données pour la vue
        $data = [ // Crée un array associatif avec les données
            'title' => 'Bienvenue sur LVDPA', // Titre de la page
            'sidebarMenus' => $sidebarMenus, // Les menus filtrés
            // Données supplémentaires si nécessaire
            'hasAdminAccess' => $this->sidebarService->hasAdministrativeAccess() // Vérifie si accès admin
        ];
        
        // Charger la vue avec les données
        $this->render('home', $data); // Appelle la méthode render() héritée avec le nom de vue et les données
    }
}