<?php
// app/services/SessionManager.php
namespace App\Services;

class SessionManager {
    /**
     * Initialise la session avec les protections de sécurité
     */
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) { // (exemple pour les Jurys)
            // PROTECTION XSS : Empêche l'accès aux cookies via JavaScript
            // Toujours en cours de construction et recherches, va changer dans le futur.
            ini_set('session.cookie_httponly', 1);
            
            // PROTECTION HTTPS : à décommenter pour la production
            // ini_set('session.cookie_secure', 1);
            
            // PROTECTION : Mode strict pour éviter les attaques de fixation
            ini_set('session.use_strict_mode', 1);
            
            session_start();
            
            // RÉGÉNÉRATION ID = Protection contre la fixation de session
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id(true);
                $_SESSION['initiated'] = true;
            }
        }
    }
    
    /**
     * Connecte un utilisateur et stocke ses données en session
     * 
     * @param array $userData Données de l'utilisateur
     */
    public static function setUser($userData) {
        // RÉGÉNÉRATION ID : Nouvel ID à chaque connexion pour la sécurité
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_pseudo'] = $userData['pseudo'];
        $_SESSION['user_role'] = $userData['role'];                     // admin, moderateur, utilisateur normal
        $_SESSION['user_type_utilisateur'] = $userData['type_utilisateur']; // avocat, psychologue, etc.
        $_SESSION['last_activity'] = time();
        
        // Marquer comme connecté
        $_SESSION['logged_in'] = true;
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               !empty($_SESSION['user_id']) && 
               isset($_SESSION['logged_in']) && 
               $_SESSION['logged_in'] === true;
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     */
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Récupère le pseudo de l'utilisateur connecté
     */
    public static function getUserPseudo() {
        return $_SESSION['user_pseudo'] ?? null;
    }
    
    /**
     * Récupère le type d'utilisateur (avocat, psychologue, etc.)
     */
    public static function getUserType() {
        if (self::isLoggedIn()) {
            return $_SESSION['user_type_utilisateur'] ?? 'inconnu';
        }
        return 'visiteur';
    }
    
    /**
     * Récupère le rôle de l'utilisateur (admin, moderateur, utilisateur)
     */
    public static function getUserRole() {
        if (self::isLoggedIn()) {
            return $_SESSION['user_role'] ?? 'utilisateur';
        }
        return 'visiteur';
    }
    
    
    // Vérifie si l'utilisateur est administrateur:
    
    public static function isAdmin() {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
    }
    
    
    // Vérifie si l'utilisateur est modérateur:
  
    public static function isModerator() {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'moderateur';
    }
    
    
    // Vérifie si l'utilisateur est un professionnel
     
    public static function isProfessional() {
        $professionalTypes = ['avocat', 'psychologue', 'mediateur'];
        return self::isLoggedIn() && in_array($_SESSION['user_type_utilisateur'] ?? '', $professionalTypes);
    }
    
    /**
     * Stocke un message flash dans la session
     */
    public static function setFlashMessage($type, $message) {
        $_SESSION['flash_messages'][$type][] = $message;
    }
    
    /**
     * Récupère un type spécifique de message flash
     * 
     * @param string $type
     * @return array
     */
    public static function getFlashMessage($type) {
        $messages = $_SESSION['flash_messages'][$type] ?? [];
        unset($_SESSION['flash_messages'][$type]);
        return $messages;
    }
    
    /**
     * Récupère et efface tous les messages flash
     */
    public static function getFlashMessages() {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    /**
     * Vérifie si la session est valide et active
     */
    public static function validateSession() {
        // Vérifier si l'utilisateur est connecté
        if (!self::isLoggedIn()) {
            return false;
        }
        
        // Vérifier si la session n'a pas expiré
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            // Si la dernière activité date de plus de 30 minutes, déconnexion
            self::endSession();
            return false;
        }
        
        // Mettre à jour le timestamp de dernière activité
        $_SESSION['last_activity'] = time();
        
        return true;
    }

    /**
     * Termine la session (utilisé pour la déconnexion ou en cas de session invalide)
     */
    public static function endSession() {
        // SÉCURITÉ : Effacer toutes les données de session
        $_SESSION = array();
        
        // SÉCURITÉ : Supprimer le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // SÉCURITÉ : Détruire complètement la session
        session_destroy();
    }
    
    /**
     * SÉCURITÉ : Régénère l'ID de session (à utiliser périodiquement)
     */
    public static function regenerateId() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
    
    /**
     * Étend la durée de vie de la session (pour "Se souvenir de moi")
     * 
     * @param int $days Nombre de jours
     */
    public static function extendSession($days = 30) {
        // Définir la durée de vie du cookie de session
        $lifetime = $days * 24 * 60 * 60; // Convertion en secondes
        
        // Mettre à jour le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(
                session_name(),
                $_COOKIE[session_name()],
                time() + $lifetime,
                '/'
            );
        }
        
        // Optionnel : stocker une date d'expiration étendue dans la session
        $_SESSION['extended_expiration'] = time() + $lifetime;
    }
}