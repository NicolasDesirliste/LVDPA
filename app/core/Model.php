<?php
// app/core/Model.php
namespace App\Core;

/**
 * Classe abstraite Model
 * 
 * Responsabilité UNIQUE : Représenter une entité de la base de données
 * - Contient les données
 * - Peut se valider
 * - Peut se convertir en tableau
 */
abstract class Model {
    /**
     * Les données du modèle
     */
    protected array $data = [];
    
    /**
     * Constructeur - initialise avec des données si fournies
     */
    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }
    
    /**
     * Remplit le modèle avec des données
     * 
     * @param array $data
     */
    public function fill(array $data): void {
        $this->data = array_merge($this->data, $data);
    }
    
    /**
     * Récupère une valeur
     * 
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key) {
        return $this->data[$key] ?? null;
    }
    
    /**
     * Définit une valeur
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void {
        $this->data[$key] = $value;
    }
    
    /**
     * Convertit le modèle en tableau
     * 
     * @return array
     */
    public function toArray(): array {
        return $this->data;
    }
}