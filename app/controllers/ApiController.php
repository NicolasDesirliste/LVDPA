<?php
// app/controllers/ApiController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SidebarService;

/**
 * Classe ApiController
 * 
 * Responsabilité : Gérer les endpoints API
 * - Retourne des données JSON
 * - Pas de rendu de vues HTML
 */
class ApiController extends Controller {
    private SidebarService $sidebarService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct();
        $this->sidebarService = new SidebarService();
    }
    
    /**
     * Retourne les menus de la sidebar en JSON
     * Route: /api/sidebar-menus
     */
    public function getSidebarMenus(): void {
        // Récupérer les menus filtrés selon les permissions
        $menus = $this->sidebarService->getFilteredMenus();
        
        // Retourner en JSON
        $this->json([
            'success' => true,
            'menus' => $menus,
            'userType' => $this->getCurrentUserType(),
            'isAuthenticated' => $this->isAuthenticated()
        ]);
    }
    
    /**
     * Retourne le statut de session
     * Route: /api/session-status
     */
    public function getSessionStatus(): void {
        $this->json([
            'isAuthenticated' => $this->isAuthenticated(),
            'userType' => $this->getCurrentUserType(),
            'userId' => $this->getCurrentUserId(),
            'isAdmin' => \App\Services\SessionManager::isAdmin(),
            'isModerator' => \App\Services\SessionManager::isModerator(),
            'isProfessional' => \App\Services\SessionManager::isProfessional()
        ]);
    }
}