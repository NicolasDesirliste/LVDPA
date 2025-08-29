<?php
// app/core/Controller.php
namespace App\Core; // Namespace dans le dossier Core

use App\Services\SessionManager; // Import du gestionnaire de sessions

/**
 * Classe Controller
 * 
 * Responsabilité UNIQUE : Fournir les fonctionnalités communes aux contrôleurs
 * - Redirections
 * - Rendu des vues
 * - Réponses JSON
 * - Méthodes de protection (via SessionManager)
 */
abstract class Controller { // Classe abstraite (ne peut pas être instanciée directement)
    /**
     * Constructeur - Initialise la session
     */
    public function __construct() { // Constructeur public
        SessionManager::initSession(); // Initialise la session PHP
    }
    
    /**
     * Redirige vers une URL
     * 
     * @param string $url
     */
    protected function redirect(string $url): void { // Méthode protégée (accessible aux classes enfants)
        header("Location: $url"); // Envoie l'en-tête HTTP de redirection
        exit; // Arrête l'exécution du script
    }
    
    /**
     * Définit un message flash
     * 
     * @param string $message
     * @param string $type (success, error, warning, info)
     */
    protected function setFlash(string $message, string $type = 'success'): void { // Type par défaut 'success'
        SessionManager::setFlashMessage($type, $message); // Délègue au SessionManager
    }
    
    /**
     * Récupère les messages flash
     * 
     * @return array
     */
    protected function getFlashMessages(): array { // Retourne un array
        return SessionManager::getFlashMessages(); // Délègue au SessionManager
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool { // Retourne un booléen
        return SessionManager::isLoggedIn(); // Délègue au SessionManager
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     * 
     * @return int|null
     */
    protected function getCurrentUserId(): ?int { // Type de retour nullable (int ou null)
        return SessionManager::getUserId(); // Délègue au SessionManager
    }
    
    /**
     * Récupère le type d'utilisateur
     * 
     * @return string
     */
    protected function getCurrentUserType(): string { // Retourne une string
        return SessionManager::getUserType(); // Délègue au SessionManager
    }
    
    /**
     * Exige qu'un utilisateur soit connecté
     * Redirige vers login sinon
     */
    protected function requireAuth(): void { // Méthode sans retour
        if (!SessionManager::isLoggedIn()) { // Si pas connecté
            $this->setFlash("Vous devez être connecté pour accéder à cette page", "error"); // Message d'erreur
            $this->redirect("/login"); // Redirige vers login
        }
    }
    
    /**
     * Exige qu'un utilisateur soit administrateur
     */
    protected function requireAdmin(): void { // Méthode sans retour
        if (!SessionManager::isAdmin()) { // Si pas admin
            $this->setFlash("Accès réservé aux administrateurs", "error"); // Message d'erreur
            $this->redirect("/"); // Redirige vers l'accueil
        }
    }
    
    /**
     * Charge une vue
     * 
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     */
    protected function render(string $view, array $data = []): void { // Data par défaut array vide
        // Ajouter les messages flash aux données
        $data['flashMessages'] = $this->getFlashMessages(); // Récupère et ajoute les messages flash
        
        // Ajouter les infos utilisateur aux données
        $data['isAuthenticated'] = $this->isAuthenticated(); // Ajoute le statut de connexion
        $data['currentUserId'] = $this->getCurrentUserId(); // Ajoute l'ID utilisateur
        $data['currentUserType'] = $this->getCurrentUserType(); // Ajoute le type d'utilisateur
        
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data); // Transforme les clés du tableau en variables
        
        // Construire le chemin de la vue
        $viewPath = dirname(__DIR__) . '/views/' . $view . '.php'; // Construit le chemin complet
        
        if (!file_exists($viewPath)) { // Si le fichier n'existe pas
            die("La vue $view n'existe pas"); // Arrête avec message d'erreur
        }
        
        // Inclure la vue
        require $viewPath; // Inclut et exécute le fichier vue
    }
    
    /**
     * Retourne une réponse JSON
     * 
     * @param array $data
     * @param int $statusCode
     */
    protected function json(array $data, int $statusCode = 200): void { // Code HTTP par défaut 200
        http_response_code($statusCode); // Définit le code de réponse HTTP
        header('Content-Type: application/json'); // Définit l'en-tête Content-Type
        echo json_encode($data); // Encode et affiche en JSON
        exit; // Arrête l'exécution
    }
    
}