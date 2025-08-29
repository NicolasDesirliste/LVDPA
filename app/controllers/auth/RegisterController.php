<?php
// app/controllers/auth/RegisterController.php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Services\DataSanitizer;
use App\Services\SessionManager;
use App\Services\UserRegistrationService;
use App\Core\ViewSpa;

/**
 * Classe RegisterController
 * 
 * Responsabilité: Gérer l'inscription des utilisateurs
 * - Affiche le formulaire
 * - Traite l'inscription en la délégant à UserRegistration.php 
 */
class RegisterController extends Controller {
    private UserRegistrationService $registrationService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct();
        $this->registrationService = new UserRegistrationService();
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function showForm(): void {
    if ($this->isAuthenticated()) {
        $this->redirect('/dashboard');
    }
    
    ViewSpa::render('auth/register', [
    'title' => 'Inscription'
    ]);
}
    
    /**
     * Traite le formulaire d'inscription
     */
    public function process(): void {
        // Si déjà connecté, rediriger
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        // Vérifier la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        // Récupérer et nettoyer les données
        $data = $this->sanitizeFormData();
        
        // Appeler le service d'inscription
        $result = $this->registrationService->register($data);
        
        if ($result['success']) {
            $this->setFlash("Inscription réussie ! Vérifiez votre email pour activer votre compte.", "success");
            $this->redirect('/login');
        } else {
            // Stocker les erreurs et les données pour le formulaire
            $this->handleRegistrationErrors($result['errors'], $data);
        }
    }
    
    /**
     * Nettoie les données du formulaire
     * 
     * @return array
     */
    private function sanitizeFormData(): array {
        return [
            'type_utilisateur' => DataSanitizer::cleanText($_POST['type_utilisateur'] ?? ''),
            'departement' => DataSanitizer::cleanInt($_POST['departement'] ?? ''),
            'pseudo' => DataSanitizer::cleanText($_POST['pseudo'] ?? ''),
            'email' => DataSanitizer::cleanText($_POST['email'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '', // Pas de sanitize sur le mot de passe
            'mot_de_passe_confirmation' => $_POST['mot_de_passe_confirmation'] ?? ''
        ];
    }
    
    /**
     * Gère les erreurs d'inscription
     * 
     * @param array $errors
     * @param array $formData
     */
    private function handleRegistrationErrors(array $errors, array $formData): void {
        // Stocker les erreurs pour l'affichage
        SessionManager::setFlashMessage('errors', $errors);
        
        // Stocker les données pour pré-remplir le formulaire (sauf mots de passe)
        unset($formData['mot_de_passe'], $formData['mot_de_passe_confirmation']);
        SessionManager::setFlashMessage('form_data', $formData);
        
        $this->redirect('/register');
    }
}