<?php
// app/controllers/auth/LoginController.php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\ViewSpa;
use App\Services\DataSanitizer;
use App\Services\SessionManager;
use App\Services\UserAuthenticationService;

/**
 * Classe LoginController
 * 
 * Responsabilité : Orchestrer le processus de connexion
 * - Affiche le formulaire
 * - Délègue l'authentification à UserAuthenticationService
 * - Gère les réponses (redirect ou JSON)
 */
class LoginController extends Controller {
    private UserAuthenticationService $authService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct();
        $this->authService = new UserAuthenticationService();
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function showForm(): void {
    // Si déjà connecté, rediriger
    if ($this->isAuthenticated()) {
        $this->redirect('/LVDPA/dashboard');
        return;
    }
    
    // Récupérer les messages flash
    $errors = SessionManager::getFlashMessage('login_errors');
    $success = SessionManager::getFlashMessage('success');
    
    // Afficher le formulaire
    ViewSpa::render('auth/login-content', [
        'title' => 'Connexion',
        'errors' => $errors,
        'success_message' => $success[0] ?? null  // <-- Prendre le premier élément ou null
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
    public function process(): void {
    /* Debug temporaire pour tester
    error_log("=== PROCESS LOGIN APPELÉ ===");
    error_log("Méthode: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST brut: " . print_r($_POST, true));
    */

    // Si déjà connecté, gérer selon le type de requête
    if ($this->isAuthenticated()) {
        error_log("Utilisateur déjà connecté");
        $this->handleAlreadyAuthenticated();
        return;
    }
    
    // Vérifier la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("Pas une requête POST, redirection");
        $this->redirect('/LVDPA/login');
        return;
    }
    
    // Récupérer et nettoyer les données
    $credentials = $this->sanitizeCredentials();
    error_log("Credentials après sanitize: " . print_r($credentials, true));
    
    // Déléguer l'authentification au service
    $result = $this->authService->authenticate($credentials);
    error_log("Résultat auth: " . print_r($result, true));
    
    // Gérer le résultat
    if ($result['success']) {
        error_log("Connexion réussie pour: " . $result['user']->getPseudo());
        $this->handleSuccessfulLogin($result['user']);
    } else {
        error_log("Connexion échouée");
        $this->handleFailedLogin($result['errors']);
    }
    }
    
// FIN DE TEST ----------------------------------------------------------------------------------------    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout(): void {
        // Déléguer la déconnexion au service
        $result = $this->authService->logout();
        
        // Répondre selon le type de requête
        if (ViewSpa::isAjaxRequest()) {
            ViewSpa::json([
                'success' => true,
                'message' => $result['message'],
                'redirect' => '/LVDPA/'
            ]);
        } else {
            $this->setFlash($result['message'], "success");
            $this->redirect('/LVDPA/');
        }
    }
    
    /**
     * Nettoie les données de connexion
     * 
     * @return array
     */
    private function sanitizeCredentials(): array {
        return [
            'pseudo' => DataSanitizer::cleanText($_POST['pseudo'] ?? ''),
            'email' => DataSanitizer::cleanText($_POST['email'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '', // Pas de sanitization sur le mot de passe
            'remember' => isset($_POST['remember'])
        ];
    }
    
    /**
     * Gère le cas où l'utilisateur est déjà connecté
     */
    private function handleAlreadyAuthenticated(): void {
        if (ViewSpa::isAjaxRequest()) {
            ViewSpa::json([
                'success' => false,
                'message' => 'Vous êtes déjà connecté',
                'redirect' => '/LVDPA/dashboard'
            ]);
        } else {
            $this->redirect('/LVDPA/dashboard');
        }
    }
    
    /**
     * Gère une connexion réussie
     * 
     * @param \App\Models\User $user
     */
    private function handleSuccessfulLogin($user): void {
    if (ViewSpa::isAjaxRequest()) {
        // Formater la date de dernière connexion
        $lastConnection = $user->get('derniere_connexion');
        if ($lastConnection && $lastConnection !== '0000-00-00 00:00:00') {
            $date = new \DateTime($lastConnection);
            $dateFormatted = $date->format('d/m/Y à H:i');
            $welcomeMessage = "Bon retour parmi nous {$user->getPseudo()} ! Votre dernière connexion date du {$dateFormatted}, nous sommes heureux de vous revoir !";
        } else {
            $welcomeMessage = "Bienvenue {$user->getPseudo()} ! C'est votre première connexion, nous sommes ravis de vous accueillir !";
        }
        
        ViewSpa::json([
            'success' => true,
            'message' => "Connexion réussie !",
            'redirect' => null,  // <-- null au lieu de '/LVDPA/dashboard'
            'userType' => ucfirst($user->get('type_utilisateur')),
            'welcomeMessage' => $welcomeMessage // <-- NOUVEAU : message préformaté
        ]);
    } else {
        $this->setFlash("Bienvenue {$user->getPseudo()} !", "success");
        $this->redirect('/LVDPA/');  // <-- '/' au lieu de '/dashboard'
    }
}
    
    /**
     * Gère une connexion échouée
     * 
     * @param array $errors
     */
    private function handleFailedLogin(array $errors): void {
        if (ViewSpa::isAjaxRequest()) {
            ViewSpa::json([
                'success' => false,
                'errors' => $errors,
                'message' => 'Échec de la connexion'
            ]);
        } else {
            SessionManager::setFlashMessage('login_errors', $errors);
            $this->redirect('/LVDPA/login');
        }
    }
}