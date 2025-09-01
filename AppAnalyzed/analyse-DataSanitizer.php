<?php
// app/services/DataSanitizer.php
namespace App\Services; // Namespace dans le dossier Services

/**
 * Classe DataSanitizer
 * 
 * Responsabilité: Nettoyer les données entrantes
 */
class DataSanitizer { // Déclaration de la classe
    public static function cleanText($value) { // Méthode statique publique pour nettoyer du texte
        return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8'); // trim() enlève les espaces, htmlspecialchars() convertit les caractères spéciaux HTML, ?? '' gère les valeurs null
    }
    
    public static function cleanInt($value) { // Méthode statique pour nettoyer un entier
        return (int)($value ?? 0); // Convertit en entier, 0 si null
    }
    
    public static function cleanFloat($value) { // Méthode statique pour nettoyer un nombre décimal
        return (float)($value ?? 0); // Convertit en float, 0 si null
    }
    
    public static function cleanCheckbox($value) { // Méthode statique pour nettoyer une checkbox
        return isset($value) ? 1 : 0; // Retourne 1 si la variable existe, 0 sinon
    }
    
    public static function cleanTime($value) { // Méthode statique pour nettoyer une valeur temporelle
        return $value ?? ''; // Retourne la valeur ou string vide si null
    }
}