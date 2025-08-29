<?php
// app/core/ViewSpa.php
namespace App\Core;

/**
 * Classe ViewSpa
 * 
 * Responsabilité UNIQUE : Gérer le rendu des vues pour l'application SPA
 * - Rendu de vues partielles (pour les requêtes AJAX)
 * - Rendu de vues complètes (pour le chargement initial)
 * - Détection automatique du type de requête
 */
class ViewSpa {
    
    /**
     * Détecte si c'est une requête AJAX
     * 
     * @return bool
     */
    public static function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Rend une vue (partielle ou complète selon le contexte)
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser (par défaut 'home')
     */
    public static function render(string $view, array $data = [], string $layout = 'home'): void {
        if (self::isAjaxRequest()) {
            self::renderPartial($view, $data);
        } else {
            self::renderFull($view, $data, $layout);
        }
    }
    
    /**
     * Rend uniquement une vue partielle (pour AJAX)
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     */
    public static function renderPartial(string $view, array $data = []): void {
        // Extraire les données pour la vue
        extract($data);
        
        // Construire le chemin
        $viewPath = self::getViewPath($view);
        
        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo json_encode(['error' => 'Vue non trouvée']);
            return;
        }
        
        // Envoyer les headers appropriés
        header('Content-Type: text/html; charset=UTF-8');
        
        // Inclure la vue
        require $viewPath;
    }
    
    /**
     * Rend une vue complète avec layout
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser
     */
    public static function renderFull(string $view, array $data = [], string $layout = 'home'): void {
        // Préparer les données pour le layout
        $data['contentView'] = $view;
        
        // Extraire les données
        extract($data);
        
        // Construire le chemin du layout
        $layoutPath = self::getViewPath($layout);
        
        if (!file_exists($layoutPath)) {
            die("Layout '$layout' introuvable");
        }
        
        // Inclure le layout
        require $layoutPath;
    }
    
    /**
     * Construit le chemin vers une vue
     * 
     * @param string $view Nom de la vue
     * @return string Chemin complet
     */
    private static function getViewPath(string $view): string {
        // Gérer les sous-dossiers (ex: 'user/profile' => 'user/profile.php')
        $view = str_replace('.', '/', $view);
        
        return dirname(__DIR__) . '/views/' . $view . '.php';
    }
    
    /**
     * Inclut le contenu d'une vue dans le layout
     * À utiliser dans main.php
     * 
     * @param string $view Nom de la vue à inclure
     * @param array $data Données disponibles
     */
    public static function includeContent(string $view, array $data = []): void {
        extract($data);
        
        $viewPath = self::getViewPath($view);
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '<div class="alert alert-error">Contenu non trouvé</div>';
        }
    }
    
    /**
     * Envoie une réponse JSON (utile pour les APIs)
     * 
     * @param array $data Données à envoyer
     * @param int $statusCode Code HTTP
     */
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}