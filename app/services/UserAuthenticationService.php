<?php
// app/services/UserAuthenticationService.php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserValidator;
use App\Services\SessionManager;

/**
 * Classe UserAuthenticationService
 * 
 * Responsabilité: Gérer la logique d'authentification
 * - Valide les données de connexion
 * - Vérifie les identifiants
 * - Gère l'état de connexion
 */
class UserAuthenticationService {
    private UserRepository $userRepository;
    private UserValidator $userValidator;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userValidator = new UserValidator();
    }
    
    /**
     * Authentifie un utilisateur
     * 
     * @param array $credentials ['pseudo', 'email', 'mot_de_passe', 'remember']
     * @return array ['success' => bool, 'message' => string, 'user' => ?User, 'errors' => array]
     */ /*
    public function authenticate(array $credentials): array {
        // 1. Valider les données
        $validationErrors = $this->validateCredentials($credentials);
        if (!empty($validationErrors)) {
            return [
                'success' => false,
                'message' => 'Données invalides',
                'user' => null,
                'errors' => $validationErrors
            ];
        }
        
        // 2. Chercher l'utilisateur
        $user = $this->findUser($credentials);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Identifiants incorrects',
                'user' => null,
                'errors' => ['Identifiants incorrects']
            ];
        } */
       // tests ----------------------------------------------------------------------------------
       public function authenticate(array $credentials): array {
    // Debug temporaire
    error_log("=== AUTHENTICATE APPELÉ ===");
    error_log("Credentials reçus: " . print_r($credentials, true));
    
    // 1. Valider les données
    $validationErrors = $this->validateCredentials($credentials);
    error_log("Erreurs de validation: " . print_r($validationErrors, true));
    
    if (!empty($validationErrors)) {
        return [
            'success' => false,
            'message' => 'Données invalides',
            'user' => null,
            'errors' => $validationErrors
        ];
    }
    
    // 2. Chercher l'utilisateur
    $user = $this->findUser($credentials);
    error_log("Utilisateur trouvé: " . ($user ? "OUI - ID: " . $user->getId() : "NON"));
    
    if (!$user) {
        return [
            'success' => false,
            'message' => 'Identifiants incorrects',
            'user' => null,
            'errors' => ['Identifiants incorrects']
        ];
    } // fin de tests -------------------------------------------------------------------
        
        // 3. Vérifier le mot de passe
        if (!$this->verifyPassword($credentials['mot_de_passe'], $user->get('mot_de_passe'))) {
            return [
                'success' => false,
                'message' => 'Identifiants incorrects',
                'user' => null,
                'errors' => ['Identifiants incorrects']
            ];
        }
        
        // 4. Vérifier l'état du compte
        $accountCheck = $this->checkAccountStatus($user);
        if (!$accountCheck['success']) {
            return $accountCheck;
        }
        
        // 5. Créer la session
        $this->createUserSession($user, $credentials['remember'] ?? false);
        
        // 6. Mettre à jour la dernière connexion
        $this->userRepository->updateLastConnection($user->getId());
        
        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $user,
            'errors' => []
        ];
    }
    
    /**
     * Valide les données de connexion
     * 
     * @param array $credentials
     * @return array Tableau des erreurs
     */
    private function validateCredentials(array $credentials): array {
        $errors = [];
        
        // Validation basique des champs requis
        if (empty($credentials['pseudo'])) {
            $errors['pseudo'] = "Le pseudonyme est obligatoire";
        }
        
        if (empty($credentials['email'])) {
            $errors['email'] = "L'email est obligatoire";
        } elseif (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide";
        }
        
        if (empty($credentials['mot_de_passe'])) {
            $errors['mot_de_passe'] = "Le mot de passe est obligatoire";
        }
        
        return $errors;
    }
    
    /**
     * Trouve l'utilisateur par email et vérifie le pseudo
     * 
     * @param array $credentials
     * @return User|null
     */ 
    // Bonne méthode:
     /*
    private function findUser(array $credentials): ?User {
        // Chercher par email
        $user = $this->userRepository->findByEmail($credentials['email']);
        
        if (!$user) {
            return null;
        }
        
        // Vérifier que le pseudo correspond
        if ($user->getPseudo() !== $credentials['pseudo']) {
            return null;
        }
        
        return $user;
    } */

    // TEST Method: --------------------------------------------------------
    private function findUser(array $credentials): ?User {
    error_log("=== FINDUSER DEBUG ===");
    error_log("Email recherché: '" . $credentials['email'] . "'");
    error_log("Pseudo recherché: '" . $credentials['pseudo'] . "'");
    
    // Chercher par email
    $user = $this->userRepository->findByEmail($credentials['email']);
    
    if (!$user) {
        error_log("Aucun utilisateur trouvé avec cet email");
        return null;
    }
    
    error_log("Utilisateur trouvé dans la BDD:");
    error_log("- ID: " . $user->getId());
    error_log("- Pseudo BDD: '" . $user->getPseudo() . "'");
    error_log("- Email BDD: '" . $user->get('email') . "'");
    
    // Vérifier que le pseudo correspond
    if ($user->getPseudo() !== $credentials['pseudo']) {
        error_log("ÉCHEC: Les pseudos ne correspondent pas");
        error_log("Comparaison: '" . $user->getPseudo() . "' !== '" . $credentials['pseudo'] . "'");
        return null;
    }
    
    error_log("SUCCESS: Pseudo et email correspondent");
    return $user;
} // FIN de TESTS --------------------------------------------------------------

    
    /**
     * Vérifie le mot de passe
     * 
     * @param string $plainPassword
     * @param string $hashedPassword
     * @return bool
     */
    private function verifyPassword(string $plainPassword, string $hashedPassword): bool {
        return password_verify($plainPassword, $hashedPassword);
    }
    
    /**
     * Vérifie l'état du compte (banni, vérifié)
     * 
     * @param User $user
     * @return array
     */
    private function checkAccountStatus(User $user): array {
        // Compte banni ?
        if ($user->isBanned()) {
            return [
                'success' => false,
                'message' => 'Votre compte a été suspendu',
                'user' => null,
                'errors' => ['Votre compte a été suspendu. Contactez l\'administration.']
            ];
        }
        
        // Compte non vérifié ?
        if (!$user->isVerified()) {
            return [
                'success' => false,
                'message' => 'Compte non vérifié',
                'user' => null,
                'errors' => ['Veuillez vérifier votre email avant de vous connecter.']
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Crée la session utilisateur
     * 
     * @param User $user
     * @param bool $remember
     */
    private function createUserSession(User $user, bool $remember = false): void {
        // Créer la session avec toutes les données nécessaires
        SessionManager::setUser([
            'id' => $user->getId(),
            'pseudo' => $user->getPseudo(),
            'role' => $user->get('role'),
            'type_utilisateur' => $user->get('type_utilisateur')
        ]);
        
        // Si "Se souvenir de moi"
        if ($remember) {
            // AFPT (après examen): Implémenter un système de token persistant
            // Pour l'instant, augmentation artificielle de la durée de vie du cookie
            
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     * 
     * @return array
     */
    public function logout(): array {
        SessionManager::endSession();
        
        return [
            'success' => true,
            'message' => 'Déconnexion réussie',
            'user' => null,
            'errors' => []
        ];
    }
}