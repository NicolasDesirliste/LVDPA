<?php
// app/services/UserAccountService.php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

/**
 * Classe UserAccountService
 * 
 * Responsabilité: Gérer les opérations sur les comptes utilisateur
 * - Vérification de compte
 * - Suspension/bannissement
 * - Changement de rôle
 * - Gestion du statut des professionnels
 */
class UserAccountService {
    private UserRepository $userRepository;
    private EmailService $emailService;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->emailService = new EmailService();
    }
    
    /**
     * Vérifie un compte avec un token
     * 
     * @param string $token
     * @return array ['success' => bool, 'message' => string]
     */
    public function verifyAccount(string $token): array {
        // Trouver l'utilisateur par son token
        $user = $this->userRepository->findByToken($token);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Token de vérification invalide.'
            ];
        }
        
        // Vérifier que le token n'a pas expiré
        if (strtotime($user->get('token_expiration')) < time()) {
            return [
                'success' => false,
                'message' => 'Le lien de vérification a expiré.'
            ];
        }
        
        // Vérifier que le compte n'est pas déjà vérifié
        if ($user->get('est_verifie')) {
            return [
                'success' => false,
                'message' => 'Ce compte est déjà vérifié.'
            ];
        }
        
        // Activer le compte
        $user->set('est_verifie', 1);
        $user->set('token', null);
        $user->set('token_expiration', null);
        $user->set('premiere_connexion', 1); // Pour rediriger vers le profil
        
        if ($this->userRepository->update($user)) {
            return [
                'success' => true,
                'message' => 'Votre compte a été vérifié avec succès !'
            ];
        }
        
        return [
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
    public function resendVerificationEmail(string $email): array {
        // Trouver l'utilisateur
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Aucun compte associé à cet email.'
            ];
        }
        
        // Vérifier que le compte n'est pas déjà vérifié
        if ($user->get('est_verifie')) {
            return [
                'success' => false,
                'message' => 'Ce compte est déjà vérifié.'
            ];
        }
        
        // Générer un nouveau token
        $user->set('token', bin2hex(random_bytes(32)));
        $user->set('token_expiration', date('Y-m-d H:i:s', strtotime('+24 hours')));
        
        if ($this->userRepository->update($user)) {
            // Envoyer l'email
            $this->emailService->sendVerificationEmail($user);
            
            return [
                'success' => true,
                'message' => 'Un nouvel email de vérification a été envoyé.'
            ];
        }
        
        return [
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
    public function suspendAccount(int $userId, string $reason = ''): array {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $user->set('est_banni', 1);
        
        if ($this->userRepository->update($user)) {
            // AFPT: Logger la raison du bannissement dans une table dédiée
            // AFPT: Envoyer un email de notification de suspension
            
            return [
                'success' => true,
                'message' => 'Le compte a été suspendu.'
            ];
        }
        
        return [
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
    public function unsuspendAccount(int $userId): array {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $user->set('est_banni', 0);
        
        if ($this->userRepository->update($user)) {
            return [
                'success' => true,
                'message' => 'Le compte a été réactivé.'
            ];
        }
        
        return [
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
    public function changeUserRole(int $userId, string $newRole): array {
        $validRoles = ['utilisateur', 'moderateur', 'admin'];
        
        if (!in_array($newRole, $validRoles)) {
            return [
                'success' => false,
                'message' => 'Rôle invalide.'
            ];
        }
        
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $user->set('role', $newRole);
        
        if ($this->userRepository->update($user)) {
            return [
                'success' => true,
                'message' => "Le rôle a été changé en {$newRole}."
            ];
        }
        
        return [
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
    public function verifyProfessional(int $userId): array {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }
        
        $professionalTypes = ['avocat', 'psychologue', 'mediateur'];
        if (!in_array($user->get('type_utilisateur'), $professionalTypes)) {
            return [
                'success' => false,
                'message' => 'Cet utilisateur n\'est pas un professionnel.'
            ];
        }
        
        // Marquer comme vérifié
        $user->set('est_verifie', 1);
        
        if ($this->userRepository->update($user)) {
            // Envoyer un email de confirmation au professionnel
            $this->emailService->sendProfessionalApprovalEmail($user);
            
            return [
                'success' => true,
                'message' => 'Le professionnel a été vérifié.'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Erreur lors de la vérification.'
        ];
    }
}