<?php
// app/services/UserAuthenticationService.php
namespace App\Services; // Namespace dans le dossier Services

use App\Models\User; // Import du modèle User
use App\Repositories\UserRepository; // Import du repository
use App\Services\UserValidator; // Import du validateur
use App\Services\SessionManager; // Import du gestionnaire de sessions

/**
 * Classe UserAuthenticationService
 * 
 * Responsabilité: Gérer la logique d'authentification
 * - Valide les données de connexion
 * - Vérifie les identifiants
 * - Gère l'état de connexion
 */
class UserAuthenticationService { // Déclaration de la classe
    private UserRepository $userRepository; // Propriété privée pour le repository
    private UserValidator $userValidator; // Propriété privée pour le validateur
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        $this->userRepository = new UserRepository(); // Instancie le repository
        $this->userValidator = new UserValidator(); // Instancie le validateur
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
       public function authenticate(array $credentials): array { // Méthode publique qui prend des credentials
    // Debug temporaire
    error_log("=== AUTHENTICATE APPELÉ ==="); // Log de debug
    error_log("Credentials reçus: " . print_r($credentials, true)); // Log des données reçues
    
    // 1. Valider les données
    $validationErrors = $this->validateCredentials($credentials); // Appelle la validation
    error_log("Erreurs de validation: " . print_r($validationErrors, true)); // Log des erreurs
    
    if (!empty($validationErrors)) { // Si des erreurs de validation
        return [ // Retourne échec
            'success' => false,
            'message' => 'Données invalides',
            'user' => null,
            'errors' => $validationErrors
        ];
    }
    
    // 2. Chercher l'utilisateur
    $user = $this->findUser($credentials); // Cherche l'utilisateur
    error_log("Utilisateur trouvé: " . ($user ? "OUI - ID: " . $user->getId() : "NON")); // Log du résultat
    
    if (!$user) { // Si utilisateur non trouvé
        return [ // Retourne échec
            'success' => false,
            'message' => 'Identifiants incorrects',
            'user' => null,
            'errors' => ['Identifiants incorrects']
        ];
    } // fin de tests -------------------------------------------------------------------
        
        // 3. Vérifier le mot de passe
        if (!$this->verifyPassword($credentials['mot_de_passe'], $user->get('mot_de_passe'))) { // Compare les mots de passe
            return [ // Retourne échec si pas de correspondance
                'success' => false,
                'message' => 'Identifiants incorrects',
                'user' => null,
                'errors' => ['Identifiants incorrects']
            ];
        }
        
        // 4. Vérifier l'état du compte
        $accountCheck = $this->checkAccountStatus($user); // Vérifie le statut du compte
        if (!$accountCheck['success']) { // Si le compte a un problème
            return $accountCheck; // Retourne directement le résultat
        }
        
        // 5. Créer la session
        $this->createUserSession($user, $credentials['remember'] ?? false); // Crée la session avec remember optionnel
        
        // 6. Mettre à jour la dernière connexion
        $this->userRepository->updateLastConnection($user->getId()); // Met à jour dans la BDD
        
        return [ // Retourne succès
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
    private function validateCredentials(array $credentials): array { // Méthode privée de validation
        $errors = []; // Initialise tableau d'erreurs
        
        // Validation basique des champs requis
        if (empty($credentials['pseudo'])) { // Si pseudo vide
            $errors['pseudo'] = "Le pseudonyme est obligatoire"; // Ajoute l'erreur
        }
        
        if (empty($credentials['email'])) { // Si email vide
            $errors['email'] = "L'email est obligatoire"; // Ajoute l'erreur
        } elseif (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) { // Si email invalide
            $errors['email'] = "L'email n'est pas valide"; // Ajoute l'erreur
        }
        
        if (empty($credentials['mot_de_passe'])) { // Si mot de passe vide
            $errors['mot_de_passe'] = "Le mot de passe est obligatoire"; // Ajoute l'erreur
        }
        
        return $errors; // Retourne le tableau d'erreurs
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
    private function findUser(array $credentials): ?User { // Méthode privée qui retourne User ou null
    error_log("=== FINDUSER DEBUG ==="); // Log de debug
    error_log("Email recherché: '" . $credentials['email'] . "'"); // Log l'email
    error_log("Pseudo recherché: '" . $credentials['pseudo'] . "'"); // Log le pseudo
    
    // Chercher par email
    $user = $this->userRepository->findByEmail($credentials['email']); // Cherche dans la BDD
    
    if (!$user) { // Si pas trouvé
        error_log("Aucun utilisateur trouvé avec cet email"); // Log l'échec
        return null; // Retourne null
    }
    
    error_log("Utilisateur trouvé dans la BDD:"); // Log le succès
    error_log("- ID: " . $user->getId()); // Log l'ID
    error_log("- Pseudo BDD: '" . $user->getPseudo() . "'"); // Log le pseudo de la BDD
    error_log("- Email BDD: '" . $user->get('email') . "'"); // Log l'email de la BDD
    
    // Vérifier que le pseudo correspond
    if ($user->getPseudo() !== $credentials['pseudo']) { // Compare les pseudos (stricte)
        error_log("ÉCHEC: Les pseudos ne correspondent pas"); // Log l'échec
        error_log("Comparaison: '" . $user->getPseudo() . "' !== '" . $credentials['pseudo'] . "'"); // Log la comparaison
        return null; // Retourne null
    }
    
    error_log("SUCCESS: Pseudo et email correspondent"); // Log le succès
    return $user; // Retourne l'utilisateur
} // FIN de TESTS --------------------------------------------------------------

    
    /**
     * Vérifie le mot de passe
     * 
     * @param string $plainPassword
     * @param string $hashedPassword
     * @return bool
     */
    private function verifyPassword(string $plainPassword, string $hashedPassword): bool { // Méthode privée
        return password_verify($plainPassword, $hashedPassword); // Utilise password_verify de PHP
    }
    
    /**
     * Vérifie l'état du compte (banni, vérifié)
     * 
     * @param User $user
     * @return array
     */
    private function checkAccountStatus(User $user): array { // Méthode privée qui prend un User
        // Compte banni ?
        if ($user->isBanned()) { // Si l'utilisateur est banni
            return [ // Retourne échec avec détails
                'success' => false,
                'message' => 'Votre compte a été suspendu',
                'user' => null,
                'errors' => ['Votre compte a été suspendu. Contactez l\'administration.']
            ];
        }
        
        // Compte non vérifié ?
        if (!$user->isVerified()) { // Si l'utilisateur n'est pas vérifié
            return [ // Retourne échec avec détails
                'success' => false,
                'message' => 'Compte non vérifié',
                'user' => null,
                'errors' => ['Veuillez vérifier votre email avant de vous connecter.']
            ];
        }
        
        return ['success' => true]; // Retourne succès si tout est OK
    }
    
    /**
     * Crée la session utilisateur
     * 
     * @param User $user
     * @param bool $remember
     */
    private function createUserSession(User $user, bool $remember = false): void { // Méthode privée sans retour
        // Créer la session avec toutes les données nécessaires
        SessionManager::setUser([ // Appelle setUser du SessionManager
            'id' => $user->getId(), // ID de l'utilisateur
            'pseudo' => $user->getPseudo(), // Pseudo
            'role' => $user->get('role'), // Rôle (admin, moderateur, etc.)
            'type_utilisateur' => $user->get('type_utilisateur') // Type (avocat, psychologue, etc.)
        ]);
        
        // Si "Se souvenir de moi"
        if ($remember) { // Si la checkbox remember était cochée
            // AFPT (après examen): Implémenter un système de token persistant
            // Pour l'instant, augmentation artificielle de la durée de vie du cookie
            
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     * 
     * @return array
     */
    public function logout(): array { // Méthode publique qui retourne un array
        SessionManager::endSession(); // Appelle endSession pour terminer la session
        
        return [ // Retourne le résultat
            'success' => true,
            'message' => 'Déconnexion réussie',
            'user' => null,
            'errors' => []
        ];
    }
}