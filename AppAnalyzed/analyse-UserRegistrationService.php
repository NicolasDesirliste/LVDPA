<?php
// app/services/UserRegistrationService.php
namespace App\Services; // Namespace dans le dossier Services

use App\Models\User; // Import du modèle User
use App\Repositories\UserRepository; // Import du repository
use App\Services\UserValidator; // Import du validateur
use App\Services\EmailService; // Import du service email

/**
 * Classe UserRegistrationService
 * 
 * Responsabilité : Gérer la logique métier de l'inscription
 * - Prépare les données (hash, token)
 * - Coordonne validation et sauvegarde
 * - Déclenche l'envoi d'email
 */
class UserRegistrationService { // Déclaration de la classe
    private UserRepository $userRepository; // Propriété privée pour le repository
    private UserValidator $userValidator; // Propriété privée pour le validateur
    private EmailService $emailService; // Propriété privée pour le service email
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        $this->userRepository = new UserRepository(); // Instancie le repository
        $this->userValidator = new UserValidator(); // Instancie le validateur
        $this->emailService = new EmailService(); // Instancie le service email
    }
    
    /**
     * Inscrit un nouvel utilisateur
     * 
     * @param array $data Données du formulaire
     * @return array ['success' => bool, 'errors' => array, 'user' => ?User]
     */
    public function register(array $data): array { // Méthode publique qui prend des données
        // 1. Valider les données
        if (!$this->userValidator->validateCreation($data)) { // Si validation échoue
            return [ // Retourne échec avec erreurs
                'success' => false,
                'errors' => $this->userValidator->getErrors(), // Récupère les erreurs du validateur
                'user' => null
            ];
        }
        
        // 2. Vérifier l'unicité
        $uniquenessErrors = $this->checkUniqueness($data); // Vérifie email et pseudo uniques
        if (!empty($uniquenessErrors)) { // Si des erreurs d'unicité
            return [ // Retourne échec
                'success' => false,
                'errors' => $uniquenessErrors,
                'user' => null
            ];
        }
        
        // 3. Préparer l'utilisateur
        $user = $this->prepareUser($data); // Crée l'objet User avec les données préparées
        
        // 4. Sauvegarder en base
        if (!$this->userRepository->create($user)) { // Si échec de création en BDD
            return [ // Retourne échec
                'success' => false,
                'errors' => ['Une erreur est survenue lors de l\'inscription.'],
                'user' => null
            ];
        }
        
        // 5. Envoyer l'email de vérification
        $this->sendVerificationEmail($user); // Envoie l'email
        
        return [ // Retourne succès
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
    private function checkUniqueness(array $data): array { // Méthode privée
        $errors = []; // Initialise tableau d'erreurs
        
        if ($this->userRepository->emailExists($data['email'])) { // Si email existe déjà
            $errors['email'] = "Cet email est déjà utilisé."; // Ajoute l'erreur
        }
        
        if ($this->userRepository->pseudoExists($data['pseudo'])) { // Si pseudo existe déjà
            $errors['pseudo'] = "Ce pseudo est déjà pris."; // Ajoute l'erreur
        }
        
        return $errors; // Retourne les erreurs
    }
    
    /**
     * Prépare l'objet User avec toutes les données nécessaires
     * 
     * @param array $data
     * @return User
     */
    private function prepareUser(array $data): User { // Méthode privée qui retourne un User
        $user = new User(); // Crée un nouvel objet User
        
        // Données du formulaire
        $user->set('pseudo', $data['pseudo']); // Définit le pseudo
        $user->set('email', $data['email']); // Définit l'email
        $user->set('departement', $data['departement']); // Définit le département
        $user->set('type_utilisateur', $data['type_utilisateur']); // Définit le type d'utilisateur
        
        // Mot de passe hashé
        $user->set('mot_de_passe', password_hash($data['mot_de_passe'], PASSWORD_DEFAULT)); // Hash le mot de passe avec l'algo par défaut
        
        // Données par défaut
        $user->set('role', 'utilisateur'); // Rôle par défaut
        $user->set('est_verifie', 0); // Non vérifié par défaut
        $user->set('est_banni', 0); // Non banni par défaut
        
        // Token de vérification
        $user->set('token', $this->generateVerificationToken()); // Génère un token unique
        $user->set('token_expiration', date('Y-m-d H:i:s', strtotime('+24 hours'))); // Expire dans 24h
        
        return $user; // Retourne l'objet User préparé
    }
    
    /**
     * Génère un token de vérification sécurisé
     * 
     * @return string
     */
    private function generateVerificationToken(): string { // Méthode privée qui retourne string
        return bin2hex(random_bytes(32)); // Génère 32 octets aléatoires et convertit en hexadécimal
    }
    
    /**
     * Envoie l'email de vérification
     * 
     * @param User $user
     */
    private function sendVerificationEmail(User $user): void { // Méthode privée sans retour
        $this->emailService->sendVerificationEmail($user); // Délègue l'envoi au service email
    }
    
}