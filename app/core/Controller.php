<?php
// app/core/Controller.php
namespace App\Core;

use App\Services\SessionManager;

/**
 * Classe Controller
 * 
 * Responsabilité UNIQUE : Fournir les fonctionnalités communes aux contrôleurs
 * - Redirections
 * - Rendu des vues
 * - Réponses JSON
 * - Méthodes de protection (via SessionManager)
 */
abstract class Controller {
    /**
     * Constructeur - Initialise la session
     */
    public function __construct() {
        SessionManager::initSession();
    }
    
    /**
     * Redirige vers une URL
     * 
     * @param string $url
     */
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
    
    /**
     * Définit un message flash
     * 
     * @param string $message
     * @param string $type (success, error, warning, info)
     */
    protected function setFlash(string $message, string $type = 'success'): void {
        SessionManager::setFlashMessage($type, $message);
    }
    
    /**
     * Récupère les messages flash
     * 
     * @return array
     */
    protected function getFlashMessages(): array {
        return SessionManager::getFlashMessages();
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool {
        return SessionManager::isLoggedIn();
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     * 
     * @return int|null
     */
    protected function getCurrentUserId(): ?int {
        return SessionManager::getUserId();
    }
    
    /**
     * Récupère le type d'utilisateur
     * 
     * @return string
     */
    protected function getCurrentUserType(): string {
        return SessionManager::getUserType();
    }
    
    /**
     * Exige qu'un utilisateur soit connecté
     * Redirige vers login sinon
     */
    protected function requireAuth(): void {
        if (!SessionManager::isLoggedIn()) {
            $this->setFlash("Vous devez être connecté pour accéder à cette page", "error");
            $this->redirect("/login");
        }
    }
    
    /**
     * Exige qu'un utilisateur soit administrateur
     */
    protected function requireAdmin(): void {
        if (!SessionManager::isAdmin()) {
            $this->setFlash("Accès réservé aux administrateurs", "error");
            $this->redirect("/");
        }
    }
    
    /**
     * Charge une vue
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     */
    protected function render(string $view, array $data = []): void {
        // Ajouter les messages flash aux données
        $data['flashMessages'] = $this->getFlashMessages();
        
        // Ajouter les infos utilisateur aux données
        $data['isAuthenticated'] = $this->isAuthenticated();
        $data['currentUserId'] = $this->getCurrentUserId();
        $data['currentUserType'] = $this->getCurrentUserType();
        
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Construire le chemin de la vue
        $viewPath = dirname(__DIR__) . '/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die("La vue $view n'existe pas");
        }
        
        // Inclure la vue
        require $viewPath;
    }
    
    /**
     * Retourne une réponse JSON
     * 
     * @param array $data
     * @param int $statusCode
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
}