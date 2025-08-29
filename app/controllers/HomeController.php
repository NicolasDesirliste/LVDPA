<?php
// app/controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SidebarService;

/**
 * Classe HomeController
 * 
 * Responsabilité : Gérer l'affichage de la page d'accueil
 * - Prépare les données pour la vue
 * - Utilise SidebarService pour les menus
 */
class HomeController extends Controller {
    private SidebarService $sidebarService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct();
        $this->sidebarService = new SidebarService();
    }
    
    /**
     * Affiche la page d'accueil
     */
    public function index(): void {
        // Récupérer les menus filtrés selon les permissions
        $sidebarMenus = $this->sidebarService->getFilteredMenus();
        
        // Préparer les données pour la vue
        $data = [
            'title' => 'Bienvenue sur LVDPA',
            'sidebarMenus' => $sidebarMenus,
            // Données supplémentaires si nécessaire
            'hasAdminAccess' => $this->sidebarService->hasAdministrativeAccess()
        ];
        
        // Charger la vue avec les données
        $this->render('home', $data);
    }
}