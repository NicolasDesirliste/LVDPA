<?php
// app/services/UserAccountService.php
namespace App\Services; // Namespace dans le dossier Services

use App\Repositories\UserRepository; // Import du repository
use App\Models\User; // Import du modèle User

/**
 * Classe UserAccountService
 * 
 * Responsabilité: Gérer les opérations sur les comptes utilisateur
 * - Vérification de compte
 * - Suspension/bannissement
 * - Changement de rôle
 * - Gestion du statut des professionnels
 */
class UserAccountService { // Déclaration de la classe
    private UserRepository $userRepository; // Propriété privée pour le repository
    private EmailService $emailService; // Propriété privée pour le service email
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        $this->userRepository = new UserRepository(); // Instancie le repository
        $this->emailService = new EmailService(); // Instancie le service email
    }
    
    /**
     * Vérifie un compte avec un token
     * 
     * @param string $token
     * @return array ['success' => bool, 'message' => string]
     */
    public function verifyAccount(string $token): array { // Prend un token et retourne un array
        // Trouver l'utilisateur par son token
        $user = $this->userRepository->findByToken($token); // Cherche l'utilisateur avec le token
        
        if (!$user) { // Si aucun utilisateur trouvé
            return [ // Retourne un array d'échec
                'success' => false, // Échec
                'message' => 'Token de vérification invalide.' // Message d'erreur
            ];
        }
        
        // Vérifier que le token n'a pas expiré
        if (strtotime($user->get('token_expiration')) < time()) { // Compare la date d'expiration avec maintenant
            return [ // Retourne échec si expiré
                'success' => false,
                'message' => 'Le lien de vérification a expiré.'
            ];
        }
        
        // Vérifier que le compte n'est pas déjà vérifié
        if ($user->get('est_verifie')) { // Si déjà vérifié
            return [ // Retourne échec
                'success' => false,
                'message' => 'Ce compte est déjà vérifié.'
            ];
        }
        
        // Activer le compte
        $user->set('est_verifie', 1); // Met est_verifie à 1
        $user->set('token', null); // Supprime le token
        $user->set('token_expiration', null); // Supprime l'expiration
        $user->set('premiere_connexion', 1); // Marque comme première connexion
        
        if ($this->userRepository->update($user)) { // Si la mise à jour réussit
            return [ // Retourne succès
                'success' => true,
                'message' => 'Votre compte a été vérifié avec succès !'
            ];
        }
        
        return [ // Si échec de mise à jour
            'success' => false,
            'message' => 'Une erreur est survenue lors de la vérification.'
        ];
    }
    
    /**
     * Renvoie un email de vérification
     * 
     * @param string $email
     * @return array ['success' => bool, 'message' => string]
     */
    public function resendVerificationEmail(string $email): array { // Prend un email
        // Trouver l'utilisateur
        $user = $this->userRepository->findByEmail($email); // Cherche par email
        
        if (!$user) { // Si pas trouvé
            return [ // Retourne échec
                'success' => false,
                'message' => 'Aucun compte associé à cet email.'
            ];
        }
        
        // Vérifier que le compte n'est pas déjà vérifié
        if ($user->get('est_verifie')) { // Si déjà vérifié
            return [ // Retourne échec
                'success' => false,
                'message' => 'Ce compte est déjà vérifié.'
            ];
        }
        
        // Générer un nouveau token
        $user->set('token', bin2hex(random_bytes(32))); // Génère 32 octets aléatoires et convertit en hex
        $user->set('token_expiration', date('Y-m-d H:i:s', strtotime('+24 hours'))); // Expire dans 24h
        
        if ($this->userRepository->update($user)) { // Si mise à jour réussie
            // Envoyer l'email
            $this->emailService->sendVerificationEmail($user); // Envoie l'email
            
            return [ // Retourne succès
                'success' => true,
                'message' => 'Un nouvel email de vérification a été envoyé.'
            ];
        }
        
        return [ // Si échec
            'success' => false,
            'message' => 'Une erreur est survenue.'
        ];
    }
    
    /**
     * Suspend/bannit un compte
     * 
     * @param int $userId
     * @param string $reason Raison du bannissement
     * @return array ['success' => bool, 'message' => string]
     */
    public function suspendAccount(int $userId, string $reason = ''): array { // Prend ID et raison optionnelle
        $user = $this->userRepository->findById($userId); // Cherche l'utilisateur
        
        if (!$user) { // Si pas trouvé
            return [ // Retourne échec
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $user->set('est_banni', 1); // Met est_banni à 1
        
        if ($this->userRepository->update($user)) { // Si mise à jour réussie
            // AFPT: Logger la raison du bannissement dans une table dédiée
            // AFPT: Envoyer un email de notification de suspension
            
            return [ // Retourne succès
                'success' => true,
                'message' => 'Le compte a été suspendu.'
            ];
        }
        
        return [ // Si échec
            'success' => false,
            'message' => 'Erreur lors de la suspension du compte.'
        ];
    }
    
    /**
     * Réactive un compte suspendu
     * 
     * @param int $userId
     * @return array ['success' => bool, 'message' => string]
     */
    public function unsuspendAccount(int $userId): array { // Prend un ID utilisateur
        $user = $this->userRepository->findById($userId); // Cherche l'utilisateur
        
        if (!$user) { // Si pas trouvé
            return [ // Retourne échec
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $user->set('est_banni', 0); // Met est_banni à 0
        
        if ($this->userRepository->update($user)) { // Si mise à jour réussie
            return [ // Retourne succès
                'success' => true,
                'message' => 'Le compte a été réactivé.'
            ];
        }
        
        return [ // Si échec
            'success' => false,
            'message' => 'Erreur lors de la réactivation du compte.'
        ];
    }
    
    /**
     * Change le rôle d'un utilisateur
     * 
     * @param int $userId
     * @param string $newRole
     * @return array ['success' => bool, 'message' => string]
     */
    public function changeUserRole(int $userId, string $newRole): array { // Prend ID et nouveau rôle
        $validRoles = ['utilisateur', 'moderateur', 'admin']; // Liste des rôles valides
        
        if (!in_array($newRole, $validRoles)) { // Si le rôle n'est pas dans la liste
            return [ // Retourne échec
                'success' => false,
                'message' => 'Rôle invalide.'
            ];
        }
        
        $user = $this->userRepository->findById($userId); // Cherche l'utilisateur
        
        if (!$user) { // Si pas trouvé
            return [ // Retourne échec
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $user->set('role', $newRole); // Change le rôle
        
        if ($this->userRepository->update($user)) { // Si mise à jour réussie
            return [ // Retourne succès
                'success' => true,
                'message' => "Le rôle a été changé en {$newRole}." // Message avec le nouveau rôle
            ];
        }
        
        return [ // Si échec
            'success' => false,
            'message' => 'Erreur lors du changement de rôle.'
        ];
    }
    
    /**
     * Vérifie un professionnel (avocat, psychologue, médiateur)
     * 
     * @param int $userId
     * @return array ['success' => bool, 'message' => string]
     */
    public function verifyProfessional(int $userId): array { // Prend un ID utilisateur
        $user = $this->userRepository->findById($userId); // Cherche l'utilisateur
        
        if (!$user) { // Si pas trouvé
            return [ // Retourne échec
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $professionalTypes = ['avocat', 'psychologue', 'mediateur']; // Liste des types professionnels
        if (!in_array($user->get('type_utilisateur'), $professionalTypes)) { // Si pas un professionnel
            return [ // Retourne échec
                'success' => false,
                'message' => 'Cet utilisateur n\'est pas un professionnel.'
            ];
        }
        
        // Marquer comme vérifié
        $user->set('est_verifie', 1); // Met est_verifie à 1
        
        if ($this->userRepository->update($user)) { // Si mise à jour réussie
            // Envoyer un email de confirmation au professionnel
            $this->emailService->sendProfessionalApprovalEmail($user); // Envoie l'email
            
            return [ // Retourne succès
                'success' => true,
                'message' => 'Le professionnel a été vérifié.'
            ];
        }
        
        return [ // Si échec
            'success' => false,
            'message' => 'Erreur lors de la vérification.'
        ];
    }
}