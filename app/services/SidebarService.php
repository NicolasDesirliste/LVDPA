<?php
// app/services/SidebarService.php
namespace App\Services;

use App\Services\SessionManager;

/**
 * Classe SidebarService
 * 
 * Responsabilité UNIQUE : Générer la structure de la sidebar selon les permissions
 * - Lit la configuration
 * - Applique les règles de visibilité
 * - Retourne uniquement les éléments autorisés
 */
class SidebarService {
    private array $config;
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Charger la configuration
        $this->config = require dirname(__DIR__) . '/config/sidebar-config.php';
    }
    
    /**
     * Récupère les menus filtrés selon les permissions de l'utilisateur
     * 
     * @return array
     */
    public function getFilteredMenus(): array {
        $filteredMenus = [];
        
        foreach ($this->config['menus'] as $menu) {
            // Vérifier si le menu est visible pour l'utilisateur actuel
            if ($this->isVisible($menu['visibility'])) {
                // Filtrer les liens du menu
                $filteredLinks = $this->filterLinks($menu['links']);
                
                // N'ajouter le menu que s'il reste des liens visibles
                if (!empty($filteredLinks)) {
                    $menu['links'] = $filteredLinks;
                    $filteredMenus[] = $menu;
                }
            }
        }
        
        return $filteredMenus;
    }
    
    /**
     * Filtre les liens selon les permissions
     * 
     * @param array $links
     * @return array
     */
    private function filterLinks(array $links): array {
        $filteredLinks = [];
        
        foreach ($links as $link) {
            // Si pas de règle de visibilité sur le lien, il est visible
            $linkVisibility = $link['visibility'] ?? 'all';
            
            if ($this->isVisible($linkVisibility)) {
                // Adapter l'URL pour les professionnels si nécessaire
                $link = $this->adaptLinkForUser($link);
                $filteredLinks[] = $link;
            }
        }
        
        return $filteredLinks;
    }
    
    /**
     * Vérifie si un élément est visible selon la règle de visibilité
     * 
     * @param string $visibility
     * @return bool
     */
    private function isVisible(string $visibility): bool {
        switch ($visibility) {
            case 'all':
                return true;
                
            case 'guest':
                return !SessionManager::isLoggedIn();
                
            case 'authenticated':
                return SessionManager::isLoggedIn();
                
            case 'role:admin':
                return SessionManager::isAdmin();
                
            case 'role:moderateur':
                return SessionManager::isModerator();
                
            case 'type:professionnel':
                return SessionManager::isProfessional();
                
            default:
                // Par défaut, on cache l'élément si la règle n'est pas reconnue
                return false;
        }
    }
    
    /**
     * Adapte certains liens selon le type d'utilisateur
     * 
     * @param array $link
     * @return array
     */
    private function adaptLinkForUser(array $link): array {
        // Exemple : adapter l'URL de "Mon profil" pour les professionnels
        if ($link['text'] === 'Mon profil' && SessionManager::isProfessional()) {
            $link['href'] = '/LVDPA/profil-professionnel';
            // Optionnel : ajouter une classe CSS pour styling différent
            $link['class'] = 'professionnel-link';
        }
        
        return $link;
    }
    
    /**
     * Récupère un menu spécifique par son titre
     * 
     * @param string $title
     * @return array|null
     */
    public function getMenuByTitle(string $title): ?array {
        $allMenus = $this->getFilteredMenus();
        
        foreach ($allMenus as $menu) {
            if ($menu['title'] === $title) {
                return $menu;
            }
        }
        
        return null;
    }
    
    /**
     * Vérifie si l'utilisateur a accès à au moins un menu admin/modérateur
     * 
     * @return bool
     */
    public function hasAdministrativeAccess(): bool {
        return SessionManager::isAdmin() || SessionManager::isModerator();
    }
    
    /**
     * Compte le nombre de menus visibles pour l'utilisateur
     * 
     * @return int
     */
    public function countVisibleMenus(): int {
        return count($this->getFilteredMenus());
    }
    
    /**
     * Récupère tous les liens d'un certain type
     * Utile pour générer des sitemaps ou des menus secondaires
     * 
     * @param string $visibility
     * @return array
     */
    public function getLinksByVisibility(string $visibility): array {
        $links = [];
        
        foreach ($this->config['menus'] as $menu) {
            if ($this->isVisible($menu['visibility'])) {
                foreach ($menu['links'] as $link) {
                    $linkVisibility = $link['visibility'] ?? 'all';
                    if ($linkVisibility === $visibility) {
                        $links[] = $link;
                    }
                }
            }
        }
        
        return $links;
    }
}