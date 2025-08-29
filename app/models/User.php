<?php
// app/models/User.php
namespace App\Models;

use App\Core\Model;

/**
 * Classe User
 * 
 * Responsabilité UNIQUE : Représenter un utilisateur
 * - Contient les données d'un utilisateur
 * - NE FAIT PAS de validation (c'est le rôle du Validator)
 * - NE FAIT PAS de requêtes SQL (c'est le rôle du Repository)
 */
class User extends Model {
    /**
     * Récupère l'ID de l'utilisateur
     * 
     * @return int|null
     */
    public function getId(): ?int {
        $id = $this->get('id');
        return $id ? (int)$id : null;
    }
    
    /**
     * Récupère le pseudo
     * 
     * @return string|null
     */
    public function getPseudo(): ?string {
        return $this->get('pseudo');
    }
    
    /**
     * Récupère l'email
     * 
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->get('email');
    }
    
    /**
     * Vérifie si l'utilisateur est vérifié
     * 
     * @return bool
     */
    public function isVerified(): bool {
        return (bool)$this->get('est_verifie');
    }
    
    /**
     * Vérifie si l'utilisateur est banni
     * 
     * @return bool
     */
    public function isBanned(): bool {
        return (bool)$this->get('est_banni');
    }
    
    /**
     * Vérifie si l'utilisateur est un professionnel
     * 
     * @return bool
     */
    public function isProfessional(): bool {
        return in_array($this->get('type_utilisateur'), ['avocat', 'psychologue', 'mediateur']);
    }
}