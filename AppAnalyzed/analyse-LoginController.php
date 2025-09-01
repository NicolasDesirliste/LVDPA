<?php
// app/controllers/auth/LoginController.php
namespace App\Controllers\Auth; // Namespace dans le sous-dossier Auth

use App\Core\Controller; // Import de la classe Controller de base
use App\Core\ViewSpa; // Import de ViewSpa pour les vues SPA
use App\Services\DataSanitizer; // Import du service de nettoyage de données
use App\Services\SessionManager; // Import du gestionnaire de sessions
use App\Services\UserAuthenticationService; // Import du service d'authentification

/**
 * Classe LoginController
 * 
 * Responsabilité : Orchestrer le processus de connexion
 * - Affiche le formulaire
 * - Délègue l'authentification à UserAuthenticationService
 * - Gère les réponses (redirect ou JSON)
 */
class LoginController extends Controller { // Hérite de Controller
    private UserAuthenticationService $authService; // Propriété privée pour le service d'auth
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        parent::__construct(); // Appelle le constructeur parent
        $this->authService = new UserAuthenticationService(); // Instancie le service d'authentification
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function showForm(): void { // Méthode publique pour afficher le formulaire
    // Si déjà connecté, rediriger
    if ($this->isAuthenticated()) { // Vérifie si l'utilisateur est déjà connecté
        $this->redirect('/LVDPA/dashboard'); // Redirige vers le dashboard
        return; // Sort de la fonction
    }
    
    // Récupérer les messages flash
    $errors = SessionManager::getFlashMessage('login_errors'); // Récupère les erreurs de connexion
    $success = SessionManager::getFlashMessage('success'); // Récupère les messages de succès
    
    // Afficher le formulaire
    ViewSpa::render('auth/login-content', [ // Utilise ViewSpa pour rendre la vue
        'title' => 'Connexion', // Titre de la page
        'errors' => $errors, // Passe les erreurs à la vue
        'success_message' => $success[0] ?? null  // Prend le premier message de succès ou null
    ]);
}
    
    /**
     * Traite la tentative de connexion
     */ /*
    public function process(): void {
        // Si déjà connecté, gérer selon le type de requête
        if ($this->isAuthenticated()) {
            $this->handleAlreadyAuthenticated();
            return;
        }
        
        // Vérifier la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/LVDPA/login');
            return;
        }
        
        // Récupérer et nettoyer les données
        $credentials = $this->sanitizeCredentials();
        
        // Déléguer l'authentification au service
        $result = $this->authService->authenticate($credentials);
        
        // Gérer le résultat
        if ($result['success']) {
            $this->handleSuccessfulLogin($result['user']);
        } else {
            $this->handleFailedLogin($result['errors']);
        }
    } */
// TEST ------------------------------------------------------------------------
    public function process(): void { // Méthode publique pour traiter la connexion
    /* Debug temporaire pour tester
    error_log("=== PROCESS LOGIN APPELÉ ===");
    error_log("Méthode: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST brut: " . print_r($_POST, true));
    */

    // Si déjà connecté, gérer selon le type de requête
    if ($this->isAuthenticated()) { // Vérifie si déjà authentifié
        error_log("Utilisateur déjà connecté"); // Log de debug
        $this->handleAlreadyAuthenticated(); // Appelle la méthode pour gérer ce cas
        return; // Sort de la fonction
    }
    
    // Vérifier la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Vérifie que c'est une requête POST
        error_log("Pas une requête POST, redirection"); // Log de debug
        $this->redirect('/LVDPA/login'); // Redirige vers le formulaire
        return; // Sort de la fonction
    }
    
    // Récupérer et nettoyer les données
    $credentials = $this->sanitizeCredentials(); // Appelle la méthode pour nettoyer les données
    error_log("Credentials après sanitize: " . print_r($credentials, true)); // Log de debug
    
    // Déléguer l'authentification au service
    $result = $this->authService->authenticate($credentials); // Appelle le service d'authentification
    error_log("Résultat auth: " . print_r($result, true)); // Log de debug
    
    // Gérer le résultat
    if ($result['success']) { // Si connexion réussie
        error_log("Connexion réussie pour: " . $result['user']->getPseudo()); // Log de debug
        $this->handleSuccessfulLogin($result['user']); // Gère la connexion réussie
    } else { // Si échec
        error_log("Connexion échouée"); // Log de debug
        $this->handleFailedLogin($result['errors']); // Gère l'échec
    }
    }
    
