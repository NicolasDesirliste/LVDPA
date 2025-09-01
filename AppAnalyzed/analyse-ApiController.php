<?php

namespace App\Controllers; // Déclaration du namespace pour l'autoloading

use App\Core\Controller; // Import de la classe Controller de base
use App\Services\SidebarService; // Import de la classe SidebarService

/**
 * Classe ApiController
 * 
 * Responsabilité : Gérer les endpoints API
 * - Retourne des données JSON
 * - Pas de rendu de vues HTML
 */
class ApiController extends Controller { // La classe hérite de Controller
    private SidebarService $sidebarService; // Propriété privée typée SidebarService
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        parent::__construct(); // Appelle le constructeur de la classe parent Controller
        $this->sidebarService = new SidebarService(); // Instancie un nouveau SidebarService
    }
    
    /**
     * Retourne les menus de la sidebar en JSON
     * Route: /api/sidebar-menus
     */
    public function getSidebarMenus(): void { // Méthode publique avec type de retour void
        // Récupérer les menus filtrés selon les permissions
        $menus = $this->sidebarService->getFilteredMenus(); // Appelle getFilteredMenus() du service et stocke le résultat
        
        // Retourner en JSON
        $this->json([ // Appelle la méthode json() héritée de Controller
            'success' => true, // Indicateur de succès
            'menus' => $menus, // Les menus filtrés
            'userType' => $this->getCurrentUserType(), // Appelle une méthode héritée pour obtenir le type d'utilisateur
            'isAuthenticated' => $this->isAuthenticated() // Appelle une méthode héritée pour vérifier l'authentification
        ]);
    }
    
    /**
     * Retourne le statut de session
     * Route: /api/session-status
     */
    public function getSessionStatus(): void { // Méthode publique avec type de retour void
        $this->json([ // Appelle directement json() avec un array
            'isAuthenticated' => $this->isAuthenticated(), // Vérifie si l'utilisateur est authentifié
            'userType' => $this->getCurrentUserType(), // Récupère le type d'utilisateur
            'userId' => $this->getCurrentUserId(), // Récupère l'ID de l'utilisateur
            'isAdmin' => \App\Services\SessionManager::isAdmin(), // Appelle isAdmin() 
            'isModerator' => \App\Services\SessionManager::isModerator(), // Appelle isModerator()  
            'isProfessional' => \App\Services\SessionManager::isProfessional() // Appelle isProfessional()  
        ]);
    }
}