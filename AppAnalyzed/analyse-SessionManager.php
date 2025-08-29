<?php
// app/services/SessionManager.php
namespace App\Services; // Déclaration du namespace

class SessionManager { // Déclaration de la classe SessionManager
    /**
     * Initialise la session avec les protections de sécurité
     */
    public static function initSession() { // Méthode statique publique
        if (session_status() === PHP_SESSION_NONE) { // Vérifie si aucune session n'est active (PHP_SESSION_NONE)
            // PROTECTION XSS : Empêche l'accès aux cookies via JavaScript
            // Toujours en cours de construction et recherches, va changer dans le futur.
            ini_set('session.cookie_httponly', 1); // Configure le cookie de session comme HttpOnly (inaccessible en JavaScript)
            
            // PROTECTION HTTPS : à décommenter pour la production
            // ini_set('session.cookie_secure', 1);
            
            // PROTECTION : Mode strict pour éviter les attaques de fixation
            ini_set('session.use_strict_mode', 1); // Active le mode strict (refuse les IDs de session non générés par PHP)
            
            session_start(); // Démarre la session PHP
            
            // RÉGÉNÉRATION ID = Protection contre la fixation de session
            if (!isset($_SESSION['initiated'])) { // Vérifie si la clé 'initiated' n'existe pas dans $_SESSION
                session_regenerate_id(true); // Génère un nouvel ID de session, true = supprime l'ancienne session
                $_SESSION['initiated'] = true; // Marque la session comme initialisée
            }
        }
    }
    
