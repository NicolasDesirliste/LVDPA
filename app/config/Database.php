<?php
// app/config/Database.php
namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe Database
 * 
 * Responsabilité UNIQUE : Fournir une connexion à la base de données
 * Utilise le pattern Singleton pour éviter les connexions multiples
 */
class Database {
    private static ?PDO $connection = null;
    
    /**
     * Constructeur privé pour empêcher l'instanciation
     */
    private function __construct() {}
    
    /**
     * Obtenir la connexion à la base de données
     * 
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    'mysql:host=localhost;dbname=lvdpa;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion : ' . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}