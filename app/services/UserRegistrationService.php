<?php
// app/services/UserRegistrationService.php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserValidator;
use App\Services\EmailService;

/**
 * Classe UserRegistrationService
 * 
 * Responsabilité : Gérer la logique métier de l'inscription
 * - Prépare les données (hash, token)
 * - Coordonne validation et sauvegarde
 * - Déclenche l'envoi d'email
 */
class UserRegistrationService {
    private UserRepository $userRepository;
    private UserValidator $userValidator;
    private EmailService $emailService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userValidator = new UserValidator();
        $this->emailService = new EmailService();
    }
    
    /**
     * Inscrit un nouvel utilisateur
     * 
     * @param array $data Données du formulaire
     * @return array ['success' => bool, 'errors' => array, 'user' => ?User]
     */
    public function register(array $data): array {
        // 1. Valider les données
        if (!$this->userValidator->validateCreation($data)) {
            return [
                'success' => false,
                'errors' => $this->userValidator->getErrors(),
                'user' => null
            ];
        }
        
        // 2. Vérifier l'unicité
        $uniquenessErrors = $this->checkUniqueness($data);
        if (!empty($uniquenessErrors)) {
            return [
                'success' => false,
                'errors' => $uniquenessErrors,
                'user' => null
            ];
        }
        
        // 3. Préparer l'utilisateur
        $user = $this->prepareUser($data);
        
        // 4. Sauvegarder en base
        if (!$this->userRepository->create($user)) {
            return [
                'success' => false,
                'errors' => ['Une erreur est survenue lors de l\'inscription.'],
                'user' => null
            ];
        }
        
        // 5. Envoyer l'email de vérification
        $this->sendVerificationEmail($user);
        
        return [
            'success' => true,
            'errors' => [],
            'user' => $user
        ];
    }
    
    /**
     * Vérifie l'unicité de l'email et du pseudo
     * 
     * @param array $data
     * @return array Tableau des erreurs
     */
    private function checkUniqueness(array $data): array {
        $errors = [];
        
        if ($this->userRepository->emailExists($data['email'])) {
            $errors['email'] = "Cet email est déjà utilisé.";
        }
        
        if ($this->userRepository->pseudoExists($data['pseudo'])) {
            $errors['pseudo'] = "Ce pseudo est déjà pris.";
        }
        
        return $errors;
    }
    
    /**
     * Prépare l'objet User avec toutes les données nécessaires
     * 
     * @param array $data
     * @return User
     */
    private function prepareUser(array $data): User {
        $user = new User();
        
        // Données du formulaire
        $user->set('pseudo', $data['pseudo']);
        $user->set('email', $data['email']);
        $user->set('departement', $data['departement']);
        $user->set('type_utilisateur', $data['type_utilisateur']);
        
        // Mot de passe hashé
        $user->set('mot_de_passe', password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        
        // Données par défaut
        $user->set('role', 'utilisateur');
        $user->set('est_verifie', 0);
        $user->set('est_banni', 0);
        
        // Token de vérification
        $user->set('token', $this->generateVerificationToken());
        $user->set('token_expiration', date('Y-m-d H:i:s', strtotime('+24 hours')));
        
        return $user;
    }
    
    /**
     * Génère un token de vérification sécurisé
     * 
     * @return string
     */
    private function generateVerificationToken(): string {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Envoie l'email de vérification
     * 
     * @param User $user
     */
    private function sendVerificationEmail(User $user): void {
        $this->emailService->sendVerificationEmail($user);
    }
    
}