    /**
     * Connecte un utilisateur et stocke ses données en session
     * 
     * @param array $userData Données de l'utilisateur
     */
    public static function setUser($userData) { // Méthode statique qui prend un array de données utilisateur
        // RÉGÉNÉRATION ID : Nouvel ID à chaque connexion pour la sécurité
        session_regenerate_id(true); // Régénère l'ID de session à chaque connexion
        
        $_SESSION['user_id'] = $userData['id']; // Stocke l'ID utilisateur dans la session
        $_SESSION['user_pseudo'] = $userData['pseudo']; // Stocke le pseudo dans la session
        $_SESSION['user_role'] = $userData['role']; // Stocke le rôle (admin, moderateur, utilisateur normal)
        $_SESSION['user_type_utilisateur'] = $userData['type_utilisateur']; // Stocke le type (avocat, psychologue, etc.)
        $_SESSION['last_activity'] = time(); // Stocke le timestamp actuel pour gérer l'expiration
        
        // Marquer comme connecté
        $_SESSION['logged_in'] = true; // Flag indiquant que l'utilisateur est connecté
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn() { // Méthode statique qui retourne un booléen
        return isset($_SESSION['user_id']) && // Vérifie que user_id existe
               !empty($_SESSION['user_id']) && // Vérifie que user_id n'est pas vide
               isset($_SESSION['logged_in']) && // Vérifie que logged_in existe
               $_SESSION['logged_in'] === true; // Vérifie que logged_in est strictement égal à true
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     */
    public static function getUserId() { // Méthode statique
        return $_SESSION['user_id'] ?? null; // Retourne user_id ou null si n'existe pas
    }
    
    /**
     * Récupère le pseudo de l'utilisateur connecté
     */
    public static function getUserPseudo() { // Méthode statique
        return $_SESSION['user_pseudo'] ?? null; // Retourne user_pseudo ou null si n'existe pas
    }
    
    /**
     * Récupère le type d'utilisateur (avocat, psychologue, etc.)
     */
    public static function getUserType() { // Méthode statique
        if (self::isLoggedIn()) { // Vérifie d'abord si l'utilisateur est connecté
            return $_SESSION['user_type_utilisateur'] ?? 'inconnu'; // Retourne le type ou 'inconnu' si non défini
        }
        return 'visiteur'; // Retourne 'visiteur' si non connecté
    }
    
    /**
     * Récupère le rôle de l'utilisateur (admin, moderateur, utilisateur)
     */
    public static function getUserRole() { // Méthode statique
        if (self::isLoggedIn()) { // Vérifie d'abord si l'utilisateur est connecté
            return $_SESSION['user_role'] ?? 'utilisateur'; // Retourne le rôle ou 'utilisateur' par défaut
        }
        return 'visiteur'; // Retourne 'visiteur' si non connecté
    }
    
    
    // Vérifie si l'utilisateur est administrateur:
    
    public static function isAdmin() { // Méthode statique qui retourne un booléen
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin'; // True si connecté ET rôle est 'admin'
    }
    
    
    // Vérifie si l'utilisateur est modérateur:
  
    public static function isModerator() { // Méthode statique qui retourne un booléen
        return self::isLoggedIn() && $_SESSION['user_role'] === 'moderateur'; // True si connecté ET rôle est 'moderateur'
    }
    
    
    // Vérifie si l'utilisateur est un professionnel
     
    public static function isProfessional() { // Méthode statique qui retourne un booléen
        $professionalTypes = ['avocat', 'psychologue', 'mediateur']; // Array des types professionnels
        return self::isLoggedIn() && in_array($_SESSION['user_type_utilisateur'] ?? '', $professionalTypes); // True si connecté ET type dans la liste
    }
    
    /**
     * Stocke un message flash dans la session
     */
    public static function setFlashMessage($type, $message) { // Méthode statique avec 2 paramètres
        $_SESSION['flash_messages'][$type][] = $message; // Ajoute le message dans l'array flash_messages sous la clé $type
    }
    
    /**
     * Récupère un type spécifique de message flash
     * 
     * @param string $type
     * @return array
     */
    public static function getFlashMessage($type) { // Méthode statique qui prend un type
        $messages = $_SESSION['flash_messages'][$type] ?? []; // Récupère les messages du type ou array vide
        unset($_SESSION['flash_messages'][$type]); // Supprime les messages après lecture
        return $messages; // Retourne les messages
    }
    
    /**
     * Récupère et efface tous les messages flash
     */
    public static function getFlashMessages() { // Méthode statique
        $messages = $_SESSION['flash_messages'] ?? []; // Récupère tous les messages ou array vide
        unset($_SESSION['flash_messages']); // Supprime tous les messages flash
        return $messages; // Retourne les messages
    }

    /**
     * Vérifie si la session est valide et active
     */
    public static function validateSession() { // Méthode statique qui retourne un booléen
        // Vérifier si l'utilisateur est connecté
        if (!self::isLoggedIn()) { // Si pas connecté
            return false; // Retourne false
        }
        
        // Vérifier si la session n'a pas expiré
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) { // Si dernière activité > 30 min (1800 sec)
            // Si la dernière activité date de plus de 30 minutes, déconnexion
            self::endSession(); // Appelle endSession() pour terminer la session
            return false; // Retourne false
        }
        
        // Mettre à jour le timestamp de dernière activité
        $_SESSION['last_activity'] = time(); // Met à jour last_activity avec le timestamp actuel
        
        return true; // Retourne true si session valide
    }

    /**
     * Termine la session (utilisé pour la déconnexion ou en cas de session invalide)
     */
    public static function endSession() { // Méthode statique
        // SÉCURITÉ : Effacer toutes les données de session
        $_SESSION = array(); // Remplace $_SESSION par un array vide
        
        // SÉCURITÉ : Supprimer le cookie de session
        if (isset($_COOKIE[session_name()])) { // Vérifie si le cookie de session existe
            setcookie(session_name(), '', time() - 42000, '/'); // Expire le cookie (time - 42000 = dans le passé)
        }
        
        // SÉCURITÉ : Détruire complètement la session
        session_destroy(); // Détruit la session côté serveur
    }
    
    /**
     * SÉCURITÉ : Régénère l'ID de session (à utiliser périodiquement)
     */
    public static function regenerateId() { // Méthode statique
        if (session_status() === PHP_SESSION_ACTIVE) { // Vérifie que la session est active
            session_regenerate_id(true); // Régénère l'ID, true = supprime l'ancienne session
        }
    }
    
    /**
     * Étend la durée de vie de la session (pour "Se souvenir de moi")
     * 
     * @param int $days Nombre de jours
     */
    public static function extendSession($days = 30) { // Méthode statique avec paramètre par défaut de 30 jours
        // Définir la durée de vie du cookie de session
        $lifetime = $days * 24 * 60 * 60; // Convertit les jours en secondes (jours * heures * minutes * secondes)
        
        // Mettre à jour le cookie de session
        if (isset($_COOKIE[session_name()])) { // Vérifie que le cookie existe
            setcookie( // Met à jour le cookie
                session_name(), // Nom du cookie de session
                $_COOKIE[session_name()], // Valeur actuelle du cookie
                time() + $lifetime, // Nouvelle date d'expiration
                '/' // Path du cookie (racine)
            );
        }
        
        // Optionnel : stocker une date d'expiration étendue dans la session
        $_SESSION['extended_expiration'] = time() + $lifetime; // Stocke la date d'expiration étendue
    }
}