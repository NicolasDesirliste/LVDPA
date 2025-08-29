<?php
// app/services/UserValidator.php
namespace App\Services; // Namespace dans le dossier Services

use App\Models\User; // Import du modèle User

/**
 * Classe UserValidator
 * 
 * Responsabilité UNIQUE : Valider les données d'un utilisateur
 * - Ne fait QUE de la validation
 * - Retourne les erreurs trouvées
 */
class UserValidator { // Déclaration de la classe
    private array $errors = []; // Propriété privée pour stocker les erreurs de validation
    
    /**
     * Valide un utilisateur
     * 
     * @param User $user
     * @return bool
     */
    public function validate(User $user): bool { // Méthode publique qui prend un User
        $this->errors = []; // Réinitialise le tableau d'erreurs
        
        $this->validatePseudo($user->get('pseudo')); // Valide le pseudo
        $this->validateEmail($user->get('email')); // Valide l'email
        $this->validateDepartement($user->get('departement')); // Valide le département
        $this->validateTypeUtilisateur($user->get('type_utilisateur')); // Valide le type d'utilisateur
        
        // Validation du mot de passe seulement si fourni
        $password = $user->get('mot_de_passe'); // Récupère le mot de passe
        if ($password !== null) { // Si un mot de passe est fourni
            $this->validatePassword($password); // Le valide
        }
        
        return empty($this->errors); // Retourne true si aucune erreur
    }
    
    /**
     * Valide les données pour une création
     * 
     * @param array $data
     * @return bool
     */
    public function validateCreation(array $data): bool { // Méthode publique pour validation de création
        $this->errors = []; // Réinitialise le tableau d'erreurs
        
        $this->validatePseudo($data['pseudo'] ?? null); // Valide le pseudo (null si non fourni)
        $this->validateEmail($data['email'] ?? null); // Valide l'email
        $this->validateDepartement($data['departement'] ?? null); // Valide le département
        $this->validateTypeUtilisateur($data['type_utilisateur'] ?? null); // Valide le type
        $this->validatePassword($data['mot_de_passe'] ?? null); // Valide le mot de passe
        $this->validatePasswordConfirmation( // Valide la confirmation
            $data['mot_de_passe'] ?? null, // Mot de passe
            $data['mot_de_passe_confirmation'] ?? null // Confirmation
        );
        
        return empty($this->errors); // Retourne true si aucune erreur
    }
    
    /**
     * Récupère les erreurs
     * 
     * @return array
     */
    public function getErrors(): array { // Méthode publique qui retourne les erreurs
        return $this->errors; // Retourne le tableau d'erreurs
    }
    
    /**
     * Valide le pseudo
     */
    private function validatePseudo(?string $pseudo): void { // Méthode privée qui prend un string nullable
        if (empty($pseudo)) { // Si pseudo vide ou null
            $this->errors['pseudo'] = "Le pseudo est obligatoire"; // Ajoute l'erreur
        } elseif (strlen($pseudo) < 3) { // Si moins de 3 caractères
            $this->errors['pseudo'] = "Le pseudo doit contenir au moins 3 caractères"; // Ajoute l'erreur
        } elseif (strlen($pseudo) > 50) { // Si plus de 50 caractères
            $this->errors['pseudo'] = "Le pseudo ne peut pas dépasser 50 caractères"; // Ajoute l'erreur
        }
    }
    
    /**
     * Valide l'email
     */
    private function validateEmail(?string $email): void { // Méthode privée qui prend un string nullable
        if (empty($email)) { // Si email vide ou null
            $this->errors['email'] = "L'email est obligatoire"; // Ajoute l'erreur
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Si format email invalide
            $this->errors['email'] = "L'email n'est pas valide"; // Ajoute l'erreur
        }
    }
    
    /**
     * Valide le département
     */
    private function validateDepartement($departement): void { // Méthode privée (type mixed)
        if (empty($departement)) { // Si département vide
            $this->errors['departement'] = "Le département est obligatoire"; // Ajoute l'erreur
        } elseif (!is_numeric($departement) || $departement < 1 || $departement > 99) { // Si pas numérique ou hors limites
            $this->errors['departement'] = "Le département doit être un nombre entre 1 et 99"; // Ajoute l'erreur
        }
    }
    
    /**
     * Valide le type d'utilisateur
     */
    private function validateTypeUtilisateur(?string $type): void { // Méthode privée qui prend un string nullable
        $typesValides = ['particulier', 'avocat', 'psychologue', 'mediateur']; // Liste des types valides
        
        if (empty($type)) { // Si type vide ou null
            $this->errors['type_utilisateur'] = "Le type d'utilisateur est obligatoire"; // Ajoute l'erreur
        } elseif (!in_array($type, $typesValides)) { // Si pas dans la liste des types valides
            $this->errors['type_utilisateur'] = "Le type d'utilisateur n'est pas valide"; // Ajoute l'erreur
        }
    }
    
    /**
     * Valide le mot de passe
     */
    private function validatePassword(?string $password): void { // Méthode privée qui prend un string nullable
        if (empty($password)) { // Si mot de passe vide ou null
            $this->errors['mot_de_passe'] = "Le mot de passe est obligatoire"; // Ajoute l'erreur
        } elseif (strlen($password) < 8) { // Si moins de 8 caractères
            $this->errors['mot_de_passe'] = "Le mot de passe doit contenir au moins 8 caractères"; // Ajoute l'erreur
        }
    }
    
    /**
     * Valide la confirmation du mot de passe
     */
    private function validatePasswordConfirmation(?string $password, ?string $confirmation): void { // Méthode privée avec 2 paramètres
        if ($password !== $confirmation) { // Si les mots de passe ne correspondent pas (comparaison stricte)
            $this->errors['mot_de_passe_confirmation'] = "Les mots de passe ne correspondent pas"; // Ajoute l'erreur
        }
    }
}