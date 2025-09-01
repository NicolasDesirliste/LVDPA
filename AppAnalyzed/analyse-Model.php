<?php
// app/core/Model.php
namespace App\Core; // Namespace dans le dossier Core

/**
 * Classe abstraite Model
 * 
 * Responsabilité UNIQUE : Représenter une entité de la base de données
 * - Contient les données
 * - Peut se valider
 * - Peut se convertir en tableau
 */
abstract class Model { // Classe abstraite (ne peut pas être instanciée directement)
    /**
     * Les données du modèle
     */
    protected array $data = []; // Propriété protégée (accessible aux classes enfants), array vide par défaut
    
    /**
     * Constructeur - initialise avec des données si fournies
     */
    public function __construct(array $data = []) { // Constructeur avec paramètre optionnel
        if (!empty($data)) { // Si des données sont fournies
            $this->fill($data); // Appelle la méthode fill() pour les stocker
        }
    }
    
    /**
     * Remplit le modèle avec des données
     * 
     * @param array $data
     */
    public function fill(array $data): void { // Méthode publique sans retour
        $this->data = array_merge($this->data, $data); // Fusionne les données existantes avec les nouvelles
    }
    
    /**
     * Récupère une valeur
     * 
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key) { // Méthode publique qui prend une clé
        return $this->data[$key] ?? null; // Retourne la valeur ou null si la clé n'existe pas
    }
    
    /**
     * Définit une valeur
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void { // Méthode publique avec clé et valeur (type mixed)
        $this->data[$key] = $value; // Stocke la valeur dans le tableau data
    }
    
    /**
     * Convertit le modèle en tableau
     * 
     * @return array
     */
    public function toArray(): array { // Méthode publique qui retourne un array
        return $this->data; // Retourne le tableau de données complet
    }
}