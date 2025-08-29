<?php
// app/services/UserValidator.php
namespace App\Services;

use App\Models\User;

/**
 * Classe UserValidator
 * 
 * Responsabilité UNIQUE : Valider les données d'un utilisateur
 * - Ne fait QUE de la validation
 * - Retourne les erreurs trouvées
 */
class UserValidator {
    private array $errors = [];
    
    /**
     * Valide un utilisateur
     * 
     * @param User $user
     * @return bool
     */
    public function validate(User $user): bool {
        $this->errors = [];
        
        $this->validatePseudo($user->get('pseudo'));
        $this->validateEmail($user->get('email'));
        $this->validateDepartement($user->get('departement'));
        $this->validateTypeUtilisateur($user->get('type_utilisateur'));
        
        // Validation du mot de passe seulement si fourni
        $password = $user->get('mot_de_passe');
        if ($password !== null) {
            $this->validatePassword($password);
        }
        
        return empty($this->errors);
    }
    
    /**
     * Valide les données pour une création
     * 
     * @param array $data
     * @return bool
     */
    public function validateCreation(array $data): bool {
        $this->errors = [];
        
        $this->validatePseudo($data['pseudo'] ?? null);
        $this->validateEmail($data['email'] ?? null);
        $this->validateDepartement($data['departement'] ?? null);
        $this->validateTypeUtilisateur($data['type_utilisateur'] ?? null);
        $this->validatePassword($data['mot_de_passe'] ?? null);
        $this->validatePasswordConfirmation(
            $data['mot_de_passe'] ?? null,
            $data['mot_de_passe_confirmation'] ?? null
        );
        
        return empty($this->errors);
    }
    
    /**
     * Récupère les erreurs
     * 
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Valide le pseudo
     */
    private function validatePseudo(?string $pseudo): void {
        if (empty($pseudo)) {
            $this->errors['pseudo'] = "Le pseudo est obligatoire";
        } elseif (strlen($pseudo) < 3) {
            $this->errors['pseudo'] = "Le pseudo doit contenir au moins 3 caractères";
        } elseif (strlen($pseudo) > 50) {
            $this->errors['pseudo'] = "Le pseudo ne peut pas dépasser 50 caractères";
        }
    }
    
    /**
     * Valide l'email
     */
    private function validateEmail(?string $email): void {
        if (empty($email)) {
            $this->errors['email'] = "L'email est obligatoire";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "L'email n'est pas valide";
        }
    }
    
    /**
     * Valide le département
     */
    private function validateDepartement($departement): void {
        if (empty($departement)) {
            $this->errors['departement'] = "Le département est obligatoire";
        } elseif (!is_numeric($departement) || $departement < 1 || $departement > 99) {
            $this->errors['departement'] = "Le département doit être un nombre entre 1 et 99";
        }
    }
    
    /**
     * Valide le type d'utilisateur
     */
    private function validateTypeUtilisateur(?string $type): void {
        $typesValides = ['particulier', 'avocat', 'psychologue', 'mediateur'];
        
        if (empty($type)) {
            $this->errors['type_utilisateur'] = "Le type d'utilisateur est obligatoire";
        } elseif (!in_array($type, $typesValides)) {
            $this->errors['type_utilisateur'] = "Le type d'utilisateur n'est pas valide";
        }
    }
    
    /**
     * Valide le mot de passe
     */
    private function validatePassword(?string $password): void {
        if (empty($password)) {
            $this->errors['mot_de_passe'] = "Le mot de passe est obligatoire";
        } elseif (strlen($password) < 8) {
            $this->errors['mot_de_passe'] = "Le mot de passe doit contenir au moins 8 caractères";
        }
    }
    
    /**
     * Valide la confirmation du mot de passe
     */
    private function validatePasswordConfirmation(?string $password, ?string $confirmation): void {
        if ($password !== $confirmation) {
            $this->errors['mot_de_passe_confirmation'] = "Les mots de passe ne correspondent pas";
        }
    }
}