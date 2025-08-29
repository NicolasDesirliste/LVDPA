<?php
// app/models/User.php
namespace App\Models; // Namespace dans le dossier Models

use App\Core\Model; // Import de la classe Model de base

/**
 * Classe User
 * 
 * Responsabilité UNIQUE : Représenter un utilisateur
 * - Contient les données d'un utilisateur
 * - NE FAIT PAS de validation (c'est le rôle du Validator)
 * - NE FAIT PAS de requêtes SQL (c'est le rôle du Repository)
 */
class User extends Model { // Hérite de Model
    /**
     * Récupère l'ID de l'utilisateur
     * 
     * @return int|null
     */
    public function getId(): ?int { // Méthode publique avec type de retour nullable
        $id = $this->get('id'); // Récupère l'id via la méthode get() héritée
        return $id ? (int)$id : null; // Si id existe, le convertit en int, sinon retourne null
    }
    
    /**
     * Récupère le pseudo
     * 
     * @return string|null
     */
    public function getPseudo(): ?string { // Type de retour string nullable
        return $this->get('pseudo'); // Retourne directement la valeur du pseudo
    }
    
    /**
     * Récupère l'email
     * 
     * @return string|null
     */
    public function getEmail(): ?string { // Type de retour string nullable
        return $this->get('email'); // Retourne directement la valeur de l'email
    }
    
    /**
     * Vérifie si l'utilisateur est vérifié
     * 
     * @return bool
     */
    public function isVerified(): bool { // Retourne un booléen
        return (bool)$this->get('est_verifie'); // Convertit la valeur en booléen avec cast (bool)
    }
    
    /**
     * Vérifie si l'utilisateur est banni
     * 
     * @return bool
     */
    public function isBanned(): bool { // Retourne un booléen
        return (bool)$this->get('est_banni'); // Convertit la valeur en booléen avec cast (bool)
    }
    
    /**
     * Vérifie si l'utilisateur est un professionnel
     * 
     * @return bool
     */
    public function isProfessional(): bool { // Retourne un booléen
        return in_array($this->get('type_utilisateur'), ['avocat', 'psychologue', 'mediateur']); // Vérifie si le type est dans la liste des professionnels
    }
}