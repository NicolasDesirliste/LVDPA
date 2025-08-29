<?php
// app/repositories/UserRepository.php
namespace App\Repositories;

use App\Config\Database;
use App\Models\User;
use PDO;

/**
 * Classe UserRepository
 * 
 * Responsabilité UNIQUE : Gérer les requêtes SQL pour les utilisateurs
 * - Récupère des utilisateurs depuis la BDD
 * - Sauvegarde des utilisateurs dans la BDD */

class UserRepository {
    private PDO $db;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Trouve un utilisateur par son ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User {
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }
        
        return new User($data);
    }
    
    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }
        
        return new User($data);
    }
    
    /**
     * Trouve un utilisateur par son pseudo
     * 
     * @param string $pseudo
     * @return User|null
     */
    public function findByPseudo(string $pseudo): ?User {
        $sql = "SELECT * FROM utilisateurs WHERE pseudo = :pseudo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pseudo' => $pseudo]);
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }
        
        return new User($data);
    }
    
    /**
     * Trouve un utilisateur par son token de vérification
     * 
     * @param string $token
     * @return User|null
     */
    public function findByToken(string $token): ?User {
        $sql = "SELECT * FROM utilisateurs WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }
        
        return new User($data);
    }
    
    /**
     * Trouve tous les professionnels vérifiés d'un département
     * 
     * @param int $departement
     * @param string $type Type de professionnel
     * @return array
     */
    public function findProfessionalsByDepartment(int $departement, string $type): array {
        $sql = "SELECT * FROM utilisateurs 
                WHERE departement = :departement 
                AND type_utilisateur = :type
                AND est_verifie = 1
                AND est_banni = 0
                ORDER BY derniere_connexion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'departement' => $departement,
            'type' => $type
        ]);
        
        $users = [];
        while ($data = $stmt->fetch()) {
            $users[] = new User($data);
        }
        
        return $users;
    }
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool {
        $sql = "INSERT INTO utilisateurs 
                (pseudo, email, mot_de_passe, departement, type_utilisateur, 
                 role, token, token_expiration, date_inscription)
                VALUES 
                (:pseudo, :email, :mot_de_passe, :departement, :type_utilisateur,
                 :role, :token, :token_expiration, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'pseudo' => $user->get('pseudo'),
            'email' => $user->get('email'),
            'mot_de_passe' => $user->get('mot_de_passe'),
            'departement' => $user->get('departement'),
            'type_utilisateur' => $user->get('type_utilisateur'),
            'role' => $user->get('role') ?? 'utilisateur',
            'token' => $user->get('token'),
            'token_expiration' => $user->get('token_expiration')
        ]);
        
        if ($result) {
            // Récupérer l'ID généré
            $user->set('id', $this->db->lastInsertId());
        }
        
        return $result;
    }
    
    /**
     * Met à jour un utilisateur
     * 
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool {
        $sql = "UPDATE utilisateurs 
                SET pseudo = :pseudo,
                    email = :email,
                    departement = :departement,
                    type_utilisateur = :type_utilisateur,
                    role = :role,
                    est_verifie = :est_verifie,
                    est_banni = :est_banni,
                    token = :token,
                    token_expiration = :token_expiration,
                    derniere_connexion = :derniere_connexion
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $user->getId(),
            'pseudo' => $user->get('pseudo'),
            'email' => $user->get('email'),
            'departement' => $user->get('departement'),
            'type_utilisateur' => $user->get('type_utilisateur'),
            'role' => $user->get('role'),
            'est_verifie' => $user->get('est_verifie') ?? 0,
            'est_banni' => $user->get('est_banni') ?? 0,
            'token' => $user->get('token'),
            'token_expiration' => $user->get('token_expiration'),
            'derniere_connexion' => $user->get('derniere_connexion')
        ]);
    }
    
    /**
     * Met à jour la dernière connexion
     * 
     * @param int $userId
     * @return bool
     */
    public function updateLastConnection(int $userId): bool {
        $sql = "UPDATE utilisateurs 
                SET derniere_connexion = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
    
    /**
     * Vérifie si un email existe déjà
     * 
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Vérifie si un pseudo existe déjà
     * 
     * @param string $pseudo
     * @return bool
     */
    public function pseudoExists(string $pseudo): bool {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pseudo' => $pseudo]);
        
        return $stmt->fetchColumn() > 0;
    }
}

/*
Considérations actuelles et à implémenter plus tard: 

-> Recherche par département:
public function findByDepartment(int $departement): array

-> Recherche des utilisateurs bannis: 
public function findBannedUsers(): array

-> Recherche avec pagination: 
public function findPaginated(int $page, int $perPage): array

-> Recherche par date d'inscription (si nécessaire): 
public function findRegisteredBetween($dateStart, $dateEnd): array

-> Statistiques:
public function countByType(): array

// Recherche complexe
public function searchUsers(array $criteria): array


Autres considérations (future lointain..): 

-> Créer des repositories spécialisés (UserSearchRepository, UserStatsRepository)
-> Utiliser des Query Objects pour les requêtes complexes
*/