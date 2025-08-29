<?php
// app/repositories/UserRepository.php
namespace App\Repositories; // Namespace dans le dossier Repositories

use App\Config\Database; // Import de la classe Database pour la connexion
use App\Models\User; // Import du modèle User
use PDO; // Import de la classe PDO native PHP

/**
 * Classe UserRepository
 * 
 * Responsabilité UNIQUE : Gérer les requêtes SQL pour les utilisateurs
 * - Récupère des utilisateurs depuis la BDD
 * - Sauvegarde des utilisateurs dans la BDD */

class UserRepository { // Déclaration de la classe
    private PDO $db; // Propriété privée pour stocker la connexion PDO
    
    /**
     * Constructeur
     */
    public function __construct() { // Constructeur public
        $this->db = Database::getConnection(); // Récupère la connexion PDO via la méthode statique
    }
    
    /**
     * Trouve un utilisateur par son ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User { // Méthode publique avec type de retour nullable
        $sql = "SELECT * FROM utilisateurs WHERE id = :id"; // Requête SQL avec paramètre nommé
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute(['id' => $id]); // Exécute avec le paramètre
        
        $data = $stmt->fetch(); // Récupère la première ligne
        if (!$data) { // Si aucune donnée trouvée
            return null; // Retourne null
        }
        
        return new User($data); // Crée et retourne un objet User avec les données
    }
    
    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User { // Méthode avec paramètre string
        $sql = "SELECT * FROM utilisateurs WHERE email = :email"; // Requête SQL
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute(['email' => $email]); // Exécute avec l'email
        
        $data = $stmt->fetch(); // Récupère le résultat
        if (!$data) { // Si aucun résultat
            return null; // Retourne null
        }
        
        return new User($data); // Crée et retourne un User
    }
    
    /**
     * Trouve un utilisateur par son pseudo
     * 
     * @param string $pseudo
     * @return User|null
     */
    public function findByPseudo(string $pseudo): ?User { // Méthode avec paramètre string
        $sql = "SELECT * FROM utilisateurs WHERE pseudo = :pseudo"; // Requête SQL
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute(['pseudo' => $pseudo]); // Exécute avec le pseudo
        
        $data = $stmt->fetch(); // Récupère le résultat
        if (!$data) { // Si aucun résultat
            return null; // Retourne null
        }
        
        return new User($data); // Crée et retourne un User
    }
    
    /**
     * Trouve un utilisateur par son token de vérification
     * 
     * @param string $token
     * @return User|null
     */
    public function findByToken(string $token): ?User { // Méthode avec paramètre string
        $sql = "SELECT * FROM utilisateurs WHERE token = :token"; // Requête SQL
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute(['token' => $token]); // Exécute avec le token
        
        $data = $stmt->fetch(); // Récupère le résultat
        if (!$data) { // Si aucun résultat
            return null; // Retourne null
        }
        
        return new User($data); // Crée et retourne un User
    }
    
    /**
     * Trouve tous les professionnels vérifiés d'un département
     * 
     * @param int $departement
     * @param string $type Type de professionnel
     * @return array
     */
    public function findProfessionalsByDepartment(int $departement, string $type): array { // Retourne un array
        $sql = "SELECT * FROM utilisateurs 
                WHERE departement = :departement 
                AND type_utilisateur = :type
                AND est_verifie = 1
                AND est_banni = 0
                ORDER BY derniere_connexion DESC"; // Requête SQL multi-lignes avec conditions
        
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute([ // Exécute avec les paramètres
            'departement' => $departement, // Paramètre département
            'type' => $type // Paramètre type
        ]);
        
        $users = []; // Initialise un array vide
        while ($data = $stmt->fetch()) { // Boucle tant qu'il y a des résultats
            $users[] = new User($data); // Ajoute un nouvel objet User au tableau
        }
        
        return $users; // Retourne le tableau d'utilisateurs
    }
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool { // Prend un User et retourne un booléen
        $sql = "INSERT INTO utilisateurs 
                (pseudo, email, mot_de_passe, departement, type_utilisateur, 
                 role, token, token_expiration, date_inscription)
                VALUES 
                (:pseudo, :email, :mot_de_passe, :departement, :type_utilisateur,
                 :role, :token, :token_expiration, NOW())"; // Requête INSERT avec NOW() pour la date
        
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $result = $stmt->execute([ // Exécute et stocke le résultat
            'pseudo' => $user->get('pseudo'), // Récupère le pseudo du User
            'email' => $user->get('email'), // Récupère l'email
            'mot_de_passe' => $user->get('mot_de_passe'), // Récupère le mot de passe hashé
            'departement' => $user->get('departement'), // Récupère le département
            'type_utilisateur' => $user->get('type_utilisateur'), // Récupère le type
            'role' => $user->get('role') ?? 'utilisateur', // Récupère le rôle ou 'utilisateur' par défaut
            'token' => $user->get('token'), // Récupère le token
            'token_expiration' => $user->get('token_expiration') // Récupère l'expiration du token
        ]);
        
        if ($result) { // Si l'insertion a réussi
            // Récupérer l'ID généré
            $user->set('id', $this->db->lastInsertId()); // Stocke l'ID auto-généré dans l'objet User
        }
        
        return $result; // Retourne true si succès, false sinon
    }
    
    /**
     * Met à jour un utilisateur
     * 
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool { // Prend un User et retourne un booléen
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
                WHERE id = :id"; // Requête UPDATE avec tous les champs
        
        $stmt = $this->db->prepare($sql); // Prépare la requête
        return $stmt->execute([ // Exécute et retourne directement le résultat
            'id' => $user->getId(), // Utilise la méthode getId()
            'pseudo' => $user->get('pseudo'), // Récupère le pseudo
            'email' => $user->get('email'), // Récupère l'email
            'departement' => $user->get('departement'), // Récupère le département
            'type_utilisateur' => $user->get('type_utilisateur'), // Récupère le type
            'role' => $user->get('role'), // Récupère le rôle
            'est_verifie' => $user->get('est_verifie') ?? 0, // Récupère est_verifie ou 0 par défaut
            'est_banni' => $user->get('est_banni') ?? 0, // Récupère est_banni ou 0 par défaut
            'token' => $user->get('token'), // Récupère le token
            'token_expiration' => $user->get('token_expiration'), // Récupère l'expiration
            'derniere_connexion' => $user->get('derniere_connexion') // Récupère la dernière connexion
        ]);
    }
    
    /**
     * Met à jour la dernière connexion
     * 
     * @param int $userId
     * @return bool
     */
    public function updateLastConnection(int $userId): bool { // Prend un ID et retourne un booléen
        $sql = "UPDATE utilisateurs 
                SET derniere_connexion = NOW() 
                WHERE id = :id"; // Requête UPDATE simple avec NOW()
        
        $stmt = $this->db->prepare($sql); // Prépare la requête
        return $stmt->execute(['id' => $userId]); // Exécute et retourne le résultat
    }
    
    /**
     * Vérifie si un email existe déjà
     * 
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool { // Retourne un booléen
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email"; // Compte le nombre de lignes
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute(['email' => $email]); // Exécute avec l'email
        
        return $stmt->fetchColumn() > 0; // Retourne true si le count est supérieur à 0
    }
    
    /**
     * Vérifie si un pseudo existe déjà
     * 
     * @param string $pseudo
     * @return bool
     */
    public function pseudoExists(string $pseudo): bool { // Retourne un booléen
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo"; // Compte le nombre de lignes
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->execute(['pseudo' => $pseudo]); // Exécute avec le pseudo
        
        return $stmt->fetchColumn() > 0; // Retourne true si le count est supérieur à 0
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