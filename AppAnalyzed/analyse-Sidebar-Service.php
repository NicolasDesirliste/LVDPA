<?php
// app/services/SidebarService.php
namespace App\Services; // Déclaration du namespace pour l'autoloading PSR-4

use App\Services\SessionManager; // Import de la classe SessionManager

/**
 * Classe SidebarService
 * 
 * Responsabilité UNIQUE : Générer la structure de la sidebar selon les permissions
 * - Lit la configuration
 * - Applique les règles de visibilité
 * - Retourne uniquement les éléments autorisés
 */
class SidebarService { 
    private array $config; // Propriété privée pour stocker la configuration, typée comme array
    // Typage déclaré (PHP 7.4 ++)
    /*
    private = "Touche pas, c'est mes données internes !"
    array = "Et ça DOIT être un tableau, sinon erreur !"
    OK Jacquouille? ====>  C'EST OKAYH!  
    */
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public appelé à l'instanciation
        // Charger la configuration
        $this->config = require dirname(__DIR__) . '/config/sidebar-config.php'; // Charge et exécute le fichier de config qui retourne un array
    }
    
    /**
     * Récupère les menus filtrés selon les permissions de l'utilisateur
     * 
     * @return array
     */
    public function getFilteredMenus(): array { // Méthode publique qui retourne un array
        $filteredMenus = []; // Initialise un array vide pour stocker les menus filtrés
        
        foreach ($this->config['menus'] as $menu) { // Parcourt chaque menu dans la configuration
            // Vérifier si le menu est visible pour l'utilisateur actuel
            if ($this->isVisible($menu['visibility'])) { // Appelle isVisible() avec la règle de visibilité du menu
                // Filtrer les liens du menu
                $filteredLinks = $this->filterLinks($menu['links']); // Appelle filterLinks() pour filtrer les liens du menu
                
                // N'ajouter le menu que s'il reste des liens visibles
                if (!empty($filteredLinks)) { // Vérifie que l'array de liens filtrés n'est pas vide
                    $menu['links'] = $filteredLinks; // Remplace les liens originaux par les liens filtrés
                    $filteredMenus[] = $menu; // Ajoute le menu modifié à l'array des menus filtrés
                }
            }
        }
        
        return $filteredMenus; // Retourne l'array des menus filtrés
    }
    
    /**
     * Filtre les liens selon les permissions
     * 
     * @param array $links
     * @return array
     */
    private function filterLinks(array $links): array { // Méthode privée qui prend un array de liens et retourne un array
        $filteredLinks = []; // Initialise un array vide pour les liens filtrés
        
        foreach ($links as $link) { // Parcourt chaque lien
            // Si pas de règle de visibilité sur le lien, il est visible
            $linkVisibility = $link['visibility'] ?? 'all'; // Utilise l'opérateur null coalescing, défaut à 'all' si visibility n'existe pas
            
            if ($this->isVisible($linkVisibility)) { // Vérifie si le lien est visible selon sa règle
                // Adapter l'URL pour les professionnels si nécessaire
                $link = $this->adaptLinkForUser($link); // Appelle adaptLinkForUser() pour personnaliser le lien
                $filteredLinks[] = $link; // Ajoute le lien adapté à l'array des liens filtrés
            }
        }
        
        return $filteredLinks; // Retourne l'array des liens filtrés
    }
    
    /**
     * Vérifie si un élément est visible selon la règle de visibilité
     * 
     * @param string $visibility
     * @return bool
     */
    private function isVisible(string $visibility): bool { // Méthode privée qui prend une string et retourne un booléen
        switch ($visibility) { // Structure switch sur la règle de visibilité
            case 'all': // Cas où visibility est 'all'
                return true; // Retourne true (visible pour tous)
                
            case 'guest': // Cas où visibility est 'guest'
                return !SessionManager::isLoggedIn(); // Retourne true si l'utilisateur n'est PAS connecté
                
            case 'authenticated': // Cas où visibility est 'authenticated'
                return SessionManager::isLoggedIn(); // Retourne true si l'utilisateur est connecté
                
            case 'role:admin': // Cas où visibility est 'role:admin'
                return SessionManager::isAdmin(); // Retourne le résultat de isAdmin()
                
            case 'role:moderateur': // Cas où visibility est 'role:moderateur'
                return SessionManager::isModerator(); // Retourne le résultat de isModerator()
                
            case 'type:professionnel': // Cas où visibility est 'type:professionnel'
                return SessionManager::isProfessional(); // Retourne le résultat de isProfessional()
                
            default: // Cas par défaut si aucune règle ne correspond
                // Par défaut, on cache l'élément si la règle n'est pas reconnue
                return false; // Retourne false (non visible)
        }
    }
    
    /**
     * Adapte certains liens selon le type d'utilisateur
     * 
     * @param array $link
     * @return array
     */
    private function adaptLinkForUser(array $link): array { // Méthode privée qui prend un array link et retourne un array
        // Exemple : adapter l'URL de "Mon profil" pour les professionnels
        if ($link['text'] === 'Mon profil' && SessionManager::isProfessional()) { // Si le texte est "Mon profil" ET l'utilisateur est professionnel
            $link['href'] = '/LVDPA/profil-professionnel'; // Change l'URL vers la page profil professionnel
            // Optionnel : ajouter une classe CSS pour styling différent
            $link['class'] = 'professionnel-link'; // Ajoute une classe CSS au lien
        }
        
        return $link; // Retourne le lien (modifié ou non)
    }
    
    /**
     * Récupère un menu spécifique par son titre
     * 
     * @param string $title
     * @return array|null
     */
    public function getMenuByTitle(string $title): ?array { // Méthode publique avec type de retour nullable (array ou null)
        $allMenus = $this->getFilteredMenus(); // Récupère tous les menus filtrés
        
        foreach ($allMenus as $menu) { // Parcourt chaque menu
            if ($menu['title'] === $title) { // Compare le titre du menu avec le titre recherché (égalité stricte)
                return $menu; // Retourne le menu si trouvé
            }
        }
        
        return null; // Retourne null si aucun menu trouvé
    }
    
    /**
     * Vérifie si l'utilisateur a accès à au moins un menu admin/modérateur
     * 
     * @return bool
     */
    public function hasAdministrativeAccess(): bool { // Méthode publique qui retourne un booléen
        return SessionManager::isAdmin() || SessionManager::isModerator(); // Retourne true si admin OU modérateur
    }
    
    /**
     * Compte le nombre de menus visibles pour l'utilisateur
     * 
     * @return int
     */
    public function countVisibleMenus(): int { // Méthode publique qui retourne un entier
        return count($this->getFilteredMenus()); // Utilise count() sur le résultat de getFilteredMenus()
    }
    
    /**
     * Récupère tous les liens d'un certain type
     * Utile pour générer des sitemaps ou des menus secondaires
     * 
     * @param string $visibility
     * @return array
     */
    public function getLinksByVisibility(string $visibility): array { // Méthode publique qui prend une string visibility et retourne un array
        $links = []; // Initialise un array vide pour stocker les liens
        
        foreach ($this->config['menus'] as $menu) { // Première boucle : parcourt tous les menus
            if ($this->isVisible($menu['visibility'])) { // Vérifie si le menu est visible
                foreach ($menu['links'] as $link) { // Deuxième boucle : parcourt les liens du menu
                    $linkVisibility = $link['visibility'] ?? 'all'; // Récupère la visibilité du lien, défaut à 'all'
                    if ($linkVisibility === $visibility) { // Compare avec la visibilité recherchée (égalité stricte)
                        $links[] = $link; // Ajoute le lien à l'array si correspond
                    }
                }
            }
        }
        
        return $links; // Retourne tous les liens trouvés
    }
}