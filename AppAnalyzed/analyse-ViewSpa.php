<?php
// app/core/ViewSpa.php
namespace App\Core; // Déclaration du namespace pour l'autoloading

/**
 * Classe ViewSpa
 * 
 * Responsabilité UNIQUE : Gérer le rendu des vues pour l'application SPA
 * - Rendu de vues partielles (pour les requêtes AJAX)
 * - Rendu de vues complètes (pour le chargement initial)
 * - Détection automatique du type de requête
 */
class ViewSpa { // Déclaration de la classe ViewSpa
    
    /**
     * Détecte si c'est une requête AJAX
     * 
     * @return bool
     */
    public static function isAjaxRequest(): bool { // Méthode statique publique qui retourne un booléen
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && // Vérifie que l'en-tête HTTP_X_REQUESTED_WITH n'est pas vide
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'; // ET que sa valeur en minuscules est 'xmlhttprequest'
    }
    
    /**
     * Rend une vue (partielle ou complète selon le contexte)
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser (par défaut 'home')
     */
    public static function render(string $view, array $data = [], string $layout = 'home'): void { // Méthode statique avec 3 paramètres, layout par défaut 'home'
        if (self::isAjaxRequest()) { // Vérifie si c'est une requête AJAX
            self::renderPartial($view, $data); // Si oui, rend seulement la vue partielle
        } else { // Sinon
            self::renderFull($view, $data, $layout); // Rend la vue complète avec layout
        }
    }
    
    /**
     * Rend uniquement une vue partielle (pour AJAX)
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     */
    public static function renderPartial(string $view, array $data = []): void { // Méthode statique pour rendu partiel
        // Extraire les données pour la vue
        extract($data); // Extrait les clés du tableau comme variables
        
        // Construire le chemin
        $viewPath = self::getViewPath($view); // Appelle getViewPath() pour obtenir le chemin complet
        
        if (!file_exists($viewPath)) { // Vérifie si le fichier vue existe
            http_response_code(404); // Envoie un code HTTP 404
            echo json_encode(['error' => 'Vue non trouvée']); // Envoie une erreur en JSON
            return; // Sort de la fonction
        }
        
        // Envoyer les headers appropriés
        header('Content-Type: text/html; charset=UTF-8'); // Définit le type de contenu comme HTML UTF-8
        
        // Inclure la vue
        require $viewPath; // Inclut et exécute le fichier vue
    }
    
    /**
     * Rend une vue complète avec layout
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser
     */
    public static function renderFull(string $view, array $data = [], string $layout = 'home'): void { // Méthode statique pour rendu complet
        // Préparer les données pour le layout
        $data['contentView'] = $view; // Ajoute le nom de la vue dans les données sous la clé 'contentView'
        
        // Extraire les données
        extract($data); // Extrait les clés comme variables pour le layout
        
        // Construire le chemin du layout
        $layoutPath = self::getViewPath($layout); // Obtient le chemin du fichier layout
        
        if (!file_exists($layoutPath)) { // Vérifie si le layout existe
            die("Layout '$layout' introuvable"); // Arrête l'exécution avec message d'erreur
        }
        
        // Inclure le layout
        require $layoutPath; // Inclut et exécute le fichier layout
    }
    
    /**
     * Construit le chemin vers une vue
     * 
     * @param string $view Nom de la vue
     * @return string Chemin complet
     */
    private static function getViewPath(string $view): string { // Méthode privée statique qui retourne un string
        // Gérer les sous-dossiers (ex: 'user/profile' => 'user/profile.php')
        $view = str_replace('.', '/', $view); // Remplace les points par des slashes pour gérer les sous-dossiers
        
        return dirname(__DIR__) . '/views/' . $view . '.php'; // Retourne le chemin complet : parent du parent + /views/ + nom + .php
    }
    
    /**
     * Inclut le contenu d'une vue dans le layout
     * À utiliser dans main.php
     * 
     * @param string $view Nom de la vue à inclure
     * @param array $data Données disponibles
     */
    public static function includeContent(string $view, array $data = []): void { // Méthode statique pour inclusion dans layout
        extract($data); // Extrait les variables des données
        
        $viewPath = self::getViewPath($view); // Obtient le chemin de la vue
        
        if (file_exists($viewPath)) { // Si le fichier existe
            require $viewPath; // L'inclut
        } else { // Sinon
            echo '<div class="alert alert-error">Contenu non trouvé</div>'; // Affiche un message d'erreur HTML
        }
    }
    
    /**
     * Envoie une réponse JSON (utile pour les APIs)
     * 
     * @param array $data Données à envoyer
     * @param int $statusCode Code HTTP
     */
    public static function json(array $data, int $statusCode = 200): void { // Méthode statique avec code HTTP par défaut 200
        http_response_code($statusCode); // Définit le code de réponse HTTP
        header('Content-Type: application/json; charset=UTF-8'); // Définit l'en-tête comme JSON UTF-8
        echo json_encode($data, JSON_UNESCAPED_UNICODE); // Encode et affiche en JSON sans échapper l'unicode
        exit; // Termine l'exécution du script
    }
}