// FIN DE TEST ----------------------------------------------------------------------------------------    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout(): void { // Méthode publique pour la déconnexion
        // Déléguer la déconnexion au service
        $result = $this->authService->logout(); // Appelle la méthode logout du service
        
        // Répondre selon le type de requête
        if (ViewSpa::isAjaxRequest()) { // Si c'est une requête AJAX
            ViewSpa::json([ // Répond en JSON
                'success' => true, // Succès
                'message' => $result['message'], // Message du service
                'redirect' => '/LVDPA/' // URL de redirection
            ]);
        } else { // Si requête normale
            $this->setFlash($result['message'], "success"); // Définit un message flash
            $this->redirect('/LVDPA/'); // Redirige vers l'accueil
        }
    }
    
    /**
     * Nettoie les données de connexion
     * 
     * @return array
     */
    private function sanitizeCredentials(): array { // Méthode privée qui retourne un array
        return [ // Retourne un array avec les données nettoyées
            'pseudo' => DataSanitizer::cleanText($_POST['pseudo'] ?? ''), // Nettoie le pseudo ou string vide
            'email' => DataSanitizer::cleanText($_POST['email'] ?? ''), // Nettoie l'email ou string vide
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '', // Récupère le mot de passe sans nettoyage
            'remember' => isset($_POST['remember']) // Booléen : true si la checkbox est cochée
        ];
    }
    
    /**
     * Gère le cas où l'utilisateur est déjà connecté
     */
    private function handleAlreadyAuthenticated(): void { // Méthode privée sans retour
        if (ViewSpa::isAjaxRequest()) { // Si requête AJAX
            ViewSpa::json([ // Répond en JSON
                'success' => false, // Pas un succès
                'message' => 'Vous êtes déjà connecté', // Message d'erreur
                'redirect' => '/LVDPA/dashboard' // URL de redirection
            ]);
        } else { // Si requête normale
            $this->redirect('/LVDPA/dashboard'); // Redirige directement
        }
    }
    
    /**
     * Gère une connexion réussie
     * 
     * @param \App\Models\User $user
     */
    private function handleSuccessfulLogin($user): void { // Méthode privée qui prend un User
    if (ViewSpa::isAjaxRequest()) { // Si requête AJAX
        // Formater la date de dernière connexion
        $lastConnection = $user->get('derniere_connexion'); // Récupère la dernière connexion
        if ($lastConnection && $lastConnection !== '0000-00-00 00:00:00') { // Si date valide
            $date = new \DateTime($lastConnection); // Crée un objet DateTime
            $dateFormatted = $date->format('d/m/Y à H:i'); // Formate la date en français
            $welcomeMessage = "Bon retour parmi nous {$user->getPseudo()} ! Votre dernière connexion date du {$dateFormatted}, nous sommes heureux de vous revoir !"; // Message personnalisé
        } else { // Si première connexion
            $welcomeMessage = "Bienvenue {$user->getPseudo()} ! C'est votre première connexion, nous sommes ravis de vous accueillir !"; // Message de bienvenue
        }
        
        ViewSpa::json([ // Répond en JSON
            'success' => true, // Succès
            'message' => "Connexion réussie !", // Message court
            'redirect' => null,  // Pas de redirection (géré côté JS)
            'userType' => ucfirst($user->get('type_utilisateur')), // Type avec majuscule
            'welcomeMessage' => $welcomeMessage // Message de bienvenue personnalisé
        ]);
    } else { // Si requête normale
        $this->setFlash("Bienvenue {$user->getPseudo()} !", "success"); // Message flash
        $this->redirect('/LVDPA/');  // Redirige vers l'accueil
    }
}
    
    /**
     * Gère une connexion échouée
     * 
     * @param array $errors
     */
    private function handleFailedLogin(array $errors): void { // Méthode privée avec array d'erreurs
        if (ViewSpa::isAjaxRequest()) { // Si requête AJAX
            ViewSpa::json([ // Répond en JSON
                'success' => false, // Échec
                'errors' => $errors, // Liste des erreurs
                'message' => 'Échec de la connexion' // Message général
            ]);
        } else { // Si requête normale
            SessionManager::setFlashMessage('login_errors', $errors); // Stocke les erreurs en session
            $this->redirect('/LVDPA/login'); // Redirige vers le formulaire
        }
    }
}